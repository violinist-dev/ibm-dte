<?php

namespace Drupal\jsonapi\Plugin\RevisionId;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jsonapi\Revisions\RevisionIdInterface;

/**
 * Defines a revision id implementation for the core current or latest revsion id values.
 *
 * @RevisionId(
 *   id = "current_latest_revision_id",
 *   title = @Translation("Current or Latest Revision ID Value"),
 *   description = @Translation("Handles current or latest revision id values."),
 * )
 */
class CurrentLatestRevisionId implements RevisionIdInterface {

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
      if ($storage = \Drupal::entityTypeManager()->getStorage($entity->getEntityTypeId())) {
        return $storage->getLatestRevisionId($entity->id());
      }
    }

    throw new \Exception('Invalid revision id value.');
  }

}
