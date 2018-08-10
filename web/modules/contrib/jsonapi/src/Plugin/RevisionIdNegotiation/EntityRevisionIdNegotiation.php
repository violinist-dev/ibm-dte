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
  public function getRevisionId(EntityInterface $entity, $input_data) {
    if (!is_numeric($input_data)) {
      throw new \InvalidArgumentException('The entity revision id must be in integer value for entity ' . $entity->uuid());
    }
    return $input_data;
  }

}
