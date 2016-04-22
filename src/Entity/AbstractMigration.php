<?php

namespace Drupal\json_migrate\Entity;

class AbstractMigration
{
  public $decodedJSON;

  /**
   * Reads and decodes JSON.
   * @return bool
   */
  public function getJSON($fileName, $path)
  {
    $result = false;
    // @todo DI instead of Drupal::service if ControllerBase available
    $path = \Drupal::service('file_system')
      ->realpath($path);
    $json = \Drupal::service('json_migrate.reader')
      ->read($path, $fileName.'.txt');

    if(empty($json['errors'])){
      $this->decodedJSON = $json['json'];
      $result = true;
    }else{
      drupal_set_message(
        t('File @name not found or readable.',
          array('@name' => $fileName.'.txt',)
        ),
        'error'
      );
    }
    return $result;
  }
}