<?php

namespace Drupal\jsonapi\Plugin\RevisionIdNegotiation;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\RevisionableStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\jsonapi\Revisions\RevisionIdNegotiationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Revision id implementation for the current or latest revisions.
 *
 * @RevisionIdNegotiation(
 *   id = "rel",
 * )
 */
class RelNegotiation extends PluginBase implements ContainerFactoryPluginInterface, RevisionIdNegotiationInterface {

  /**
   * The current revision id value.
   *
   * @var string
   */
  const CURRENT = 'working-copy';

  /**
   * The latest revision id value.
   *
   * @var string
   */
  const LATEST = 'latest-version';

  /**
   * The entity type manager to load the revision.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entity_type_manager
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionId(EntityInterface $entity, $input_data) {
    $revision_id = NULL;
    if (!($entity instanceof RevisionableInterface)) {
      throw new \InvalidArgumentException('Revision requested on a non revisionable entity ' . $entity->uuid());
    }
    switch ($input_data) {
      case static::CURRENT:
        $revision_id = $entity->getLoadedRevisionId();
        break;

      case static::LATEST:
        try {
          $storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId());
          if ($storage instanceof RevisionableStorageInterface) {
            $revision_id = $storage->getLatestRevisionId($entity->id());
          }
        }
        catch (PluginException $e) {
          // Cast the exception type to an \InvalidArgumentException.
          throw new \InvalidArgumentException($e->getMessage(), $e);
        }
        break;
    }
    if (empty($revision_id)) {
      throw new \InvalidArgumentException('Invalid revision id value for entity ' . $entity->uuid());
    }
    return $revision_id;
  }

}
