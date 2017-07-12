<?php
namespace Drupal\title_override\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TitleOverrideAddForm extends FormBase {
	public function getFormID() {
		return 'title_override_form'
	}

	public function buildForm(array $form,FormStateInterface $form_state) {

	 $form['name'] = array(
    '#title' => $this->t('Name'),
    '#type' => 'textfield',
    '#default_value' => '',
    '#description' => 'The unique ID for this metatag path context rule. This must contain only lower case letters, numbers and underscores.',
    '#required' => 1,
    '#maxlength' => 255,
    '#element_validate' => Array('title_override_edit_name_validate'),
  );

  $form['submit'] = array(
  	'#type' => 'submit',
  	'#value' => t('Add and configure')
  	)

  $form['cancel'] = array(
    '#type' => 'link',
    '#title' => t('Cancel'),
    '#href' => isset($_GET['destination']) ? $_GET['destination'] : 'admin/config/user-interface/to_titles',
  );

  return $form;	
	}

public function validateForm(array &$form, FormStateInterface $form_state) {
	// Check for string identifier sanity
  if (!preg_match('!^[a-z0-9_-]+$!', $form_state->getValue('name'))) {
    $form_state->setErrorByName('name', $this->t('The name can only consist of lowercase letters, underscores, dashes, and numbers.'));
    return;
  }

  // Ensure the CTools exportables system is loaded.
  ctools_include('export');

  // Check for name collision
   $element = $form_state->getValue('name');
  if ($exists = ctools_export_crud_load('context', $element)) {
    form_error($element, t('A context with this name already exists. Please choose another name or delete the existing item before creating a new one.'));
  }
}

/**
 * Creates default context for title overrides with submitted name.
 *
 * @param unknown_type $form
 * @param unknown_type $form_state
 */
public function submitForm(array &$form.FormStateInterface $form_state) {

  $context = title_override_load_default_context();
  $context->name = $form_state->getValue('name');
  context_save($context);
  $form_state->setRedirect('admin/config/user-interface/to_titles/'.$context->name);
}
}
}