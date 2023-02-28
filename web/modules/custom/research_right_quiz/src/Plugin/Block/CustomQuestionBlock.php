<?php

namespace Drupal\research_right_quiz\Plugin\Block;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\quiz\Entity\QuizResult;
use Drupal\quiz\Form\QuizQuestionFeedbackForm;
use Drupal\Core\Block\BlockBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\quiz\Entity\Quiz;
use Drupal\quiz\Entity\QuizQuestion;
use Drupal\quiz\Entity\QuizQuestionRelationship;
use Drupal\quiz\Entity\QuizResultAnswer;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a 'Research Right Custom Question' Block.
 *
 * @Block(
 *   id = "research_right_custom_question_block",
 *   admin_label = @Translation("Research Right Custom Question block"),
 *   category = @Translation("Research Right Custom Question block"),
 * )
 */
class CustomQuestionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $q = Quiz::load(1);
    $qq = QuizQuestion::load(1);
    $qqr = QuizQuestionRelationship::load(1);
    $qr = QuizResult::load(1);
    $qra = QuizResultAnswer::load(1);
   // $form = \Drupal::formBuilder()->getForm('Drupal\quiz\Form\QuizQuestionAnsweringForm');
    //return render($form);
    //$form = drupal_get_form('quiz_question_answering_form', $qq, $quiz_result->result_id);
    return [
      '#markup' => $this->t('Research Right Custom Question block 1'),
    ];
  }

}
