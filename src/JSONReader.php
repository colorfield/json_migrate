<?php

namespace Drupal\json_migrate;

/**
 * Class JSONReader.
 *
 * @package Drupal\json_migrate
 */
class JSONReader
{

  private $source;

  /**
   * Constructor.
   */
  public function __construct()
  {

  }

  /**
   * Reads and decodes JSON from a file.
   * @todo review Drupal api for that or get inspired from
   * https://www.drupal.org/project/migrate_source_json
   * https://api.drupal.org/api/drupal/vendor%21zendframework%21zend-feed%21src%21Reader%21Reader.php/class/Reader/8
   * @param $real_path
   * @param $file_name
   * @return array
   */
  public function read($real_path, $file_name)
  {
    $result = [];
    $result['errors'] = [];
    $this->source = $real_path . DIRECTORY_SEPARATOR . $file_name;
    
    try{
      if(file_exists($this->source)) {
        $handle = fopen($this->source, "r");
        $contents = fread($handle, filesize($this->source));
        fclose($handle);
        // @todo check json validity
        $result['json'] = json_decode($contents);
      }else{
        $result['errors'][] = 'File not found.';
      }
    }catch (\Exception $e){
      // @todo foreach on exceptions
      $result['errors'][] = $e->getMessage();
    }
    return $result;
  }
}
