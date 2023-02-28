<?php

declare(strict_types=1);

namespace Drupal\ckeditor_inserthtml\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Advanced Tab" plugin.
 *
 * @CKEditorPlugin(
 *   id = "dialogadvtab",
 *   label = @Translation("Advanced Tab Dialog")
 * )
 */
class CKEditorAdvancedTab extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $file = 'modules/custom/ckeditor_inserthtml/ckeditor/plugins/dialogadvtab/plugin.js';
      return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'AdvancedTab' => [
        'label' => $this->t('Advanced Tab'),
        'image' => $this->getModulePath('ckeditor_inserthtml') . '/link.png',
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
