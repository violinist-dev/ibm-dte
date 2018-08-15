<?php

namespace Drupal\jsonapi\Revisions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an revision id negoriation annotation object.
 *
 * Plugin Namespace: Plugin\RevisionIdNegotiation.
 *
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiationManager
 * @see \Drupal\jsonapi\Revisions\RevisionIdNegotiationInterface
 * @see plugin_api
 *
 * @Annotation
 */
class RevisionIdNegotiation extends Plugin {

  /**
   * The revision id plugin ID.
   *
   * @var string
   */
  public $id;

}
