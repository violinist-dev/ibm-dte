<?php

namespace Drupal\jsonapi\Plugin\RevisionIdNegotiation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\jsonapi\Revisions\RevisionIdNegotiationInterface;

/**
 * Defines a revision id implementation for entity revision id values.
 *
 * @RevisionIdNegotiation(
 *   id = "id",
 * )
 */
class IdNegotiation extends PluginBase implements RevisionIdNegotiationInterface {

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
