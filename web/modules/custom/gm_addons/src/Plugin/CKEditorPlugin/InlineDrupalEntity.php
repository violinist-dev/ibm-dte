<?php

namespace Drupal\gm_addons\Plugin\CKEditorPlugin;

use Drupal\entity_embed\Plugin\CKEditorPlugin\DrupalEntity;

class InlineDrupalEntity extends DrupalEntity {
  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'gm_addons') . '/js/plugins/inline-drupalentity.js';
  }
}
