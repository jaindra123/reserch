<?php

namespace Drupal\landing_page\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\redirect\Entity\Redirect;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;

/**
 * Controller routines for user routes.
 */
class LandingPageController extends ControllerBase {

  public function landingPage() {
    $roles = \Drupal::currentUser()->getRoles();
    if( in_array('administrator', $roles) || in_array('bloomsbury_editor', $roles) || in_array('institute', $roles) || in_array('instructor', $roles) ) {
      
    } else {
      $response = new RedirectResponse($GLOBALS['base_url']);
      $response->send();
    }
    return array(
      '#theme' => 'admin_hub',
      '#values' => 'data',
    );
  }

  public function userDelete(UserInterface $user) {

    if( isset($_POST['confirm_deletion']) ) {
      $destination = \Drupal::request()->query->get('destination');
      $confirm_deletion = $_POST['confirm_deletion'];
      if( $confirm_deletion == 'no' ) {
        if ( $destination ) {
          $redirect = new RedirectResponse($destination);
          $redirect->send();
        }
      } else if( $confirm_deletion == 'yes' ) {
        $uid = $user->id();
        // user_cancel([], $uid, 'user_cancel_reassign');
        $account = User::load($uid);
        $account->delete();
        $load_user = User::load($uid);
        if( $load_user ) {
          \Drupal::messenger()->addMessage('Unable to delete the User');
        } else {
          \Drupal::messenger()->addMessage('User deleted successfully');
          if ( $destination ) {
            $redirect = new RedirectResponse($destination);
            return $redirect;
          }
        }
      }
    }
    return array(
      '#theme' => 'user_delete',
      '#value' => $user,
    );
  }

}
