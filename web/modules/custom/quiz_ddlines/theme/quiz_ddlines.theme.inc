<?php

/**
 * The theme file for quiz ddlines.
 *
 * Sponsored by: Senter for IKT i utdanningen
 * Code: paalj
 *
 * @file
 * Theming functions for the ddlines question type.
 */

/**
 * Theming function for the results
 *
 * @param array $vars
 */
function theme_quiz_question_feedback__quiz_ddlines($vars) {
  static $css_added;
  if (!$css_added) {
    // @FIXME
// The Assets API has totally changed. CSS, JavaScript, and libraries are now
// attached directly to render arrays using the #attached property.
// 
// 
// @see https://www.drupal.org/node/2169605
// @see https://www.drupal.org/node/2408597
// drupal_add_css(drupal_get_path('module', 'quiz_ddlines') . '/theme/quiz_ddlines.css');

    $css_added = TRUE;
  }

  $rows = $vars['data'];

  // @FIXME
// theme() has been renamed to _theme() and should NEVER be called directly.
// Calling _theme() directly can alter the expected output and potentially
// introduce security issues (see https://www.drupal.org/node/2195739). You
// should use renderable arrays instead.
// 
// 
// @see https://www.drupal.org/node/2195739
// return theme('table', array('header' => NULL, 'rows' => $rows));
;
}
