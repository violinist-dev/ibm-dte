<?php

namespace Drupal\jsonapi\Plugin\RevisionId;

use Drupal\Core\Entity\EntityInterface;
use Drupal\jsonapi\Revisions\RevisionIdBase;

/**
 * Defines a revision id implementation for the core current or latest revsion id values.
 *
 * @RevisionId(
 *   id = "current_latest_revision_id",
 *   title = @Translation("Current or Latest Revision ID Value"),
 *   description = @Translation("Handles current or latest revision id values."),
 * )
 */
class CurrentLatestRevisionId extends RevisionIdBase {

  /**
   * {@inheritdoc}
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

    throw new \InvalidArgumentException('Invalid revision id value.');
  }

}
