<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\SourceDebugController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\json_migrate\JSONReader;
use Drupal\json_migrate\Entity\ContentType\ContentTypeMigration;
use Drupal\json_migrate\Entity\Vocabulary\VocabularyMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContentTypeController.
 *
 * @package Drupal\json_migrate\Controller
 */
class SourceDebugController extends ControllerBase
{

  const NUMBER_ITEMS = 5; // @todo set in configuration form

  private $jsonReader;
  private $fileSystem;

  public function __construct(FileSystem $fileSystem,
                              JSONReader $jsonReader)
  {
    $this->fileSystem = $fileSystem;
    $this->jsonReader = $jsonReader;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('file_system'),
      $container->get('json_migrate.reader')
    );
  }

  /**
   * Returns the documentation link.
   * @return mixed
   */
  public static function getDebugLink($entity_type, $bundle)
  {
    // @todo check if file exists, if not mention it and disable the radio
    $path = '/admin/migrate/json/debug/source/'
      .$entity_type.'/'.$bundle.'/'.SourceDebugController::NUMBER_ITEMS;
    $url = Url::fromUri('internal:'.$path);
    $link = Link::fromTextAndUrl(t('View JSON source'), $url);
    $link = $link->toRenderable();
    $link['#attributes'] = array('class' => array('documentation'));
    $output = render($link);
    return $output;
  }

  /**
   * Reads a JSON source and prints entries via Kint.
   *
   * @return string
   */
  public function printDebug($entity_type, $file_name, $length)
  {
    $path = '';
    // @todo improve via a factory / interface (avoid switch)
    switch($entity_type){
      case 'content-type':
        $path = $this->fileSystem->realpath(ContentTypeMigration::JSON_PATH);
        break;
      case 'vocabulary':
        // @todo
        $path = $this->fileSystem->realpath(VocabularyMigration::JSON_PATH);
        break;
      default:
        drupal_set_message(t('Define a type of file: "content-type" or "vocabulary".'), 'error');
        break;
    }

    $json = $this->jsonReader->read($path, $file_name.'.txt');
    // @todo if module exists kint
    if(function_exists('kint')) {
      if(empty($json['errors'])){
        // limit debug entries amount
        $count = 0;
        $debug = [];
        // limit applies only to content type nodes
        // @todo get rid of the terms views root property "terms"
        foreach($json['json'] as $entry) {
          if($count < (int) $length) {
            $debug[] = $entry;
            //kint($entry->field_picture->und[0]->uri);
          }else {
            break;
          }
          ++$count;
        }
        kint($debug);
      }else{
        drupal_set_message(
          $this->t('File not found or not readable. Make sure that the JSON file %file exists',
            array('%file' => $path . '/' . $file_name.'.txt')
          ),
          'error');
        kint($json['errors']);
      }
    }else{
      drupal_set_message($this->t('Install the Kint (drush en devel kint) module to view the results.'), 'error');
    }

    $build = array(
      '#type' => 'markup',
      // @todo format plural
      '#markup' => $this->t('Source debug info for the @name content type. Displaying @length entries.',
        array('@name' => $file_name, '@length' => $length)
      ),
    );
    return $build;
  }
}
