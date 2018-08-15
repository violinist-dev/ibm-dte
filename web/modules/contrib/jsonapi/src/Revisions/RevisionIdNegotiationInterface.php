<?php

namespace Drupal\jsonapi\Revisions;

use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the common interface for all Revision ID negotiation classes.
 *
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiationManager
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiation
 * @see plugin_api
 */
interface RevisionIdNegotiationInterface {

  /**
   * Gets the revision id.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $input_data
   *   A value used to derive a revision id for the given entity.
   *
   * @return int
   *   The revision id.
   *
   * @throws \InvalidArgumentException
   *   When the revision ID cannot be negotiated.
   */
  public function getRevisionId(EntityInterface $entity, $input_data);

}
