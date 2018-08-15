<?php

namespace Drupal\jsonapi\Normalizer\Value;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\jsonapi\JsonApiSpec;
use Drupal\jsonapi\Normalizer\CacheableDependencyTrait;

/**
 * Helps normalize entities in compliance with the JSON API spec.
 *
 * @internal
 */
class EntityNormalizerValue implements ValueExtractorInterface, CacheableDependencyInterface {

  use CacheableDependencyTrait;
  use CacheableDependenciesMergerTrait;

  /**
   * The values.
   *
   * @var array
   */
  protected $values;

  /**
   * The includes.
   *
   * @var array
   */
  protected $includes;

  /**
   * The resource path.
   *
   * @var array
   */
  protected $context;

  /**
   * The resource entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The link manager.
   *
   * @var \Drupal\jsonapi\LinkManager\LinkManager
   */
  protected $linkManager;

  /**
   * Instantiate a EntityNormalizerValue object.
   *
   * @param FieldNormalizerValueInterface[] $values
   *   The normalized result.
   * @param array $context
   *   The context for the normalizer.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param array $link_context
   *   All the objects and variables needed to generate the links for this
   *   relationship.
   */
  public function __construct(array $values, array $context, EntityInterface $entity, array $link_context) {
    $this->setCacheability(static::mergeCacheableDependencies(array_merge([$entity], $values)));

    // Gather includes from all normalizer values, before filtering away null
    // and include-only normalizer values.
    $this->includes = array_map(function ($value) {
      return $value->getIncludes();
    }, $values);

    $this->values = array_filter($values, function ($value) {
      return !($value instanceof NullFieldNormalizerValue || $value instanceof IncludeOnlyRelationshipNormalizerValue);
    });
    $this->context = $context;
    $this->entity = $entity;
    $this->linkManager = $link_context['link_manager'];
    // Flatten the includes.
    $this->includes = array_reduce($this->includes, function ($carry, $includes) {
      return array_merge($carry, $includes);
    }, []);
    // Filter the empty values.
    $this->includes = array_filter($this->includes);
  }

  /**
   * {@inheritdoc}
   */
  public function rasterizeValue() {
    // Create the array of normalized fields, starting with the URI.
    /** @var \Drupal\jsonapi\ResourceType\ResourceType $resource_type */
    $resource_type = $this->context['resource_type'];
    $rasterized = [
      'type' => $resource_type->getTypeName(),
      'id' => $this->entity->uuid(),
      'attributes' => [],
      'relationships' => [],
    ];
    $rasterized['links'] = [
      'self' => $this->linkManager->getEntityLink(
        $rasterized['id'],
        $resource_type,
        [],
        'individual'
      ),
    ];
    // Add the revision links only if they may sense.
    if ($resource_type->isVersionable() && $this->entity instanceof RevisionableInterface) {
      $rasterized['links'] += [
        'working-copy' => $this->linkManager->getEntityLink(
          $rasterized['id'],
          $resource_type,
          [],
          'individual',
          [JsonApiSpec::VERSION_QUERY_PARAMETER => 'rel:working-copy']
        ),
        'latest-version' => $this->linkManager->getEntityLink(
          $rasterized['id'],
          $resource_type,
          [],
          'individual',
          [JsonApiSpec::VERSION_QUERY_PARAMETER => 'rel:latest-version']
        ),
      ];
    }

    foreach ($this->getValues() as $field_name => $normalizer_value) {
      $rasterized[$normalizer_value->getPropertyType()][$field_name] = $normalizer_value->rasterizeValue();
    }
    return array_filter($rasterized);
  }

  /**
   * {@inheritdoc}
   */
  public function rasterizeIncludes() {
    // First gather all the includes in the chain.
    return array_map(function ($include) {
      return $include->rasterizeValue();
    }, $this->getIncludes());
  }

  /**
   * Gets the values.
   *
   * @return mixed
   *   The values.
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * Gets a flattened list of includes in all the chain.
   *
   * @return \Drupal\jsonapi\Normalizer\Value\EntityNormalizerValue[]
   *   The array of included relationships.
   */
  public function getIncludes() {
    $nested_includes = array_map(function ($include) {
      return $include->getIncludes();
    }, $this->includes);
    return array_reduce(array_filter($nested_includes), function ($carry, $item) {
      return array_merge($carry, $item);
    }, $this->includes);
  }

}
