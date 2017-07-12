<?php
namespace Drupal\title_override\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TitleOverrideEditForm extends FormBase {
	public function getFormID() {
		return 'title_override_form'
	}

	public function buildForm(array $form,FormStateInterface $form_state) {
   $form_state['to_title_override']['context'] = $context;
  $form = array();
  $form['paths'] = array(
    '#title' => 'Path',
    '#description' => 'Set this title context when any of the paths above match the page path. Put each path on a separate line. You can use the <code>*</code> character (asterisk) as a wildcard and the <code>~</code> character (tilde) to exclude one or more paths. Use &lt;front&gt; for the site front page.',
    '#type' => 'textarea',
    '#default_value' => isset($context->conditions['path']['values']) ? html_entity_decode(implode('&#13;&#10;', $context->conditions['path']['values'])) : '',
    '#required' => 1,
    '#weight' => -100,
  );
  
	 $form['title'] = array(
    '#title' => t('Title tag'),
    '#description' => t('Overrides the H1 title'),
    '#type' => 'textfield',
    '#maxlength' => 400,
    '#default_value' => isset($context->reactions['title_override']['title']) ? $context->reactions['title_override']['title'] : '',
  );

  $form['actions']['#type'] = 'actions';
  $form['actions']['save'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );
  $form['actions']['cancel'] = array(
    '#type' => 'submit',
    '#value' => t('Cancel'),
    '#submit' => array('title_override_config_edit_form_cancel_submit'),
    '#limit_validation_errors' => array(),
  );
  $form['#submit'][] = 'title_override_config_edit_form_submit';

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