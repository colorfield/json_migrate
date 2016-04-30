<?php

namespace Drupal\json_migrate\Entity\Vocabulary;
use Drupal\json_migrate\Controller\BatchController;
use Drupal\json_migrate\Entity\AbstractMigration;
use Drupal\json_migrate\Entity\MigrationInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermStorage;

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
    //drupal_set_message($entry->term_name);

    $resultVO = new TermMigrationResultVO();

    $destinationVocabularyMachineName = null;
    foreach(VocabularyMigration::$sourceVocabularies as $sourceMachineName => $vocabulary){
      if($sourceMachineName == $entry->vocabulary_machine_name){
        $destinationVocabularyMachineName = $vocabulary['destination'];
      }
    }
    if(isset($destinationVocabularyMachineName)){
      // @todo implement udpate and delete operations
      $language = 'fr'; // @toto fetch from entry
      $description = '';
      if(isset($entry->description)) {
        $description = $entry->description;
      }
      $term = Term::create([
        'vid' => $destinationVocabularyMachineName,
        'langcode' => $language,
        'name' => $entry->term_name,
        'description' => [
          'value' => $description,
          'format' => 'full_html', // @todo fetch format from entry
        ],
        'weight' => 0, // @todo weight
        //'weight' => $entry->weight,
        //'parent' => array (0),
      ]);
      $term->save();
      //\Drupal::service('path.alias_storage')->save("/taxonomy/term/" . $term->id(), "/tags/my-tag", "en");
      drupal_set_message(t('Term id %id created', array('%id' => $term->id())));

      // @todo get the saved term to populate the mapping table
      if(isset($term)) {
        // @todo should be a better way to achieve this
        $resultVO->setTerm($term);
      }
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
    drupal_set_message('Prepare migration');
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
