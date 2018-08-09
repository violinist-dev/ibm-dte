<?php

namespace Drupal\jsonapi\Revisions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an revision id annotation object.
 *
 * Plugin Namespace: Plugin\RevisionId
 *
 * @see \Drupal\jsonapi\Revisions\RevisionIdManager
 * @see \Drupal\jsonapi\Revisions\RevisionIdInterface
 * @see plugin_api
 *
 * @Annotation
 */
class RevisionId extends Plugin {

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
