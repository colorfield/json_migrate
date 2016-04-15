<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\ContentTypeController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\json_migrate\Model\ContentType\ContentTypeMigrationInterface;
use Drupal\json_migrate\Model\ContentType\ContentTypeMigrationFactory;

/**
 * Class ContentTypeController.
 *
 * @package Drupal\json_migrate\Controller
 */
class ContentTypeController extends ControllerBase
{
  /**
   * Migrate.
   *
   * @return string
   */
  public function migrate($name, $translation_mode)
  {
    $migration = new ContentTypeMigrationFactory();
    $response = array(
      '#type' => 'markup',
      '#markup' => $this->t('Migration for the content type @name', array('@name' => $name)),
    );
    try {
      $contentTypeMigration = $migration->createMigration($name);
      if($contentTypeMigration instanceof ContentTypeMigrationInterface) {
        // should return a list of contents to migrate to be passed to the batch
        $response = $contentTypeMigration->prepareMigration($name, $translation_mode);
      }
    }catch(\InvalidArgumentException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
    return $response;
  }

}
