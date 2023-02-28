<?php

namespace Drupal\research_right_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a 'Licence' Block.
 *
 * @Block(
 *   id = "licence_count",
 *   admin_label = @Translation("Licence Count"),
 *   category = @Translation("Licence Count"),
 * )
 */
class InstitutionLicenceBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $roles = \Drupal::currentUser()->getRoles();
    $current_user = \Drupal::currentUser();
    $uids = \Drupal::currentUser()->id();
    $query = \Drupal::database();

    $institution_id = $query->select('user__field_institution', 'ufi')
      ->condition('ufi.entity_id', $uids)
      ->fields('ufi', ['field_institution_target_id'])
      ->execute()
      ->fetchField();

    $num_licences = $query->select('node__field_number_of_licences', 'fnol')
      ->condition('fnol.entity_id', $institution_id)
      ->fields('fnol', ['field_number_of_licences_value'])
      ->execute()
      ->fetchField();
    if (in_array('institute', $roles)) {
      $user = User::load(\Drupal::currentUser()->id());
      $count_licence_id = $user->get("field_institution")->getValue();
      $query = \Drupal::database()->select('users_field_data', 'ufd')
        ->fields('ufd', ['uid']);
      $query->innerJoin('user__roles', 'ur', 'ufd.uid=ur.entity_id');
      $query->leftJoin('user__field_institution', 'ufi', 'ufd.uid=ufi.entity_id');
      $query->condition('ur.roles_target_id', 'student', '=')
        ->condition('ufd.status', 1, '=')
        ->condition('ufi.field_institution_target_id', $institution_id, '=');
      $query->range(0, 11);
      $result = $query->execute()->fetchAll();
      $num_of_license_useed = count($result);
    }
    return [
      '#markup' => $this->t('
        <div class="row">
          <div class="col-md-12">
            <p>Here you can keep a track of how many individual student licences you have remaining for your academic year.</p>
            <div class="licenses-wrap">
              <h5>Licenses</h5>
              <p><strong>' . $num_of_license_useed . '</strong> of <strong> ' . $num_licences . ' </strong>Licenses</b> remaining</p>
            </div>
          </div>
        '),
    ];
  }

}
