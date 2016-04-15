<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Form\SelectContentTypeForm.
 */

namespace Drupal\json_migrate\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\json_migrate\Model\ContentTypeMigration;

/**
 * Class SelectContentTypeForm.
 *
 * @package Drupal\json_migrate\Form
 */
class SelectContentTypeForm extends FormBase {

  private function getSourceContentTypes() 
  {
    // @todo set this via config, simplify the process with files / Factory
    return ContentTypeMigration::$sourceContentTypes;
  }

  private function getSourceTranslationModes() 
  {
    return ContentTypeMigration::$sourceTranslationModes;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'select_content_type_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) 
  {
    // @todo documentation about translation modes
    $form['source_translation_mode'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Source translation mode'),
      '#options' => $this->getSourceTranslationModes(),
      '#description' => $this->t('Drupal 7 translation mode'),
      '#required' => true,
    );
    $form['source_content_type'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Source content type'),
      '#options' => $this->getSourceContentTypes(),
      '#description' => $this->t('Drupal 7 content type to migrate'),
      '#required' => true,
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Migrate'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * @inheritdoc}
   */
  /*
  public function validateForm(array &$form, FormStateInterface $form_state) 
  {
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }
  */

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) 
  {
    $completeForm = $form_state->getCompleteForm();
    $sourceTranslationMode =  $completeForm['source_translation_mode']['#value'];
    $sourceContentTypeMachineName =  $completeForm['source_content_type']['#value'];
    $form_state->setRedirect(
      'json_migrate.content_type_controller_migrate',
      array(
        'name' => $sourceContentTypeMachineName,
        'translation_mode' => $sourceTranslationMode,
      )
    );
  }

}
