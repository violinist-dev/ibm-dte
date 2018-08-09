<?php

namespace Drupal\jsonapi\Plugin\RevisionId;

use Drupal\Core\Entity\EntityInterface;
use Drupal\jsonapi\Revisions\RevisionIdInterface;

/**
 * Defines a revision id implementation for entity revision id values.
 *
 * @RevisionId(
 *   id = "entity_revision_id",
 *   title = @Translation("Entity Revision ID Value"),
 *   description = @Translation("Handles entity revision id values."),
 * )
 */
class EntityRevisionId implements RevisionIdInterface {

  public function getRevisionId(EntityInterface $entity, $revision_id_value) {
    if (!is_numeric($revision_id_value)) {
      throw new \Exception('The entity revision id must be in integer value.');
    }
    return $revision_id_value;
  }

}
