<?php
/**
 * Created by PhpStorm.
 * User: spock
 * Date: 13/04/16
 * Time: 9:53
 */

namespace Drupal\json_migrate\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\json_migrate\Controller\BatchProcessController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\node\Entity\Node;

/**
 * Common helpers for content type migration
 * @todo further refactoring needed for taxonomy (e.g. EntityMigration)
 * @todo review the directory structure, e.g. extends ControllerBase
 * Class ContentTypeMigration
 * @package Drupal\json_migrate\Model
 */
abstract class ContentTypeMigration /*extends ControllerBase*/{

  const JSON_PATH = 'sites/default/files/migrate';

  /**
   * Limits to a few entries
   * @todo move in configuration form
   */
  const DEBUG = true;

  const TRANSLATION_MODE_UND = 'und';
  const TRANSLATION_MODE_I18N = 'i18n';
  const TRANSLATION_MODE_ENTITY_TRANSLATION = 'entity_translation';

  public static $sourceTranslationModes = array (
    ContentTypeMigration::TRANSLATION_MODE_UND => 'No translations',
    ContentTypeMigration::TRANSLATION_MODE_I18N => 'i18n (Internationalization)',
    ContentTypeMigration::TRANSLATION_MODE_ENTITY_TRANSLATION => 'Entity translation',
  );

  /**
   * Defines the translation mode :
   * undefined, i18n or entity translation.
   * Must be called before prepareMigration.
   * @var
   */
  private $sourceTranslationMode;

  private $concreteClass;
  private $sourceContentTypeMachineName;

  /**
   * Holds the raw result of the JSON decoded file.
   * @var
   */
  private $decodedJSON;

  private $loggerFactory;

  /**
   * Queue of entries to migrate, with optional translation structure
   * if the translation mode is not set to undefined.
   *
   * Populated before calling prepareNodeFromEntry.
   * @var array
   */
  protected $entriesToMigrate = array();

  /*
  public function __construct(LoggerChannelFactoryInterface $loggerFactory)
  {
    $this->loggerFactory = $loggerFactory;
    $this->concreteClass = get_class($this);
  }
  */

  /**
   * @inheritdoc
   */
  /*
  public static function create(ContainerInterface $container) {
    return new static($container->get('logger.factory'));
  }
  */

  /**
   * Main entry point before called before the migrate method.
   * @param $sourceContentTypeMachineName
   * @param $sourceTranslationMode
   */
  public function prepareMigration($sourceContentTypeMachineName,
                                   $sourceTranslationMode)
  {
    $this->sourceContentTypeMachineName = $sourceContentTypeMachineName;
    $this->sourceTranslationMode = $sourceTranslationMode;
    //dsm('Prepare migration for ' . $this->concreteClass);
    if($this->getJSON()) {
      // @todo review this switch with the migrate method
      switch($this->sourceTranslationMode) {
        case ContentTypeMigration::TRANSLATION_MODE_UND:
          // @todo review memory allocation
          $this->entriesToMigrate = $this->decodedJSON;
          break;
        case ContentTypeMigration::TRANSLATION_MODE_I18N:
          $this->prepareEntriesFromi18n();
          break;
        case ContentTypeMigration::TRANSLATION_MODE_ENTITY_TRANSLATION:
          // @todo
          break;
      }
    }
    return $this->batchMigrate();
  }

  /**
   * Reads and decodes JSON.
   * @return bool
   */
  private function getJSON()
  {
    $result = false;
    //dsm('Read JSON for ' . $this->sourceContentTypeMachineName);
    $realPath = \Drupal::service('file_system')
                ->realpath(ContentTypeMigration::JSON_PATH);
    $fileName = $realPath . '/' . $this->sourceContentTypeMachineName . '.txt';
    //dsm($fileName);
    // @todo review Drupal api for that or get inspired from
    // https://www.drupal.org/project/migrate_source_json
    // https://api.drupal.org/api/drupal/vendor%21zendframework%21zend-feed%21src%21Reader%21Reader.php/class/Reader/8
    if(file_exists($fileName)) {
      $handle = fopen($fileName, "r");
      $contents = fread($handle, filesize($fileName));
      fclose($handle);
      // @todo check json validity
      $this->decodedJSON = json_decode($contents);
      $result = true;
    }else{
      drupal_set_message(
        t('File @name not found',
          array('@name' => $this->sourceContentTypeMachineName.'.txt',)
        ),
        'error'
      );
    }

    return $result;
  }

  // @todo refactoring needed with translation classes (decorator, ...)

  /**
   * Retrieves JSON decoded objects for a tnid.
   * @param $tnid
   * @return array
   */
  protected function geti18nNodeTranslationsEntries($tnid)
  {
    $translations = array();
    foreach($this->decodedJSON as $entry) {
      // exclude the source nid
      if($entry->tnid == $tnid && $entry->nid != $tnid) {
        $translations[$entry->nid] = $entry;
      }
    }
    return $translations;
  }

  /**
   * Filter entries that are the source for translation.
   * nid = tnid
   * @todo use generator
   */
  protected function prepareEntriesFromi18n()
  {
    // look for entries that are the source for translation : nid = tnid
    foreach($this->decodedJSON as $entry) {
      if($entry->nid == $entry->tnid)
      $this->entriesToMigrate[$entry->nid] = $entry;
    }
    //kint($this->entriesToMigrate);
  }

  /**
   * Extracts custom node properties from a JSON entry
   * for a Drupal 8 node object.
   * Method to be overridden by concrete classes provided by the
   * ContentTypeMigrationFactory.
   * @param $entry
   * @return array
   */
  abstract protected function prepareCustomNodeProperties($entry);

  /**
   * Custom fields that must be translated (not shared amongst translations).
   * @param \Drupal\node\Entity\Node $node
   * @return mixed
   */
  abstract protected function setCustomNodeTranslationProperties(Node &$node, $entry);

  /**
   * Prepares the common node properties for an entry.
   * @param $entry
   * @return array
   */
  private function prepareCommonNodeProperties($entry)
  {
    // @todo review if we want uuid for updates
    $node_properties = array(
      'langcode' => $entry->language, // @todo remove for translations ?
      'created' => $entry->created,
      'changed' => $entry->changed,
      'status' => $entry->status, // @todo to review
      'uid' => 1, // @todo set users mapping if wished
      'title' => $entry->title,
      'body' => array(
        'summary' => $entry->body->und[0]->safe_summary,
        'value' => $entry->body->und[0]->safe_value,
        'format' => $entry->body->und[0]->format,
      ),
    );
    return $node_properties;
  }

  /**
   * Common fields that must be translated (not shared amongst translations).
   * @param \Drupal\node\Entity\Node $node
   */
  private function setCommonNodeTranslationProperties(Node &$node, $entry)
  {
    $node->title = $entry->title;
    $node->body->summary = $entry->body->und[0]->safe_summary;
    $node->body->value = $entry->body->und[0]->safe_value;
    $node->body->format = $entry->body->und[0]->format;
  }

  /**
   * Saves a node or its translation from an entry.
   * @param $entry
   * @param bool $isTranslation
   * @param \Drupal\node\Entity\Node|NULL $sourceNode
   * @return $this|\Drupal\Core\Entity\ContentEntityInterface|\Drupal\Core\Entity\EntityInterface|null|static
   */
  private function saveNodeFromEntry($entry, $isTranslation = false, Node $sourceNode = null)
  {
    //kint($entry);

    $node = null;

    if(!$isTranslation) {
      //dsm('Source');
      $node_common_properties = $this->prepareCommonNodeProperties($entry);
      $node_custom_properties = $this->prepareCustomNodeProperties($entry);
      $node_properties = array_merge($node_common_properties, $node_custom_properties);
      $node = Node::create($node_properties);
    }else {
      if(isset($sourceNode)) {
        //dsm('Translation');
        $node = $sourceNode->addTranslation($entry->language);
        // @todo review a cleaner way of defining exceptions between arrays and objects
        // keep in mind that we still need a difference between the source and the translation
        // because some fields are untranslated
        $this->setCommonNodeTranslationProperties($node, $entry);
        $this->setCustomNodeTranslationProperties($node, $entry);
      }else {
        drupal_set_message(t('No source node defined for the translation.'));
      }
    }

    //$this->loggerFactory>get(‘json_migrate’)->info($node);
    //$this->loggerFactory>get(‘json_migrate’)->warning($node);

    // @todo uncomment when done with tests
    $node->save();
    return $node;
  }

  /**
   * @todo description
   * @param $entry
   * @return array
   */
  private function i18nMigrate($entry)
  {
    $result = array();
    // @todo get and return results
    $node = $this->saveNodeFromEntry($entry);
    $translation_entries = $this->geti18nNodeTranslationsEntries($entry->tnid);
    foreach ($translation_entries as $translation_entry) {
      //kint($translation_entry);
      $translated_node = $this->saveNodeFromEntry($translation_entry, true, $node);
    }
    return $result;
  }

  /**
   * @todo description
   * @param $entry
   * @return array
   */
  private function entityTranslationMigrate($entry)
  {
    $result = array();
    // @todo to be implemented
    drupal_set_message(t('Not implemented yet.'), 'error');
    return $result;
  }

  /**
   * @todo description
   * @param $entry
   * @return array
   */
  private function noTranslationMigrate($entry)
  {
    $result = array();
    // $node = $this->saveNodeFromEntry($entry);
    // @todo to be implemented
    drupal_set_message(t('Not implemented yet.'), 'error');
    return $result;
  }

  /**
   * Batch callback.
   * @param $entry
   */
  public function batchCallback($entry)
  {
    switch($this->sourceTranslationMode) {
      case ContentTypeMigration::TRANSLATION_MODE_UND:
        $this->noTranslationMigrate($entry);
        break;
      case ContentTypeMigration::TRANSLATION_MODE_I18N:
        $this->i18nMigrate($entry);
        break;
      case ContentTypeMigration::TRANSLATION_MODE_ENTITY_TRANSLATION:
        $this->entityTranslationMigrate($entry);
        break;
    }
  }

  /**
   * @todo description
   * @todo implement within a batch
   */
  private function batchMigrate()
  {
    return BatchProcessController::setBatch($this->entriesToMigrate, $this);
  }

}