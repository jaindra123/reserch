<?php

/**
 * 
 */

namespace Drupal\landing_page\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RemoveCommand;

class InstructorLandingPage extends ConfigFormBase
{

	protected function getEditableConfigNames() {
        return [
            'landing_page.instructor.settings',
        ];
    }

    public function getFormId() {
        return 'instructor_landing_page';
    }

	public function buildForm(array $form, FormStateInterface $form_state)
	{   
        $form['#attached']['library'][] = 'landing_page/landing_page.library';

        $config = $this->config('landing_page.instructor.settings');

		// page refresh case
		if( !$form_state->get('ajax_called') ) {
			for( $i = 0; $i <= 5; $i++ ) {
				unset($_SESSION['card_group_'.$i]);
			}
		}

		// $groups = $form_state->getValue('card_groups') ? $form_state->getValue('card_groups') : 1;
		if( $form_state->getValue('card_groups') ) {
			$groups = $form_state->getValue('card_groups');
		} else if( $config->get('card_groups') ) {
			$groups = $config->get('card_groups');
		} else {
			$groups = 1;
		}

		$form['#prefix'] = '<div id="form-wrapper">';
		$form['#suffix'] = '</div>';

        $form['title'] = [
            '#type' => 'textfield',
            '#title' => 'Title',
            '#prefix' => '<div class="col-md-6 col-xs-12">',
            '#suffix' => '</div>',
            '#default_value' => $config->get('title')
        ];
        $form['description'] = [
            '#type' => 'text_format',
            '#title' => 'Description',
            '#prefix' => '<div class="col-xs-12">',
            '#suffix' => '</div>',
            '#default_value' => isset($config->get('description')['value']) ? $config->get('description')['value'] : '',
        ];
        
		for ($i = 0; $i < $groups; $i++) {
			$form['card_group_' . $i] = [
				'#type' => 'details',
				'#title' => 'Card Group '.($i+1),
				// '#collapsible' => TRUE,
				// '#collapsed' => TRUE,
				'#prefix' => '<div id="card_group_' . $i . '">',
				'#suffix' => '</div>',
				'#open' => TRUE,
			];
			$cards_group = $form_state->getValue('card_group_' . $i);
			$cards_in_group = $cards_group['cards'] ? $cards_group['cards'] : 1;

			if( !$cards_group ) {
				$cards_group = $config->get('card_group_' . $i)['cards'];
				$cards_in_group = $cards_group ? $cards_group : 1;
			}

			$form['card_group_' . $i]['title'] = [
				'#type' => 'textfield',
				'#title' => 'Card Group Title',
				'#default_value' => $config->get('card_group_' . $i)['title'],
			];
			$form['card_group_' . $i]['weight'] = [
				'#type' => 'number',
				'#title' => 'Card Group Weight',
				'#default_value' => $config->get('card_group_' . $i)['title'] ? $config->get('card_group_' . $i)['weight'] : 0,
			];
			$form['card_group_' . $i]['cards'] = [
				'#type' => 'hidden',
				'#value' => $cards_in_group
			];

			for($j = 0; $j < $cards_in_group; $j++) {
				$form['card_group_' . $i]['card_' . $j] = [
					'#type' => 'details',
					'#title' => 'Card '.($j+1),
					// '#collapsible' => TRUE,
					// '#collapsed' => TRUE,
                    '#attributes' => [
                        'id' => 'card_group_' . $i . '-card_' . $j,
					],
					'#open' => TRUE,
				];
				$form['card_group_' . $i]['card_' . $j]['title'] = [
					'#type' => 'textfield',
					'#title' => 'Title',
					'#default_value' => $config->get('card_group_' . $i)['card_' . $j]['title'],
				];
				$form['card_group_' . $i]['card_' . $j]['icon'] = [
					'#type' => 'textfield',
                    '#title' => 'Card Icon',
                    '#description' => t('Enter the icon class. Find the icon class from here <a href="https://fontawesome.com/">Font Awesome</a>'),
					'#default_value' => $config->get('card_group_' . $i)['card_' . $j]['icon'],
				];
				$form['card_group_' . $i]['card_' . $j]['desc'] = [
					'#type' => 'text_format',
					'#title' => 'Description',
					'#default_value' => $config->get('card_group_' . $i)['card_' . $j]['desc']['value'],
				];
				$form['card_group_' . $i]['card_' . $j]['link'] = [
					'#type' => 'textfield',
                    '#title' => 'Card Link',
					'#default_value' => $config->get('card_group_' . $i)['card_' . $j]['link'],
				];
				$form['card_group_' . $i]['card_' . $j]['weight'] = [
					'#type' => 'number',
                    '#title' => 'Card Weight',
					'#default_value' => $config->get('card_group_' . $i)['card_' . $j]['weight'] ? $config->get('card_group_' . $i)['card_' . $j]['weight'] : 0,
				];
                $form['card_group_' . $i]['card_' . $j]['remove_card'] = [
                    '#type' => 'submit',
                    '#value' => 'Remove Card',
                    '#name' => 'card_group_' . $i .'-card_' . $j,
                    '#ajax' => [
                        'callback' => '::removeCardInGroup',
                        'wrapper' => 'card_group_' . $i,
                        'card' => 'card_group_' . $i .'-card_' . $j,
                    ],
                ];
			}
            // if(isset($_SESSION['card_group_'.$i])){
            //    foreach($_SESSION['card_group_'.$i] as $key => $val){
            //    	 unset($form['card_group_'.$i][$key]);
            //    }    
			// }
			$form['card_group_' . $i]['add_card'] = [
				'#type' => 'submit',
				'#value' => 'Add Card',
				'#ajax' => [
					'callback' => '::addCardInGroup',
					'wrapper' => 'card_group_' . $i,
				],
				'#name' => 'add_card_btn_' . $i,
			];
			
		}

		$form['add_card_group'] = [
			'#type' => 'submit',
			'#value' => 'Add Card Group',
			'#ajax' => [
				'callback' => '::addCardsGroup',
				'wrapper' => 'form-wrapper'
			],
			'#prefix' => '<div class="card-grp-buttons">',
		];

		$form['remove_card_group'] = [
			'#type' => 'submit',
			'#value' => 'Remove Card Group',
			'#ajax' => [
				'callback' => '::removeCardsGroup',
				'wrapper' => 'form-wrapper'
			],
			'#suffix' => '</div>',
		];
		$form['card_groups'] = [
			'#type' => 'hidden',
			'#value' => $groups
		];

        $form['cancel'] = [
            '#type' => 'submit',
            '#value' => t('Cancel'),
            '#submit' => ['::cancel'],
			'#prefix' => '<div class="action-buttons">',
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Submit'),
            '#submit' => ['::saveData'],
			'#suffix' => '</div>',
        ];

		$form['#tree'] = true;

		$form['#attached']['library'][] = 'core/drupal.ajax';
		$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
		$form['#cache']['max-age'] = 0;
		return $form;
	}


	public function addCardsGroup(array &$form, FormStateInterface $form_state)
	{
		return $form;
	}

	public function removeCardsGroup(array &$form, FormStateInterface $form_state)
	{
		return $form;
	}

	public function addCardInGroup(array &$form, FormStateInterface $form_state)
	{
		$element = $form_state->getTriggeringElement();
		return $form[$element['#ajax']['wrapper']];
	}
	public function removeCardInGroup(array &$form, FormStateInterface $form_state)
	{   
        // $element = $form_state->getTriggeringElement();
		// return $form[$element['#ajax']['wrapper']];
		$response = new AjaxResponse();
		$element = $form_state->getTriggeringElement();
		$selector = '#'. $element['#ajax']['card']; /* CSS selector for item to remove. */
        $response->addCommand(new RemoveCommand($selector));
        return $response;
	}


	public function submitForm(array &$form, FormStateInterface $form_state)
	{
		$form_state->set('ajax_called', true);
		$element = $form_state->getTriggeringElement();
		if ($element['#value'] == 'Add Card Group') {
			$groups = $form_state->getValue('card_groups');
			$form_state->setValue('card_groups', $groups + 1);
			$form_state->setRebuild(TRUE);
		} elseif ($element['#value'] == 'Remove Card Group') {
			$groups = $form_state->getValue('card_groups');
			$form_state->setValue('card_groups', $groups - 1);
			$form_state->setRebuild(TRUE);
		} elseif ($element['#value'] == 'Add Card') {
			$cards_in_group = $form_state->getValue($element['#ajax']['wrapper']);
			$cards_in_group['cards'] = $cards_in_group['cards'] + 1;
			$form_state->setValue($element['#ajax']['wrapper'], $cards_in_group);
			$form_state->setRebuild(TRUE);
		} elseif ($element['#value'] == 'Remove Card') {
			$card_input = end( explode('-', $element['#ajax']['card']) );
			$card_id = end( explode('_', $card_input) );
			$cards_in_group = $form_state->getValue($element['#ajax']['wrapper']);
			$card_count = $cards_in_group['cards'];
			// shift the cards by 1 by overriding the removed card
			for( $i = $card_id; $i < $cards_in_group['cards']-1; $i++ ) {
				$cards_in_group['card_'.$i] = $cards_in_group['card_'.($i+1)];
			}
			// remove last card from array
			unset( $cards_in_group['card_'.($card_count-1)] );
			$cards_in_group['cards'] = $card_count-1;
			// $_SESSION[$element['#ajax']['wrapper']][$element['#ajax']['card']] = 1;
			$form_state->setValue(explode("-", $element['#ajax']['wrapper'])[0], $cards_in_group);
			// save the updated data
			$config = $this->saveData($form, $form_state);
			$form_state->set('config', $config);
			$form_state->setRebuild(FALSE);
		}

	}

    public function saveData(array &$form, FormStateInterface $form_state)
	{
        // \Drupal::configFactory()->getEditable('landing_page.instructor.settings')->delete();
        $config = $this->config('landing_page.instructor.settings');
        $title = $form_state->getValue('title');
        $description = $form_state->getValue('description');
        $card_groups = $form_state->getValue('card_groups');

        $config->set('title', $title);
        $config->set('description', $description);
        $config->set('card_groups', $card_groups);

        $this->sortByWeight($form, $form_state);

		for( $i = 0; $i < $card_groups; $i++ ) {
            $card_group = $form_state->getValue('card_group_'.$i);
			$config->set('card_group_'.$i, $card_group);
		}
		$config->save();

		return $config;
        
    }

    public function sortByWeight(array &$form, FormStateInterface $form_state) {

        $card_groups = $form_state->getValue('card_groups');
        // sort for the inner cards
        for( $i = 0; $i < $card_groups; $i++ ) {
            // take all card_groups in an array
            $card_group = $form_state->getValue('card_group_'.$i);

            $cards_array = array();
            for( $j = 0; $j < $card_group['cards']; $j++ ) {
                $card = $card_group['card_'.$j];
                $cards_array[$j] = $card;
            }
            
            usort($cards_array, function($a, $b) {
                $a = $a['weight'];
                $b = $b['weight'];
                if ($a==$b) return 0;
                return ($a<$b) ? -1 : 1;
            });
            $sorted_card_groups = $card_group;
            foreach( $cards_array as $key => $value ) {
                $sorted_card_groups['card_'.$key] = $value;
            }
            // set the sorted array in form_state
            $form_state->setValue('card_group_'.$i, $sorted_card_groups);
        }

        // sort for card groups
        $card_groups_array = array();
        for( $i = 0; $i < $card_groups; $i++ ) {
            // take all card_groups in an array
            $card_group = $form_state->getValue('card_group_'.$i);
            $card_groups_array[$i] = $card_group;
        }
        usort($card_groups_array, function($a, $b) {
            $a = $a['weight'];
            $b = $b['weight'];
            if ($a==$b) return 0;
            return ($a<$b) ? -1 : 1;
        });
        foreach( $card_groups_array as $key => $value ) {
            // set the sorted array in form_state
            $form_state->setValue('card_group_'.$key, $value);
        }
    }

	public function cancel() {
		header("Refresh:0");
	}
}
