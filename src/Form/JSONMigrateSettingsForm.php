<?php
/**
 * @file
 * Contains \Drupal\json_migrate\Form\JSONMigrateSettingsForm
 */
namespace Drupal\json_migrate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure JSON Migrate settings for this site.
 */
class JSONMigrateSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'json_migrate_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'json_migrate.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('json_migrate.settings');
    // @todo change json path const
    $form['json_migrate_source_path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path for the JSON source'),
      '#default_value' => $config->get('source_path'),
    );
    // @todo define other const left that must be configurable

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('json_migrate.settings')
      ->set('source_path', $form_state->getValue('json_migrate_source_path'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}