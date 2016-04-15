<?php
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\json_migrate\Model\ContentTypeMigration;

class BatchProcessController extends ControllerBase
{

  const OPERATION_CREATE = 0;
  const OPERATION_UPDATE = 1;
  const OPERATION_DELETE = 2;

  /**
   * Processes the batch.
   *
   * @param int $amount
   *  Count of the contents.
   * @param int $operation
   *  Current operation to run the batch on.
   * @param string $content
   *   The content to import.
   * @param array $context
   *   The batch context.
   */
  public static function processBatch($amount,
                                      $operation,
                                      $entry,
                                      ContentTypeMigration $migrationHelper,
                                      &$context)
  {

    if(isset($migrationHelper)) {
      $migrationHelper->batchCallback($entry);
    }

    // cannot use dependency injection an need static call from a static context
    \Drupal::logger('json_migrate')->info(
      \Drupal::translation()
        ->translate('Imported content %content', array('%content' => $entry))
    );
    //    review e.g. \Drupal\migrate_drupal_ui\MigrateUpgradeRunBatch
    //    if (!isset($context['sandbox']['current'])) {
    //      $context['sandbox']['current_id'] = $content->id; // @todo get id, e.g. nid
    //      $context['sandbox']['max'] = $amount;
    //      $context['sandbox']['num_processed'] = 0;
    //      $context['sandbox']['messages'] = [];
    //      $context['results']['failures'] = 0;
    //      $context['results']['successes'] = 0;
    //      $context['results']['operation'] = $operation;
    //    }
    //    else {
    //      $context['sandbox']['current_id'] = $content->id; // @todo get id, e.g. nid
    //      $context['sandbox']['num_processed']++;
    //      $context['results']['failures'] = 0; // @todo fetched from the result
    //      $context['sandbox']['messages'][] = ''; // @todo optional message
    //      $context['results']['successes']++; // @todo fetched from the result
    //    }

    //    Store some result for post-processing in the finished callback.
    //    $context['results'][] = $content;
    //    $context['message'] = t('Now processing %node', array('%node' => $content));

    //    if ($errors = $myWorkerClass->getErrors()) {
    //      if (!isset($context['results']['errors'])) {
    //        $context['results']['errors'] = array();
    //      }
    //      $context['results']['errors'] += $errors;
    //    }
  }


  public static function setBatch($itemsToProcess,
                                  ContentTypeMigration $migrationHelper = null)
  {
    try {
      $batch = [
        'operations' => [],
        'finished' => [BatchFinishController::class, 'finishBatch'],
        'title' => \Drupal::translation()->translate('Migrating content'),
        'init_message' => \Drupal::translation()->translate('Starting content migration.'),
        'progress_message' => \Drupal::translation()->translate('Completed @current step of @total.'),
        'error_message' => \Drupal::translation()->translate('Content migration has encountered an error.'),
      ];
      $amount = count($itemsToProcess);
      $operation = BatchProcessController::OPERATION_CREATE;
      foreach ($itemsToProcess as $entry) {
        $batch['operations'][] = [
          [BatchProcessController::class, 'processBatch'],
          [$amount, $operation, $entry, $migrationHelper]
        ];
      }
      batch_set($batch);
    } // @todo custom exception
    catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    $url = Url::fromRoute('json_migrate.batch_finish');
    return batch_process($url);
  }

  /**
   * Used for batch tests, should not directly be called.
   * @todo refactoring needed
   */
  public function initBatch()
  {
    // a list of contents to process
    $contentToMigrate = array(
      1,
      2,
      3,
      4,
      5
    );
    return BatchProcessController::setBatch($contentToMigrate);
  }
}