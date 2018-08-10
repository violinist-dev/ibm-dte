<?php

namespace Drupal\jsonapi\Revisions;

use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the common interface for all Revision ID classes.
 *
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiationManager
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiation
 * @see plugin_api
 */
interface RevisionIdNegotiationInterface {

  /**
   * Gets the revision id.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string|int $input_data
   *   A value used to derive a revision id for the given entity.
   *
   * @return int
   *   The revision id.
   *
   * @throws \InvaldArgumentException
   */
  public function getRevisionId(EntityInterface $entity, $revision_id_value);

}
