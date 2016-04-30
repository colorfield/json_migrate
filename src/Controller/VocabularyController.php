<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\VocabularyController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\json_migrate\Entity\Vocabulary\VocabularyMigration;
use Drupal\taxonomy\Entity\Term;

/**
 * Class VocabularyController.
 *
 * @package Drupal\json_migrate\Controller
 */
class VocabularyController extends ControllerBase
{
  /**
   * Migrates vocabularies.
   *
   * @return string
   */
  public function migrate($name)
  {
    $migration = new VocabularyMigration();
    $response = array(
      '#type' => 'markup',
      '#markup' => $this->t('Migration for the vocabulary @name', array('@name' => $name)),
    );
    try {
      // returns batch_process
      $response = $migration->prepareMigration($name);
      //$migration->prepareMigration($name);
    }catch(\InvalidArgumentException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
    return $response;
  }
}
