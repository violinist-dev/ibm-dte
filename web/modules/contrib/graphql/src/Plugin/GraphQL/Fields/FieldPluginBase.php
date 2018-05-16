<?php

namespace Drupal\graphql\Plugin\GraphQL\Fields;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\GraphQL\ValueWrapperInterface;
use Drupal\graphql\Plugin\FieldPluginInterface;
use Drupal\graphql\Plugin\FieldPluginManager;
use Drupal\graphql\Plugin\GraphQL\Traits\ArgumentAwarePluginTrait;
use Drupal\graphql\Plugin\GraphQL\Traits\CacheablePluginTrait;
use Drupal\graphql\Plugin\GraphQL\Traits\DeprecatablePluginTrait;
use Drupal\graphql\Plugin\GraphQL\Traits\DescribablePluginTrait;
use Drupal\graphql\Plugin\GraphQL\Traits\TypedPluginTrait;
use Drupal\graphql\Plugin\LanguageNegotiation\LanguageNegotiationGraphQL;
use Drupal\graphql\Plugin\SchemaBuilderInterface;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ResolveInfo;

abstract class FieldPluginBase extends PluginBase implements FieldPluginInterface {
  use CacheablePluginTrait;
  use DescribablePluginTrait;
  use TypedPluginTrait;
  use ArgumentAwarePluginTrait;
  use DeprecatablePluginTrait;

  /**
   * The language context service.
   *
   * @var \Drupal\graphql\GraphQLLanguageContext
   */
  protected $languageContext;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(SchemaBuilderInterface $builder, FieldPluginManager $manager, $definition, $id) {
    return [
      'description' => $definition['description'],
      'contexts' => $definition['contexts'],
      'deprecationReason' => $definition['deprecationReason'],
      'type' => $builder->processType($definition['type']),
      'args' => $builder->processArguments($definition['args']),
      'resolve' => function ($value, array $args, ResolveContext $context, ResolveInfo $info) use ($manager, $id) {
        $instance = $manager->getInstance(['id' => $id]);
        return $instance->resolve($value, $args, $context, $info);
      },
    ];
  }

  /**
   * Get the language context instance.
   *
   * @return \Drupal\graphql\GraphQLLanguageContext
   *   The language context service.
   */
  protected function getLanguageContext() {
    if (!isset($this->languageContext)) {
      $this->languageContext = \Drupal::service('graphql.language_context');
    }
    return $this->languageContext;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition() {
    $definition = $this->getPluginDefinition();

    return [
      'type' => $this->buildType($definition),
      'description' => $this->buildDescription($definition),
      'args' => $this->buildArguments($definition),
      'deprecationReason' => $this->buildDeprecationReason($definition),
      'contexts' => $this->buildCacheContexts($definition),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function resolve($value, array $args, ResolveContext $context, ResolveInfo $info) {
    $definition = $this->getPluginDefinition();

    // If not resolving in a trusted environment, check if the field is secure.
    if (!$context->getGlobal('development', FALSE) && !$context->getGlobal('bypass field security', FALSE)) {
      if (empty($definition['secure'])) {
        throw new \Exception(sprintf("Unable to resolve insecure field '%s'.", $info->fieldName));
      }
    }

    foreach ($definition['contextual_arguments'] as $argument) {
      if (array_key_exists($argument, $args) && !is_null($args[$argument])) {
        $context->setContext($argument, $args[$argument], $info);
      }
      else {
        $args[$argument] = $context->getContext($argument, $info);
      }
    }

    if ($this->isLanguageAwareField()) {
      return $this->getLanguageContext()
        ->executeInLanguageContext(function () use ($value, $args, $context, $info) {
          return $this->resolveDeferred([$this, 'resolveValues'], $value, $args, $context, $info);
        }, $context->getContext('language', $info));
    }
    else {
      return $this->resolveDeferred([$this, 'resolveValues'], $value, $args, $context, $info);
    }
  }

  /**
   * Indicator if the field is language aware.
   *
   * Checks for 'languages:*' cache contexts on the fields definition.
   *
   * @return bool
   *   The fields language awareness status.
   */
  protected function isLanguageAwareField() {
    return (boolean) count(array_filter($this->getPluginDefinition()['response_cache_contexts'], function ($context) {
      return strpos($context, 'languages:') === 0;
    }));
  }

  /**
   * {@inheritdoc}
   */
  protected function resolveDeferred(callable $callback, $value, array $args, ResolveContext $context, ResolveInfo $info) {
    $result = $callback($value, $args, $context, $info);

    if (is_callable($result)) {
      return new Deferred(function () use ($result, $value, $args, $context, $info) {
        return $this->resolveDeferred($result, $value, $args, $context, $info);
      });
    }

    $result = iterator_to_array($result);

    // Only collect cache metadata if this is a query. All other operation types
    // are not cacheable anyways.
    if ($info->operation->operation === 'query') {
      $dependencies = $this->getCacheDependencies($result, $value, $args, $context, $info);
      foreach ($dependencies as $dependency) {
        $context->addCacheableDependency($dependency);
      }
    }

    return $this->unwrapResult($result, $info);
  }

  /**
   * Unwrap the resolved values.
   *
   * @param array $result
   *   The resolved values.
   * @param \GraphQL\Type\Definition\ResolveInfo $info
   *   The resolve info object.
   *
   * @return mixed
   *   The extracted values (an array of values in case this is a list, an
   *   arbitrary value if it isn't).
   */
  protected function unwrapResult($result, ResolveInfo $info) {
    $result = array_map(function ($item) {
      return $item instanceof ValueWrapperInterface ? $item->getValue() : $item;
    }, $result);

    $result = array_map(function ($item) {
      return $item instanceof MarkupInterface ? $item->__toString() : $item;
    }, $result);

    // If this is a list, return the result as an array.
    $type = $info->returnType;
    if ($type instanceof ListOfType || ($type instanceof NonNull && $type->getWrappedType() instanceof ListOfType)) {
      return $result;
    }

    return !empty($result) ? reset($result) : NULL;
  }

  /**
   * Retrieve the list of cache dependencies for a given value and arguments.
   *
   * @param array $result
   *   The result of the field.
   * @param mixed $parent
   *   The parent value.
   * @param array $args
   *   The arguments passed to the field.
   * @param \Drupal\graphql\GraphQL\Execution\ResolveContext $context
   *   The resolve context.
   * @param \GraphQL\Type\Definition\ResolveInfo $info
   *   The resolve info object.
   *
   * @return array
   *   A list of cacheable dependencies.
   */
  protected function getCacheDependencies(array $result, $parent, array $args, ResolveContext $context, ResolveInfo $info) {
    $self = new CacheableMetadata();
    $definition = $this->getPluginDefinition();
    if (!empty($definition['response_cache_contexts'])) {
      $self->addCacheContexts($definition['response_cache_contexts']);
    }

    if (!empty($definition['response_cache_tags'])) {
      $self->addCacheTags($definition['response_cache_tags']);
    }

    if (isset($definition['response_cache_max_age'])) {
      $self->mergeCacheMaxAge($definition['response_cache_max_age']);
    }

    return array_merge([$self], array_filter($result, function ($item) {
      return $item instanceof CacheableDependencyInterface;
    }));
  }

  /**
   * Retrieve the list of field values.
   *
   * Always returns a list of field values. Even for single value fields.
   * Single/multi field handling is responsibility of the base class.
   *
   * @param mixed $value
   *   The current object value.
   * @param array $args
   *   Field arguments.
   * @param $context
   *   The resolve context.
   * @param \GraphQL\Type\Definition\ResolveInfo $info
   *   The resolve info object.
   *
   * @return \Generator
   *   The value generator.
   */
  protected function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    // Allow overriding this class without having to declare this method.
    yield NULL;
  }

}
