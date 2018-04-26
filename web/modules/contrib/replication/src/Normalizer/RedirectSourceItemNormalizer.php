<?php

namespace Drupal\replication\Normalizer;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Url;
use Drupal\multiversion\Entity\WorkspaceInterface;
use Drupal\serialization\Normalizer\FieldItemNormalizer;

class RedirectSourceItemNormalizer extends FieldItemNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\redirect\Plugin\Field\FieldType\RedirectSourceItem';

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface|null
   */
  private $selectionManager;

  /**
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  private $aliasManager;

  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   * @param \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface|null $selection_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AliasManagerInterface $alias_manager, SelectionPluginManagerInterface $selection_manager = NULL) {
    $this->entityTypeManager = $entity_type_manager;
    $this->selectionManager = $selection_manager;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = [];
    foreach ($object->getProperties(TRUE) as $name => $field) {
      $attributes[$name] = $this->serializer->normalize($field, $format, $context);
    }

    // Use the entity UUID instead of ID in urls like node/1.
    if (isset($attributes['path'])) {
      $path = parse_url($attributes['path'], PHP_URL_PATH);
      // This service is not injected to avoid circular reference error when
      // installing page_manager contrib module.
      $url = \Drupal::service('path.validator')->getUrlIfValidWithoutAccessCheck($path);
      if ($url instanceof Url) {
        $internal_path = ltrim($url->getInternalPath(), '/');
        $path = ltrim($path, '/');
        // Return attributes as they are if uri is an alias.
        if ($path != $internal_path) {
          return $attributes;
        }
        $route_name = $url->getRouteName();
        $route_name_parts = explode('.', $route_name);
        if ($route_name_parts[0] === 'entity' && $this->isMultiversionableEntityType($route_name_parts[1])) {
          $entity_type = $route_name_parts[1];
          $entity_id = $url->getRouteParameters()[$entity_type];
        }
        else {
          return $attributes;
        }
      }
      else {
        return $attributes;
      }
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
      if ($entity instanceof EntityInterface) {
        $entity_uuid = $entity->uuid();
        $attributes['path'] = str_replace($entity_id, $entity_uuid, $attributes['path']);
        $attributes['_entity_uuid'] = $entity_uuid;
        $attributes['_entity_type'] = $entity_type;
        $bundle_key = $entity->getEntityType()->getKey('bundle');
        $bundle = $entity->bundle();
        if ($bundle_key && $bundle) {
          $attributes[$bundle_key] = $bundle;
        }
      }
    }
    return $attributes;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = []) {
    if (isset($data['path'])) {
      if (!isset($data['_entity_uuid']) || !isset($data['_entity_type'])) {
        return parent::denormalize($data, $class, $format, $context);
      }
      $entity_uuid = $data['_entity_uuid'];
      $entity_type = $data['_entity_type'];
      $entity = NULL;
      if (isset($context['workspace']) && ($context['workspace'] instanceof WorkspaceInterface)) {
        $entities = $this->entityTypeManager
          ->getStorage($entity_type)
          ->useWorkspace($context['workspace']->id())
          ->loadByProperties(['uuid' => $entity_uuid]
        );
        $entity = reset($entities);
      }
      if (!($entity instanceof ContentEntityInterface)) {
        $bundle_key = $this->entityTypeManager->getStorage($entity_type)->getEntityType()->getKey('bundle');
        if (isset($data[$bundle_key])) {
          /** @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface $selection_instance */
          $selection_instance = $this->selectionManager->getInstance(['target_type' => $entity_type]);
          // We use a temporary label and entity owner ID as this will be
          // backfilled later anyhow, when the real entity comes around.
          $entity = $selection_instance->createNewEntity($entity_type, $data[$bundle_key], rand(), 1);
          // Set the target workspace if we have it in context.
          if (isset($context['workspace'])
            && ($context['workspace'] instanceof WorkspaceInterface)
            && $entity->getEntityType()->get('workspace') !== FALSE) {
            $entity->workspace->target_id = $context['workspace']->id();
          }
          // Set the UUID to what we received to ensure it gets updated when
          // the full entity comes around later.
          $entity->uuid->value = $entity_uuid;
          // Indicate that this revision is a stub.
          $entity->_rev->is_stub = TRUE;
          $entity->save();
        }
      }
      if ($entity instanceof EntityInterface) {
        $data['path'] = str_replace($entity_uuid, $entity->id(), $data['path']);
      }
    }

    return parent::denormalize($data, $class, $format, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDenormalization($data, $type, $format = NULL) {
    if (in_array($type, ['Drupal\redirect\Plugin\Field\FieldType\RedirectSourceItem'])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @param string $entity_type_id
   *
   * @return bool
   */
  protected function isMultiversionableEntityType($entity_type_id) {
    try {
      $storage = $this->entityTypeManager->getStorage($entity_type_id);
    }
    catch (InvalidPluginDefinitionException $exception) {
      return FALSE;
    }
    $entity_type = $storage->getEntityType();
    if (is_subclass_of($entity_type->getStorageClass(), 'Drupal\multiversion\Entity\Storage\ContentEntityStorageInterface')) {
      return TRUE;
    }
    return FALSE;
  }

}
