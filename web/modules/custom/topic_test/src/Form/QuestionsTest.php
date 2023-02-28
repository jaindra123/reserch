<?php 

	/**
	 * 
	 */
	namespace Drupal\topic_test\Form;
	use Drupal\Core\Form\FormBase;
	use Drupal\Core\Form\FormStateInterface;
    use Drupal\paragraphs\Entity\Paragraph;
    use Drupal\Core\Url;

	class QuestionsTest extends FormBase
	{
        protected $question_ids;
		
		public function getFormId() {
			return 'questions_test';
		}

		public function buildForm(array $form, FormStateInterface $form_state) {

            
            $route_match = \Drupal::routeMatch();
            $node = $route_match->getParameter('node');

            if ( $node ) {
                $questions = $node->get('field_questions')->getValue();
                $box_text = $node->get('field_box_text')->value;
            } else {
                $box_text = '';
            }

            // set box_text in form field
            $form['box_text'] = [
                "#type" => "processed_text",
                "#text" => $box_text,
                "#format" => "full_html",
                "#langcode" => "en"
            ];
            $form['node_id'] = [
                '#type' => 'hidden',
                '#value' => $node->id(),
            ];

            foreach( $questions as $question ) {
                $this->question_ids[] = $question['target_id'];
                $load_question = Paragraph::load($question['target_id']);
                $question_text = $load_question->get('field_question_text')->value;
                $question_text =  [
                    "#type" => "processed_text",
                    "#text" => $question_text,
                    "#format" => "full_html",
                    "#langcode" => "en"
                ];
                $answer_id = $load_question->get('field_answers')->getValue()[0]['target_id'];
                $load_answer = Paragraph::load($answer_id);

                if($load_answer == NULL) {
                    continue;
                }
                
                if( $load_answer->bundle() == 'singlechoice_answer' ) {

                    $answer_options = $load_answer->get('field_option_single')->getValue();
                    
                    $option = array();
                    foreach( $answer_options as $answer_option ) {
                        $load_option = Paragraph::load($answer_option['target_id']);
                        $option_text = $load_option->get('field_option_text')->value;
                        $option[ $option_text ] = $option_text;
                        if( $load_option->get('field_correct_option')->value == '1' ) {
                            $option_correct = $option_text;
                        }
                    }
                    $form['question_'.$load_question->id()] = [
                        '#type' => 'radios',
                        '#options' => $option,
                        '#title' => $question_text,
                        '#correct' => $option_correct,
                        '#question_id' => $load_question->id(),
                    ];

                } else if( $load_answer->bundle() == 'multichoice_answer' ) {

                    $answer_options = $load_answer->get('field_answer_options')->getValue();
                    $option = array();
                    $answer_correct = array();
                    foreach( $answer_options as $answer_option ) {
                        $load_option = Paragraph::load($answer_option['target_id']);
                        $option_text = $load_option->get('field_text')->value;
                        $option[ $option_text ] = $option_text;
                        if( $load_option->get('field_correct')->value == '1' ) {
                            $answer_correct[ $option_text ] = $option_text;
                        }
                    }
                    $form['question_'.$load_question->id()] = [
                        '#type' => 'checkboxes',
                        '#options' => $option,
                        '#title' => $question_text,
                        '#correct' => $answer_correct,
                        '#question_id' => $load_question->id(),
                    ];

                }
            }
    
            $form['question_ids'] = [
                '#type' => 'hidden',
                '#value' => $this->question_ids,
            ];

            $form['submit'] = [
                '#type' => 'submit',
                '#value' => t('Submit'),
            ];
            
            $form['#action'] = Url::fromUri('internal:/' . 'test-result')->toString();
            $form['#theme'] = 'topic_test_form';
        
            return $form;
        }
        
        public function validateForm(array &$form, FormStateInterface $form_state) {
            
        }
    
        public function submitForm(array &$form, FormStateInterface $form_state) {
            
            foreach( $this->question_ids as $question_id ) {
                $correct_answer[$question_id] = $form['question_'.$question_id]['#correct'];
            }

        }

	}
