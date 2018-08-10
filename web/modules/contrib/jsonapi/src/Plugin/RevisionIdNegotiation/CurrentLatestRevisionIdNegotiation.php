<?php

namespace Drupal\jsonapi\Plugin\RevisionIdNegotiation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\jsonapi\Revisions\RevisionIdNegotiationBase;

/**
 * Defines a revision id implementation for the core current or latest revsion id values.
 *
 * @RevisionIdNegotiation(
 *   id = "current_latest_revision_id",
 *   title = @Translation("Current or Latest Revision ID Value"),
 *   description = @Translation("Handles current or latest revision id values."),
 * )
 */
class CurrentLatestRevisionIdNegotiation extends RevisionIdNegotiationBase {

  /**
   * {@inheritdoc}
   */
  public function getRevisionId(EntityInterface $entity, $input_data) {

    if ($input_data === static::CURRENT) {
      return $entity->getLoadedRevisionId();
    }
    if ($input_data === static::LATEST) {
      // TODO: I could not figure out how to provide a constructor with the entity
      // type manager service injected.  I tried makgin this implement the ContainerFactoryPluginInterface but that did not work.
      if ($storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId())) {
        return $storage->getLatestRevisionId($entity->id());
      }
    }

    throw new \InvalidArgumentException('Invalid revision id value for entity ' . $entity->uuid());
  }

}
