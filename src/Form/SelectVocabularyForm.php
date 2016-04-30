<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Form\SelectVocabularyForm.
 */

namespace Drupal\json_migrate\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\json_migrate\Controller\JSONMigrateController;
use Drupal\json_migrate\Controller\SourceDebugController;
use Drupal\json_migrate\Entity\Vocabulary\VocabularyMigration;

/**
 * Class SelectContentTypeForm.
 *
 * @package Drupal\json_migrate\Form
 */
class SelectVocabularyForm extends FormBase {

  private function getSourceVocabularies()
  {
    $vocabularies = VocabularyMigration::$sourceVocabularies;
    $result = [];
    // append source debug link
    foreach ($vocabularies as $key => $vocabulary) {
      $result[$key] = $vocabulary['title'] . ' - '
        . SourceDebugController::getDebugLink('vocabulary', $key);
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'select_vocabulary_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) 
  {
    $form['source_vocabulary'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Source vocabulary machine name'),
      '#options' => $this->getSourceVocabularies(),
      '#description' => $this->t('Drupal 7 vocabularies to migrate.')
                        . ' ' . JSONMigrateController::getDocumentationLink(),
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) 
  {
    $completeForm = $form_state->getCompleteForm();
    $sourceVocabularyMachineName =  $completeForm['source_vocabulary']['#value'];
    $form_state->setRedirect(
      'json_migrate.vocabulary_migrate',
      array(
        'name' => $sourceVocabularyMachineName,
      )
    );
  }

}
