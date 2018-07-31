<?php

namespace Drupal\gm_addons\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "inlineCodeToggle" plugin.
 *
 * @CKEditorPlugin(
 *   id = "inlineCodeToggle",
 *   label = @Translation("InlineCodeToggle"),
 * )
 */
class InlineCodeToggle extends CKEditorPluginBase {
  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'gm_addons') . '/js/plugins/inline-code-toggle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return array(
      'Code' => array(
        'label' => $this->t('Inline Code Toggle'),
        'image' => drupal_get_path('module', 'gm_addons') . '/images/code.png',
      ),
    );
  }

}
