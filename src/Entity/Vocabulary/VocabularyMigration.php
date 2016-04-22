<?php

namespace Drupal\json_migrate\Entity\Vocabulary;
use Drupal\json_migrate\Controller\BatchController;
use Drupal\json_migrate\Entity\AbstractMigration;
use Drupal\json_migrate\Entity\MigrationInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Class VocabularyMigration
 *
 * @package Drupal\json_migrate\Entity\Vocabulary
 * @todo refactoring with ContentTypeMigration
 */
class VocabularyMigration extends AbstractMigration implements MigrationInterface
{
  const JSON_PATH = 'sites/default/files/migrate/vocabulary';

  /**
   * Source machine name, title and destination machine name vocabulary.
   * @var array
   */
  public static $sourceVocabularies = array(
    'tags' => array(
      'title' => 'Tags', // title only set for admin display
      'destination' => 'tags', // opportunity to change de destination machine name
    ),
  );

  /**
   * Queue of entries to migrate.
   * @todo compare amount of source entries and created nodes
   * @var
   */
  private $entriesToMigrate;

  // @todo use value objects
  private function termMigrate($entry)
  {
    $resultVO = new TermMigrationResultVO();
    $destinationVocabularyMachineName = null;
    foreach(VocabularyMigration::$sourceVocabularies as $sourceMachineName => $vocabulary){
      if($sourceMachineName == $entry->vocabulary_machine_name){
        $destinationVocabularyMachineName = $vocabulary['destination'];
      }
    }
    if(isset($destinationVocabularyMachineName)){
      // @todo implement udpate and delete operations
      $term = Term::create(array(
        'name' => $entry->term_name,
        'vid' => $destinationVocabularyMachineName,
        'description' => [
          'value' => $entry->description,
          'format' => 'full_html',
        ],
        'weight' => $entry->weight,
      ))->save();
      $resultVO->setTerm($term);
    }else{
      $resultVO->setError(t('No destination vocabulary found.'));
    }
    return $resultVO;
  }

  /**
   * Batch callback.
   * @param $entry
   * @return TermMigrationResultVO
   */
  public function batchCallback($entry)
  {
    $result = $this->termMigrate($entry);
    return $result;
  }

  /**
   * Passes the source entries to a batch process.
   */
  public function batchMigrate()
  {
    return BatchController::setBatch($this->entriesToMigrate, $this);
  }

  /**
   * Main entry point.
   * @param $sourceContentTypeMachineName
   */
  public function prepareMigration($sourceMachineName,
                                   $sourceTranslationMode = null)
  {
    if($this->getJSON($sourceMachineName, VocabularyMigration::JSON_PATH)) {
      // @todo review memory allocation
      // @todo get rid of the 'terms' root + 'term' wrapper from the view export
      foreach($this->decodedJSON->terms as $term) {
        $this->entriesToMigrate[] = $term->term;
      }
    }
    return $this->batchMigrate();
  }
}
