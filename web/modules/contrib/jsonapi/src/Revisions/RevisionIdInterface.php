<?php

namespace Drupal\jsonapi\Revisions;

use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the common interface for all Revision ID classes.
 *
 * @see \Drupal\jsonapi\Revisions\RevisionIdManager
 * @see \Drupal\jsonapi\Revisions\RevisionId
 * @see plugin_api
 */
interface RevisionIdInterface {

  /**
   * Gets the revision id.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string|int
   *   A value used to derive a revision id for the given entity.
   *
   * @return int
   *   The revision id.
   *
   * @throws \Exception
   */
  public function getRevisionId(EntityInterface $entity, $revision_id_value);

}
