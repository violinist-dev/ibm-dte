<?php

namespace Drupal\jsonapi\Plugin\RevisionId;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\jsonapi\Revisions\RevisionIdInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a revision id implementation for the core current or latest revsion id values.
 *
 * @RevisionId(
 *   id = "current_latest_revision_id",
 *   title = @Translation("Current or Latest Revision ID Value"),
 *   description = @Translation("Handles current or latest revision id values."),
 * )
 */
class CurrentLatestRevisionId extends PluginBase implements PluginInspectionInterface, ContainerFactoryPluginInterface, RevisionIdInterface {

  /**
   * The current revision id value.
   *
   * @var string
   */
  const CURRENT = 'current';

  /**
   * The latest revision id value.
   *
   * @var string
   */
  const LATEST = 'latest';

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get the revision id value.
   *
   * @param EntityInterface $entity
   *   The entity
   * @param string $revision_id_value
   *   The revision id value to evaluate.
   *
   * @return int
   *   The entity revision id.
   *
   * @throws \Exception
   */
  public function getRevisionId(EntityInterface $entity, $revision_id_value) {

    if ($revision_id_value === static::CURRENT) {
      return $entity->getLoadedRevisionId();
    }
    if ($revision_id_value === static::LATEST) {
      // TODO: I could not figure out how to provide a constructor with the entity
      // type manager service injected.  I tried makgin this implement the ContainerFactoryPluginInterface but that did not work.
      if ($storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId())) {
        return $storage->getLatestRevisionId($entity->id());
      }
    }

    throw new \Exception('Invalid revision id value.');
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

}
