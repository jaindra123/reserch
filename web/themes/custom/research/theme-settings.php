<?php

/**
 * @file
 * research theme settings.
 */

use Drupal\research\ThemeSettingsPreRender;
use Drupal\Core\File\Exception\FileException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * Form override for theme settings.
 */
function research_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }
  $form['#pre_render'][] = [ThemeSettingsPreRender::class, "preRender"];
  // Load file before running process (prevent not found on ajax,
  // validate and submit handlers).
  $build_info = $form_state->getBuildInfo();
  $theme_settings_files[] = drupal_get_path('theme', 'research') . '/theme-settings.php';
  $theme_settings_files[] = drupal_get_path('theme', 'research') . '/research.theme';
  foreach ($theme_settings_files as $theme_settings_file) {
    if (!in_array($theme_settings_file, $build_info['files'])) {
      $build_info['files'][] = $theme_settings_file;
    }
  }
  $form_state->setBuildInfo($build_info);

  $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
  $logo_path = $theme_path . '/assets/';

  $form['logo']['logo_path_anonymous'] = [
    '#type' => 'textfield',
    '#title' => t('Path to homepage logo'),
    '#default_value' => theme_get_setting('logo_path_anonymous'),
    '#states' => [
      'invisible' => [
        'input[name="research_home_page_settings[research_use_home_page_markup]"]' => ['checked' => FALSE],
      ],
    ],
    '#description' => t('Examples: <code>@implicit-public-file</code>,<code>@implicit-public-file</code> or <code>@implicit-file</code>.<br>If the "different homepage for anonymous users" option is enabled allows overriding the logo on the homepage.', [
      '@implicit-public-file' => $logo_path . 'Logo-Opigno-3-dark.svg',
      '@implicit-file' => 'Logo-Opigno-3-dark.svg',
      '@implicit-schema-file' => 'public://Logo-Opigno-3-dark.svg',
    ]),
  ];

  $form['logo']['logo_anonymous_upload'] = [
    '#type' => 'file',
    '#title' => t('Upload homepage logo image'),
    '#upload_location' => 'public://',
    '#upload_validators' => [
      'file_validate_extensions' => ['gif png jpg jpeg svg'],
    ],
    '#states' => [
      'invisible' => [
        'input[name="research_home_page_settings[research_use_home_page_markup]"]' => ['checked' => FALSE],
      ],
    ],
    '#description' => t("If you don't have direct file access to the server, use this field to upload your logo."),
  ];

  // research header settings.
  $form['research_header_settings'] = [
    '#type' => 'details',
    '#title' => t('Header background'),
    '#open' => TRUE,
  ];

  $form['research_header_settings']['research_use_header_background'] = [
    '#type' => 'checkbox',
    '#title' => t('Use another image for the header background'),
    '#description' => t('Check here if you want the theme to use a custom image for the header background.'),
    '#default_value' => theme_get_setting('research_use_header_background'),
  ];

  $form['research_header_settings']['image'] = [
    '#type' => 'container',
    '#states' => [
      'invisible' => [
        'input[name="research_use_header_background"]' => ['checked' => FALSE],
      ],
    ],
  ];

  $form['research_header_settings']['image']['research_header_image_path'] = [
    '#type' => 'textfield',
    '#title' => t('The path to the header background image.'),
    '#description' => t('The path to the image file you would like to use as your custom header background (relative to sites/default/files). The suggested size for the header background is 3000x134.'),
    '#default_value' => theme_get_setting('research_header_image_path'),
  ];

  $form['research_header_settings']['image']['research_header_image_upload'] = [
    '#type' => 'managed_file',
    '#upload_location' => 'public://',
    '#upload_validators' => [
      'file_validate_is_image' => ['gif png jpg jpeg'],
    ],
    '#title' => t('Upload an image'),
    '#description' => t("If you don't have direct file access to the server, use this field to upload your header background image."),
  ];

  if (\Drupal::moduleHandler()->moduleExists('opigno_mobile_app')) {
    // Premium users for mobile application can see this image.
    // Functionality to view this logo is available only
    // in mobile app not in Opigno.
    $form['research_mobile_app'] = [
      '#type' => 'details',
      '#title' => t('Mobile app logo'),
      '#open' => TRUE,
    ];

    $default_value = theme_get_setting('mobile_app_premium_logo') ?: 0;
    $form['research_mobile_app']['mobile_app_premium_logo'] = [
      '#type' => 'managed_file',
      '#title' => t('Logo to display on mobile app'),
      '#default_value' => ['target_id' => $default_value],
      '#description' => t('Allowed formats: @format', ['@format' => 'png']),
      '#upload_validators' => [
        'file_validate_extensions' => ['png'],
      ],
      '#upload_location' => 'public://logo/',
    ];
  }

  // research homepage settings.
  $research_home_page_settings = theme_get_setting('research_home_page_settings');

  $form['research_home_page_settings'] = [
    '#type' => 'details',
    '#title' => t('Homepage settings'),
    '#tree' => TRUE,
    '#open' => TRUE,
  ];

  $form['research_home_page_settings']['research_use_home_page_markup'] = [
    '#type' => 'checkbox',
    '#title' => t('Use a different homepage for anonymous users.'),
    '#description' => t('Check here if you want the theme to use a custom page for users that are not logged in.'),
    '#default_value' => $research_home_page_settings['research_use_home_page_markup'],
  ];

  if (!$form_state->get('num_slides') && isset($research_home_page_settings['research_home_page_slides'])) {
    $nb_slides = isset($research_home_page_settings['research_home_page_slides']['actions']) ? count($research_home_page_settings['research_home_page_slides']) - 1 : count($research_home_page_settings['research_home_page_slides']);
    $form_state->set('num_slides', $nb_slides);
  }

  $num_slides = $form_state->get('num_slides');
  if (!$num_slides) {
    $form_state->set('num_slides', research_HOMEPAGE_DEFAULT_NUM_SLIDES);
    $num_slides = research_HOMEPAGE_DEFAULT_NUM_SLIDES;
  }

  $form['research_home_page_settings']['research_home_page_slides'] = [
    '#type' => 'container',
    '#prefix' => '<div id="research-home-page-settings-slides-wrapper">',
    '#suffix' => '</div>',
  ];

  for ($i = 1; $i <= $num_slides; $i++) {
    $form['research_home_page_settings']['research_home_page_slides'][$i] = [
      '#type' => 'details',
      '#title' => t('Slide @num', ['@num' => $i]),
      '#tree' => TRUE,
      '#open' => TRUE,
      '#states' => [
        'invisible' => [
          'input[name="research_home_page_settings[research_use_home_page_markup]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['research_home_page_settings']['research_home_page_slides'][$i]['research_home_page_image_path'] = [
      '#type' => 'textfield',
      '#title' => t('The path to the home page background image.'),
      '#description' => t('The path to the image file you would like to use as your custom home page background (relative to sites/default/files).'),
      '#default_value' => isset($research_home_page_settings['research_home_page_slides'][$i]['research_home_page_image_path']) ? $research_home_page_settings['research_home_page_slides'][$i]['research_home_page_image_path'] : NULL,
    ];

    $form['research_home_page_settings']['research_home_page_slides'][$i]['research_home_page_image_upload'] = [
      '#name' => 'research_home_page_image_upload_' . $i,
      '#type' => 'managed_file',
      '#upload_location' => 'public://',
      '#upload_validators' => [
        'file_validate_is_image' => ['gif png jpg jpeg'],
      ],
      '#title' => t('Upload an image'),
      '#description' => t("If you don't have direct file access to the server, use this field to upload your background image."),
    ];
  }

  $form['research_home_page_settings']['research_home_page_slides']['actions'] = [
    '#type' => 'actions',
    '#states' => [
      'invisible' => [
        'input[name="research_home_page_settings[research_use_home_page_markup]"]' => ['checked' => FALSE],
      ],
    ],
  ];

  $form['research_home_page_settings']['research_home_page_slides']['actions']['add_name'] = [
    '#type' => 'submit',
    '#value' => ($num_slides < 1) ? t('Add a slide') : t('Add another slide'),
    '#submit' => ['research_form_system_theme_settings_add_slide_callback'],
    '#ajax' => [
      'callback' => 'research_form_system_theme_settings_slide_callback',
      'wrapper' => 'research-home-page-settings-slides-wrapper',
    ],
  ];

  if ($num_slides > 1) {
    $form['research_home_page_settings']['research_home_page_slides']['actions']['remove_name'] = [
      '#type' => 'submit',
      '#value' => t('Remove latest slide'),
      '#submit' => ['research_form_system_theme_settings_remove_slide_callback'],
      '#ajax' => [
        'callback' => 'research_form_system_theme_settings_slide_callback',
        'wrapper' => 'research-home-page-settings-slides-wrapper',
      ],
    ];
  }

  // Main menu settings.
  if (\Drupal::moduleHandler()->moduleExists('menu_ui')) {
    $form['research_menu_settings'] = [
      '#type' => 'details',
      '#title' => t('Menu settings'),
      '#open' => TRUE,
    ];

    $form['research_menu_settings']['research_menu_source'] = [
      '#type' => 'select',
      '#title' => t('Main menu source'),
      '#options' => [0 => t('None')] + menu_ui_get_menus(),
      '#description' => t("The menu source to use for the tile navigation. If 'none', research will use a default list of tiles."),
      '#default_value' => theme_get_setting('research_menu_source'),
    ];

    $form['research_menu_settings']['research_menu_show_for_anonymous'] = [
      '#type' => 'checkbox',
      '#title' => t('Show menu for anonymous users'),
      '#description' => t('Show the main menu for users that are not logged in. Only links that users have access to will show up.'),
      '#default_value' => theme_get_setting('research_menu_show_for_anonymous'),
    ];
  }

  // Validate and submit.
  $form['#validate'][] = 'research_form_system_theme_settings_alter_validate';
  $form['#submit'][] = 'research_form_system_theme_settings_alter_submit';

  $form_state->setCached(FALSE);
}

/**
 * Validation callback for research_form_system_theme_settings_alter().
 */
function research_form_system_theme_settings_alter_validate($form, &$form_state) {
  if (in_array('research_form_system_theme_settings_remove_slide_callback', $form_state->getSubmitHandlers()) ||
    in_array('research_form_system_theme_settings_add_slide_callback', $form_state->getSubmitHandlers()) ||
    in_array('file_managed_file_submit', $form_state->getSubmitHandlers())
  ) {
    return;
  }

  $storage = $form_state->getStorage();
  $new_storage = [];
  // Check for a new uploaded logo.
  if (isset($form['logo'])) {
    $file = _file_save_upload_from_form($form['logo']['logo_anonymous_upload'], $form_state, 0);
    if ($file) {
      // Put the temporary file in form_values so we can save it on submit.
      $form_state->setValue('logo_anonymous_upload', $file);
    }
  }

  $fid = $form_state->getValue('research_header_image_upload');
  if (!empty($fid) && $fid[0]) {
    $file = File::load($fid[0]);
    if ($file) {
      $file->setPermanent();
      $file->save();
      $file_usage = \Drupal::service('file.usage');
      $file_usage->add($file, 'research', 'user', \Drupal::currentUser()->id());
      $new_storage['research_header_image_path'] = $file;
    }
    else {
      $form_state->setErrorByName('research_header_image_upload', t("Couldn't upload file."));
    }
  }

  $research_home_page_settings = $form_state->getValue('research_home_page_settings');

  for ($i = 1; $i <= $storage['num_slides']; $i++) {
    $fid = $research_home_page_settings['research_home_page_slides'][$i]['research_home_page_image_upload'];

    if (!empty($fid) && $fid[0]) {
      $file = File::load($fid[0]);
      if ($file) {
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'research', 'user', \Drupal::currentUser()->id());
        $new_storage['research_home_page_image_path'][$i] = $file;
      }
      else {
        $form_state->setErrorByName('research_home_page_settings', t("Couldn't upload file."));
      }
    }
  }

  $form_state->setStorage($new_storage);
}

/**
 * Submission callback for research_form_system_theme_settings_alter().
 */
function research_form_system_theme_settings_alter_submit($form, FormStateInterface $form_state) {
  $storage = $form_state->getStorage();

  $values = $form_state->getValues();
  // If the user uploaded a new logo or favicon, save it to a permanent location
  // and use it in place of the default theme-provided file.
  $default_scheme = \Drupal::config('system.file')->get('default_scheme');
  try {
    if (!empty($values['logo_anonymous_upload'])) {
      $filename = \Drupal::service('file_system')->copy($values['logo_anonymous_upload']->getFileUri(), $default_scheme . '://');
      $form_state->setValue('logo_path_anonymous', $filename);
    }
  }
  catch (FileException $e) {
    // Ignore.
  }
  $form_state->unsetValue('logo_anonymous_upload');

  if (isset($storage['research_header_image_path']) && $storage['research_header_image_path']) {
    $file = $storage['research_header_image_path'];
    $form_state->setValue('research_header_image_path', str_replace('public://', '', $file->getFileUri()));
  }

  if (\Drupal::moduleHandler()->moduleExists('opigno_mobile_app')) {
    $fid = $form_state->getValue('mobile_app_premium_logo');
    // Replace file name and file uri.
    if (!empty($fid) && $fid[0]) {
      $file = File::load($fid[0]);
      $file_uri = $file->getFileUri();
      // Set up a new name in database.
      $file->setFilename('premium_logo.png');
      $file->setFileUri('public://logo/premium_logo.png');
      $file->setPermanent();
      $file->save();

      // Replace filename.
      rename($file_uri, 'public://logo/premium_logo.png');
      $form_state->setValue('mobile_app_premium_logo', $file->id());
    }
    else {
      $form_state->setValue('mobile_app_premium_logo', 0);
    }
  }

  if (isset($storage['research_home_page_image_path']) && $storage['research_home_page_image_path']) {
    foreach ($storage['research_home_page_image_path'] as $key => $file) {
      $research_home_page_settings = $form_state->getValue('research_home_page_settings');
      $research_home_page_settings['research_home_page_slides'][$key]['research_home_page_image_path'] = str_replace('public://', '', $file->getFileUri());
      $form_state->setValue('research_home_page_settings', $research_home_page_settings);
    }
  }
}

/**
 * Submission callback for research_form_system_theme_settings_alter().
 *
 * Specific logic for color integration. Remove white from the images to make
 * them transparent.
 */
function research_form_system_theme_settings_alter_color_submit($form, $form_state) {
}

/**
 * Submit handler for the "add" button.
 *
 * Increments the max counter and causes a rebuild.
 */
function research_form_system_theme_settings_add_slide_callback(array &$form, FormStateInterface $form_state) {
  $form_state->set('num_slides', $form_state->get('num_slides') + 1);
  $form_state->setRebuild();
}

/**
 * Submit handler for the "remove" button.
 *
 * Decrements the max counter and causes a form rebuild.
 */
function research_form_system_theme_settings_remove_slide_callback(array &$form, FormStateInterface $form_state) {
  if ($form_state->get('num_slides') > 1) {
    $form_state->set('num_slides', $form_state->get('num_slides') - 1);
  }
  $form_state->setRebuild();
}
