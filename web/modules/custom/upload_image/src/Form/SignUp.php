<?php
/**
 * @file
 * Contains \Drupal\upload_image\Form\SignUp.
 */

namespace Drupal\upload_image\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

/**
 * Class BpForm.
 */
class SignUp extends FormBase {
/**
 * {@inheritdoc}
 */
public function getFormId() {
return 'node_upload_image';
}

/**
* {@inheritdoc}
*/


public   function buildForm(array $form, FormStateInterface $form_state) {
$form = array(
  '#attributes' => array('enctype' => 'multipart/form-data'),
);

$form['file_upload_details'] = array(
  '#markup' => t('<b>The File</b>'),
);


$form['my_file'] = array(
  '#type' => 'managed_file',
  '#name' => 'my_file',
  '#title' => t('My Logo'),
  '#size' => 20,
  '#attributes' => array('class' => array('custom-file-input') , 'id'=> array('customInput')),
  '#upload_validators' => [
    'file_validate_extensions' => ['png gif jpg jpeg'],
    'file_validate_size' => [2097152.0],
    //'file_validate_image_resolution'=>[" ","100x100"]
    ],
  '#upload_location' => 'public://appearance/',
);

$form['logo_hyperlink'] = array(
  '#type' => 'url',
  '#title' => t('Logo hyperlink'),
  
);

// set default image and logo link
$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
if(!empty($user->get("field_institution")->first())){
  $item = $user->get("field_institution")->first()->getValue();
  $nid = $item['target_id'];
  if($nid > 0) {
    $load_node = Node::load($nid);
    if( $load_node->get('field_logo')->entity ) {
      $fid = $load_node->get('field_logo')->entity->id();
      $file_entity = $load_node->get('field_logo')->entity;
      if( $fid != NULL ){
        $form['my_file']['#default_value']= [$fid];
        $file_uri = $file_entity->getFileUri();
        // dump($file_url);
        if (is_file($file_uri)) {
          $file_url = file_create_url( $file_uri );
        } else {
          $file_url = '/themes/custom/research/dist/images/content/imagenotfound.jpeg';
        }
        $form['my_file']['#image_url'] = $file_url;
      }
    }
    
    $logo_link = $load_node->get('field_logo_hyperlink')->value;
    if( $logo_link ) {
      $form['logo_hyperlink']['#default_value'] = $logo_link;
    }
  }
}

$form['actions']['#type'] = 'actions';
$form['actions']['submit'] = array(
  '#type' => 'submit',
  '#value' => $this->t('Save'),
  '#button_type' => 'primary',
  '#attributes' => array('class' => array('btn-contact top-link  mr-3 bgthd float-left')),
);
$form['#theme'] = "upload_image_registration_form";
return $form;

//dump($form);
}



/**
* {@inheritdoc}
* form validation handler
*/
public function validateForm(array &$form, FormStateInterface $form_state) { 
  // if ($form_state->getValue('my_file') == NULL) {
  //   $form_state->setErrorByName('my_file', $this->t('Logo not upload '));
  // }
}
/**
* {@inheritdoc}
* Submit handler
*/
public function submitForm(array &$form, FormStateInterface $form_state) {

  $nid=0;

  $roles = \Drupal::currentUser()->getRoles();
  if(in_array('institute', $roles)) {
    
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    //$item=$user->get("field_institution")->first()->getValue();
    //$nid= $item['target_id'];
    if(!empty($user->get("field_institution")->first())){
      $item=$user->get("field_institution")->first()->getValue();
      $nid= $item['target_id'];
    }
  }
  
  if($nid > 0){

  $fid = current($form_state->getValue('my_file'));
  if($fid < 0) {
    $load_node = Node::load($nid);
    if( $load_node->get('field_logo')->entity ) {
      $fid = $load_node->get('field_logo')->entity->id();
      $logo_link = $load_node->get('field_logo_hyperlink')->value;
      if( $fid != NULL ){
        $form['my_file']['#default_value']= [$fid];
      }
    }
    if( $logo_link ) {
      $form['logo_hyperlink']['#default_value'] = $logo_link;
    }
  }

  $logo_link=$form_state->getValue('logo_hyperlink');

  $field_image = array('target_id' => $fid,'alt' => "Image ",'title' => "logo",);
  $node = Node::load($nid); 
  //set value for field
  $node->field_logo = $field_image;
  if($logo_link!=''){
    $node->field_logo_hyperlink = $logo_link;
  }
  //save to update node
  $node->save();

  if ($form_state->getValue('my_file') == NULL) {
    \Drupal::messenger()->addMessage('Your Logo removed successfully ', 'status');
  } else {
    \Drupal::messenger()->addMessage('Your Logo upload successfully ', 'status');
  }
}

}

}
