<?php

namespace Drupal\ckeditor_inserthtml\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "inserthtml4x" plugin.
 *
 * @CKEditorPlugin(
 *   id = "insertbutton",
 *   label = @Translation("Insert Button")
 * )
 */
class CKEditorInsertButton extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return 'modules/custom/ckeditor_inserthtml/ckeditor/plugins/insertbutton/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'insertbutton' => [
        'label' => t('Insert Button'),
        'image' => 'modules/custom/ckeditor_inserthtml/simplebutton.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
