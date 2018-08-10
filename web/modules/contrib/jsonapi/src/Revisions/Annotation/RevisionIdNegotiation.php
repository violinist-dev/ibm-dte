<?php

namespace Drupal\jsonapi\Revisions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an revision id negoriation annotation object.
 *
 * Plugin Namespace: Plugin\RevisionIdNegotiation
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

  /**
   * The human-readable name of the revision id plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The description of the revision id plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

}
