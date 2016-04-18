<?php

namespace Drupal\json_migrate\Model\Vocabulary;
use Drupal\json_migrate\Controller\BatchController;
use Drupal\json_migrate\Model\AbstractMigration;
use Drupal\json_migrate\Model\MigrationInterface;
use Drupal\json_migrate\Model\MigrationResultVOInterface;

/**
 * Class VocabularyMigration
 *
 * @package Drupal\json_migrate\Model\Vocabulary
 * @todo refactoring with ContentTypeMigration
 */
class VocabularyMigration extends AbstractMigration implements MigrationInterface
{
  const JSON_PATH = 'sites/default/files/migrate/vocabulary';

  public static $sourceVocabularies = array(
    'tags' => 'Tags',
  );

  /**
   * Queue of entries to migrate.
   * @todo compare amount of source entries and created nodes
   * @var
   */
  private $entriesToMigrate;

  private function termMigrate($entry)
  {
    $resultVO = new TermMigrationResultVO();
    // @todo implement
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
      $this->entriesToMigrate = $this->decodedJSON;
    }
    return $this->batchMigrate();
  }
}
