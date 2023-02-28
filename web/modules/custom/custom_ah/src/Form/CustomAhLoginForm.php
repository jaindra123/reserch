<?php

namespace Drupal\custom_ah\Form;

class CustomAhLoginForm extends \Drupal\Core\Form\FormBase
{

    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return 'custom_ah_login_form';
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        $form['email'] = [
          '#type' => 'textfield',
          '#title' => t('Email'),
          '#attributes' => ['placeholder' => 'Email']
        ];

      $form['password'] = [
        '#type' => 'password',
        '#title' => t('Password'),
        '#attributes' => ['placeholder' => 'Password']
      ];

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => t('login')
      ];

      return $form;
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state)
    {

      $authentication_hub = \Drupal::service('ah_client.authentication_hub');
      $email = $form_state->getValue('email');
      $password = $form_state->getValue('password');

      $response = $authentication_hub->login($email, $password);
      dump($response);
      die;
    }
}
