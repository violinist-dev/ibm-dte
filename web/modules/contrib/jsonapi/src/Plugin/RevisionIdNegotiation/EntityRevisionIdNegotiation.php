<?php

namespace Drupal\jsonapi\Plugin\RevisionIdNegotiation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\jsonapi\Revisions\RevisionIdNegotiationBase;

/**
 * Defines a revision id implementation for entity revision id values.
 *
 * @RevisionIdNegotiation(
 *   id = "entity_revision_id",
 *   title = @Translation("Entity Revision ID Value"),
 *   description = @Translation("Handles entity revision id values."),
 * )
 */
class EntityRevisionIdNegotiation extends RevisionIdNegotiationBase {

  /**
   * {@inheritdoc}
   */
  public function getRevisionId(EntityInterface $entity, $revision_id_value) {
    if (!is_numeric($revision_id_value)) {
      throw new \InvalidArgumentException('The entity revision id must be in integer value for entity ' . $entity->uuid());
    }
    return $revision_id_value;
  }

}
