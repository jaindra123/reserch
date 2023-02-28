<?php

namespace Drupal\topic_test\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TopicTestController extends ControllerBase {

    public function topicTestResult() {

        $user_current = \Drupal::currentUser()->id();
        $user_info_current = \Drupal\user\Entity\User::load( $user_current );
        $user_current_id = $user_info_current->uid->value;

        // get the default test mode and attempts value if exist
        if( $user_info_current->get('field_institution')->getValue() ) {
            $institution_id = $user_info_current->get('field_institution')->getValue()[0]['target_id'];
            $load_institution = \Drupal\node\Entity\Node::load( $institution_id );

            if( is_object($load_institution) ) {
                $mode_default = $load_institution->get('field_topic_test_mode')->value;
                $attempts_default = $load_institution->get('field_topic_test_attempts')->value;

                $test_mode = $mode_default;

                define("LTI_MODE", "Restrict topic tests for LTI users only");

                if( $test_mode == LTI_MODE ) {
                    $test_total_attempts = $attempts_default;

                    // check wether result is confirmed or not by the lti user
                    $lti_result_confirmed = isset($_POST['result']) ? $_POST['result'] : NULL;

                    if( $lti_result_confirmed != 'confirmed' ) {
                        // get the available no. of attempts for the current lti user
                        $current_attempts = $user_info_current->get('field_topic_test_attempts')->value;
                        if( $current_attempts == NULL ) {
                            $current_attempts = $attempts_default;
                        }
                        // descrease the current_attempts by 1
                        if( $current_attempts != 'Unlimited' && $current_attempts > 0 ) {
                            $current_attempts = $current_attempts - 1;
                            $user_info_current->set('field_topic_test_attempts', $current_attempts);
                            $user_info_current->save();
                        }
                    }

                }
            } else {
                $test_mode = 'Self-study';
            }

        } else {
            $test_mode = 'Self-study';
        }

        // check wether result is confirmed or not by the lti user
        $lti_result_confirmed = isset($_POST['result']) ? $_POST['result'] : NULL;

        $question_ids = $_POST['question_ids'];
        $question_ids = explode(" ", $question_ids);
        $form_element = '';
        $question_no = 1;
        $total_questions = count($question_ids);
        $total_marks = $total_questions;
        $total_score = 0;

        foreach( $question_ids as $question_id ) {
            $load_question = Paragraph::load($question_id);
            $question_text = $load_question->get('field_question_text')->value;
            $answer_id = $load_question->get('field_answers')->getValue()[0]['target_id'];
            $feedback_correct = $load_question->get('field_feedback_for_correct')->value;
            $feedback_incorrect = $load_question->get('field_feedback_for_incorrect')->value;
            $load_answer = Paragraph::load($answer_id);
            
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

                $input_name = 'question_'.$load_question->id();
                $radio_options = '';
                // check question is attempted by user or not
                if( isset($_POST[$input_name]) && $_POST[$input_name] != NULL ) {
                    $attempted = true;
                    $not_attempt_markup = '';
                } else {
                    $attempted = false;
                    $not_attempt_markup = '<div class="not-attempted">Not attempted.</div>';
                }

                $is_correct = false;
                if( $_POST[$input_name] == $option_correct ) {
                    $is_correct = true;
                }

                if( $is_correct ) {
                    $feedback = '<strong>Feedback: </strong>'. $feedback_correct;
                    // increase the score
                    $total_score = $total_score + 1;
                } else {
                    $feedback = '<strong>Feedback: </strong>'. $feedback_incorrect;
                }
                
                foreach( $option as $option_item ) {

                    // if the loop item in selected option
                    if( $_POST[$input_name] == $option_item ) {
                        $is_checked = 'checked';
                        if( $_POST[$input_name] == $option_correct ) {
                            $status_class = 'rightAnswer';
                            $icon_class = 'fa fa-check';
                        } else if( $_POST[$input_name] != $option_correct ) {
                            $status_class = 'worngAnswer';
                            $icon_class = 'fa fa-times';
                        }
                    } else {
                        $is_checked = '';
                        $status_class = 'd-none';
                    }

                    // make always right answer as correct
                    if( $option_item == $option_correct ) {
                        // if overall this answer is wrong
                        if( !$is_correct ) {
                            $status_class = 'rightAnswer not-chosen';
                            $icon_class = 'fa fa-check';
                        } else {
                            $status_class = 'rightAnswer';
                            $icon_class = 'fa fa-check';
                        }
                    }

                    $radio_options .= 
                    '<li>
                        <span class="showIocn '. $status_class .'"><i class="'. $icon_class .'"></i></span>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input disabled="disabled" type="radio" class="custom-control-input" id="'.$input_name. '-'. $option_item .'" name="'. $input_name .'" value="'. $option_item .'"'. $is_checked .'>
                            <label class="custom-control-label" for="'.$input_name. '-'. $option_item .'">'. $option_item .'</label>
                        </div>
                    </li>';
                }

                $form_element .= 
                '<div class="question-topic-item">
                    <h4>Question '. $question_no++ .' of '. $total_questions .'</h4>
                    '. $not_attempt_markup .'
                    <h5>'. $question_text .'</h5>
                    <ul>'. $radio_options .'</ul>
                    <div class="feedback mb-5">'. $feedback .'</div>
                </div>';

            } else if( $load_answer->bundle() == 'multichoice_answer' ) {

                $answer_options = $load_answer->get('field_answer_options')->getValue();
                $option = array();
                $option_correct = array();
                foreach( $answer_options as $answer_option ) {
                    $load_option = Paragraph::load($answer_option['target_id']);
                    $option_text = $load_option->get('field_text')->value;
                    $option[ $option_text ] = $option_text;
                    if( $load_option->get('field_correct')->value == '1' ) {
                        $option_correct[] = $option_text;
                    }
                }

                $input_name = 'question_'.$load_question->id();
                $radio_options = '';
                $is_correct = false;
                if( $_POST[$input_name] == $option_correct ) {
                    $is_correct = true;
                }

                // check question is attempted by user or not
                if( isset($_POST[$input_name]) && $_POST[$input_name] != NULL ) {
                    $attempted = true;
                    $not_attempt_markup = '';
                } else {
                    $attempted = false;
                    $not_attempt_markup = '<div class="not-attempted">Not attempted.</div>';
                }

                if( $is_correct ) {
                    $feedback = '<strong>Feedback: </strong>'. $feedback_correct;
                    // increase the score
                    $total_score = $total_score + 1;
                } else {
                    $feedback = '<strong>Feedback: </strong>'. $feedback_incorrect;
                }
                
                foreach( $option as $option_item ) {

                    // if the loop item in selected option
                    if( in_array($option_item, $_POST[$input_name]) ) {
                        $is_checked = 'checked';
                        if( in_array($option_item, $option_correct) ) {
                            $status_class = 'rightAnswer';
                            $icon_class = 'fa fa-check';
                        } else {
                            $status_class = 'worngAnswer';
                            $icon_class = 'fa fa-times';
                        }
                    } else {
                        $is_checked = '';
                        $status_class = 'd-none';
                    }

                    // make always right answers as correct
                    if( in_array($option_item, $option_correct) ) {
                        // if overall this answer is wrong
                        if( !$is_correct ) {
                            $status_class = 'rightAnswer not-chosen';
                            $icon_class = 'fa fa-check';
                        } else {
                            $status_class = 'rightAnswer';
                            $icon_class = 'fa fa-check';
                        }
                    }

                    $radio_options .= 
                    '<li>
                        <span class="showIocn '. $status_class .'"><i class="'. $icon_class .'"></i></span>
                        <div class="custom-control custom-checkbox">
                            <input disabled="disabled" type="checkbox" class="custom-control-input" id="'.$input_name. '-'. $option_item .'" name="'. $input_name .'" value="'. $option_item .'"'. $is_checked .'>
                            <label class="custom-control-label" for="'.$input_name. '-'. $option_item .'">'. $option_item .'</label>
                        </div>
                    </li>';
                }

                $form_element .= 
                '<div class="question-topic-item">
                    <h4>Question '. $question_no++ .' of '. $total_questions .'</h4>
                    '. $not_attempt_markup .'
                    <h5>'. $question_text .'</h5>
                    <ul>'. $radio_options .'</ul>
                    <div class="feedback mb-5">'. $feedback .'</div>
                </div>';

            }
        }

        // calculation of result and scores
        $percentage = ( $total_score/$total_marks ) * 100;
        $percentage = round($percentage, 2);

        if( $test_mode == LTI_MODE ) {
            // show unlimited text when user have unlimited no. of attempts
            $current_attempts = $user_info_current->get('field_topic_test_attempts')->value;
            if( $attempts_default == 'Unlimited' ) {
                $attempts_markup = 'You have unlimited attempts.';
            } else {
                $attempts_markup = 'You have <b>'. $current_attempts .' </b>of <b>'. $test_total_attempts .'</b> attempts remaining.';
            }
            $retake_test_button = ( $current_attempts > 1 ) ? '<a id="lti-retake" href="javascript:void(0)">Retake test.</a>' : '';
            $scoreboard_attempts_markup = 
            '<div class="attempts mb-3">
                '. $attempts_markup . $retake_test_button .
            '</div>';
        } else {
            $scoreboard_attempts_markup = '';
        }

        // scoreboard markup
        $scoreboard = 
        '<div class="scoreboard result-wrapper p-3">
            <h4 class="mb-3">Results</h4>
            <div class="score mb-3">You have scored <b>'. $total_score .'/'. $total_marks .'</b>, or <b>'. $percentage .'%</b></div>
            '. $scoreboard_attempts_markup .'
        </div>';

        // change the id of retake test buttono as per the test mode
        if( $test_mode == LTI_MODE ) {
            $retake_test_id = 'lti-retake';
            $retake_test_button_bottom = ( $current_attempts > 1 || $attempts_default == 'Unlimited' ) ? '<button id="'. $retake_test_id .'" class="form-submit" type="button">Retake test</button>' : '';
        } else {
            $retake_test_id = 'self-retake';
            $retake_test_button_bottom = '<button id="'. $retake_test_id .'" class="form-submit" type="button">Retake test</button>';
        }

        // get the box_text of the test page
        if( isset($_POST['node_id']) ) {
            $node = \Drupal\node\Entity\Node::load( (int)$_POST['node_id'] );
        } else {
            $node = NULL;
        }

        if ( $node ) {
            $box_text = $node->get('field_box_text')->value;
            if( $box_text ) {
                $box_text_markup = '<div class="box-content">'. $box_text .'</div>';
            } else {
                $box_text_markup = '';
            }
        } else {
            $box_text = '';
            $box_text_markup = '';
        }

        // main result markup
        $result_markup = $scoreboard.
        ''. $box_text_markup .'
        <div class="w-100 topicText-wrapper mt-4">'
            . $form_element .
            '<div class="button-groups mt-5">
                <a href="/" class="secondary-buttton">Return to Homepage</a>
                '. $retake_test_button_bottom .'
            </div>
        </div>';

        // confirmation markup
        if( $attempts_default == 'Unlimited' ) {
            $confirmation_attempts_markup = 'You have unlimited attempts.';
        } else {
            $confirmation_attempts_markup = '<b>This is attempt number '. ($test_total_attempts - $current_attempts) .' of '. $test_total_attempts .'. </b>';
        }
     
        $confirmation_markup = 
        '<div class="w-100 finishText-wrapper mt-4">
            <h4>Are you sure you want to finish this test?</h4>
            <p>'. $confirmation_attempts_markup .'</p>
            <p>Once finished, your answers will be submitted and you will be presented with the results.</p>					
            <p>Your instructor will also be able to see the results.</p>						
            <div class="button-groups mt-5">
                <a href="javascript:void(0)" id="back-to-form" class="secondary-buttton">Back</a>
                <button id="confirm-result" class="form-submit" type="button">Submit</button>
            </div>
        </div>';

        if( $test_mode == LTI_MODE ) {
            
            if( $lti_result_confirmed == 'confirmed' ) {
                $main_markup =  '<div id="lti-users-mode" class="main-markup lti-users-mode">'.  $result_markup .'</div>';

                // retake test confirmation markup for lti user
                echo 
                '<div class="retake-test-confirmation finishText-wrapper">
                    <form method="post">
                        <h4 class="title">You have been given the option to retake this test if you wish to.</h4>
                        <p>Click the \'Retake test\' button below to begin.</p>
                        <p>When you are satisfied with your answers, click the <b>\'Submit\'</b> button.</p>
                        <p><b>Please note</b>, any questions you do not attempt will be marked as incorrect in your final result.</p>
                        <div class="single-checkbox">
							<div class="custom-control custom-checkbox">
								<input name="confirm_retake_test" type="checkbox" class="custom-control-input" id="customCheck" required>
								<label class="custom-control-label" for="customCheck">
                                    <p>You are retaking this assessment.</p>
                                    <p>Your most recent result will replace the results of any previous attempts you have made.</p>
                                </label>
							</div>
						</div>
                        <button id="retake-test-confirm-btn" class="mt-4 form-submit" type="submit">Retake test</button>
                    </form>
                </div>';

            } else {
                $main_markup =  '<div id="lti-users-mode" class="main-markup lti-users-mode">'. $confirmation_markup .'</div>';
            }

        } else {
            $main_markup = '<div id="self-study-mode" class="main-markup self-study-mode">'. $result_markup .'</div>';
        }

        echo $main_markup;

        die();

    }

    public function topicTestManage() {

        $user_current = \Drupal::currentUser()->id();
        $user_info_current = \Drupal\user\Entity\User::load( $user_current );
        $user_current_id = $user_info_current->uid->value;

        $string = array();

        // get the options of the select field
        $contentTypes = \Drupal::service('entity_field.manager');
        $fields = $contentTypes->getFieldStorageDefinitions('node');
        $field_topic_test_mode = [];
        $field_topic_test_attempts = [];
        foreach( $fields as $key => $value ) {
            if( $key == 'field_topic_test_mode' ) {
                $attribute_data = $value->getSettings();
                $field_topic_test_mode = $attribute_data['allowed_values'];
            }

            if( $key == 'field_topic_test_attempts' ) {
                $attribute_data = $value->getSettings();
                $field_topic_test_attempts = $attribute_data['allowed_values'];
            }
        }

        // get the default value if exist
        if( $user_info_current->get('field_institution')->getValue() ) {
            $institution_id = $user_info_current->get('field_institution')->getValue()[0]['target_id'];
            $load_institution = \Drupal\node\Entity\Node::load( $institution_id );
            $mode_default = $load_institution->get('field_topic_test_mode')->value;
            $attempts_default = $load_institution->get('field_topic_test_attempts')->value;

            $string['field_topic_test_mode']['default'] = $mode_default;
            $string['field_topic_test_attempts']['default'] = $attempts_default;
        } else {
            $string['field_topic_test_mode']['default'] = '';
            $string['field_topic_test_attempts']['default'] = '';
        }


        $string['field_topic_test_mode']['options'] = $field_topic_test_mode;
        $string['field_topic_test_attempts']['options'] = $field_topic_test_attempts;

        return array(
            '#theme' => 'topic_test_manage',
            '#values' => $string,
        );
    }

    public function topicTestManageSave() {

        $user_current = \Drupal::currentUser()->id();
        $user_info_current = \Drupal\user\Entity\User::load( $user_current );
        $user_current_id = $user_info_current->uid->value;

        if( $user_info_current->get('field_institution')->getValue() ) {
            $institution_id = $user_info_current->get('field_institution')->getValue()[0]['target_id'];
            $load_institution = \Drupal\node\Entity\Node::load( $institution_id );
        } else {
            \Drupal::messenger()->addMessage(t('Data has not been updated, because You are not belonging to any institute'), 'status');
            $response = new RedirectResponse($GLOBALS['base_url'].'/manage-topictest');
            $response->send();
            exit;
        }

        
        $values = \Drupal::request()->request;
        $topictest_mode = $values->get('topictest_mode');
        $topictest_attempts = $values->get('topictest_attempts');

        $load_institution->field_topic_test_mode = $topictest_mode;
        $load_institution->field_topic_test_attempts = $topictest_attempts;

        $load_institution->save();

        if( $topictest_attempts != 'Unlimited' && $topictest_mode != 'Self-study' ) {
            $user_ids = \Drupal::entityQuery('user')
                        ->condition('field_institution', $institution_id)
                        ->execute();
            // dd($user_ids);
            $load_users = User::loadMultiple($user_ids);
            foreach( $load_users as $key => $load_user ) {
                if( !is_object($load_user) ) {
                    continue;
                }
                $load_user->set('field_topic_test_attempts', $topictest_attempts);
                $load_user->save();
            }
            // $user_info_current->set('field_topic_test_attempts', $topictest_attempts);
            // $user_info_current->save();
        }

        \Drupal::messenger()->addMessage(t('Data has been updated'), 'status');
        $response = new RedirectResponse($GLOBALS['base_url'].'/manage-topictest');
        $response->send();
        exit;

    }

}