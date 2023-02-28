<?php
namespace Drupal\quiz_ddlines_paragraph\Plugin\quiz\QuizQuestion;
use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\quiz\Entity\QuizQuestion;
use Drupal\quiz\Entity\QuizResultAnswer;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * @QuizQuestion (
 *   id = "ddlines_paragraph",
 *   label = @Translation("Drag and drop paragraph"),
 *   handlers = {
 *     "response" = "\Drupal\quiz_ddlines_paragraph\Plugin\quiz\QuizQuestion\DDLinesParagraphResponse"
 *   }
 * )
*/

class DDLinesParagraphQuestion extends QuizQuestion {
  use StringTranslationTrait;

    public function getCreationForm(array &$form_state = NULL) {
      return [];
    }

    public function getMaximumScore(): int {
      return 1;
    }


    public function getCorrectAnswer() {
      return $this->get('field_correct_ordering_answer')->getString();
    }

}
