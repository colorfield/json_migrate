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

    // @todo fetch the term after creation, only a boolean is passed
    $term = Term::create(array(
      'name' => 'test',
      'vid' => 'tags',
    ))->save();

    //kint($term);

    try {
      // returns batch_process
      //$response = $migration->prepareMigration($name);
    }catch(\InvalidArgumentException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
    return $response;
  }

}
