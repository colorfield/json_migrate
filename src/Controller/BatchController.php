<?php
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\json_migrate\Entity\ContentType\ContentTypeMigration;
use Drupal\json_migrate\Entity\ContentType\NodeMigrationResultVO;
use Drupal\json_migrate\Entity\MigrationInterface;
use Drupal\json_migrate\Entity\MigrationResultVOInterface;
use Drupal\json_migrate\Entity\Vocabulary\TermMigrationResultVO;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Response;

class BatchController extends ControllerBase
{

  const OPERATION_CREATE = 0;
  const OPERATION_UPDATE = 1;
  const OPERATION_DELETE = 2;

  /**
   * Logs the migration from a node to a node into the migration table.
   *
   * @todo move in ContentTypeMigration
   * @param $entry
   * @param \Drupal\node\Entity\Node $node
   * @param $operation
   * @param null $i18n_source_nid
   * @throws \Exception
   */
  private static function logNodeMigration($entry,
                                           Node $node,
                                           $operation,
                                           $i18n_source_nid = null)
  {
    $nid = $entry->nid;
    // can be overridden by i18n that has a different node id
    if(isset($i18n_source_nid)) {
      $nid = $i18n_source_nid;
    }

    $fields = array(
      'source_nid' => (int) $nid,
      'source_uid' => (int) $entry->uid,
      'destination_nid' => (int) $node->id(),
      'language' => (string) $node->language(), // @todo fix language
      'status' => 1,
      'operation' => (int) $operation,
      'timestamp' => REQUEST_TIME,
    );

    try{
      $insert = \Drupal::database()->insert('json_migrate_node');
      //$insert = Database::getConnection('default')->insert('json_migrate_node');
      $insert->fields($fields);
      $insert->execute();
    }catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

  /**
   * Logs the migration from a term to a term into the migration table.
   *
   * @todo move in VocabularyMigration
   * @param $entry
   * @param \Drupal\taxonomy\Entity\Term $term
   * @param $operation
   */
  private static function logTermMigration($entry, Term $term, $operation)
  {
    $fields = array(
      'source_tid' => (int) $entry->term_id,
      'source_vid' => (int) $entry->vocabulary_id,
      'destination_tid' => (int) $term->id(),
      'language' => (string) 'en', // @todo set language
      'status' => 1,
      'operation' => (int) $operation,
      'timestamp' => REQUEST_TIME,
    );

    try{
      $insert = \Drupal::database()->insert('json_migrate_node');
      //$insert = Database::getConnection('default')->insert('json_migrate_node');
      $insert->fields($fields);
      $insert->execute();
    }catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

  /**
   * Processes the batch.
   *
   * @param int $amount
   *  Count of the contents.
   * @param int $operation
   *  Current operation to run the batch on.
   * @param string $entry
   *   The content to import.
   * @param $migrationHelper
   *   The helper that contains the callback method.
   * @param array $context
   *   The batch context.
   */
  public static function processBatch($amount,
                                      $operation,
                                      $entry,
                                      MigrationInterface $migrationHelper,
                                      &$context)
  {
    // cannot use dependency injection an need static call for
    // the logger and database services

    if(isset($migrationHelper)) {

      $migrationResult = $migrationHelper->batchCallback($entry);
      $itemMessage = '';

      // fail logging
      // @todo check interface
      if ($migrationResult->getError()) {
        \Drupal::logger('json_migrate')->error(
          \Drupal::translation()
            ->translate('Error importing %content. %message. Review the json_migrate_* logs table.',
              array('%content' => $entry, '%message' => $migrationResult->getError()))
        );

        // @todo log errors in table
        /*
        \Drupal::database()->insert('json_migrate_node', array(

        ));
        */

      // success logging
      }else {
        // @todo refactoring needed within the several migration helpers (node, term, user)
        // @todo create NodeEntryVO and TermEntryVO

        // node migration
        if($migrationResult instanceof NodeMigrationResultVO) {
          \Drupal::logger('json_migrate')->info(
            \Drupal::translation()
              ->translate('Imported node from source node id %source_nid.',
                array('%source_nid' => $entry->nid))
          );

          $node = $migrationResult->getSourceNode();
          $itemMessage = $entry->title;
          BatchController::logNodeMigration($entry, $node, $operation);

          if($migrationResult->hasTranslations()) {
            foreach ($migrationResult->getTranslatedNodes() as $i18n_source_nid => $node) {
              BatchController::logNodeMigration($entry, $node, $operation, $i18n_source_nid);
            }
          }

        // term migration
        }elseif($migrationResult instanceof TermMigrationResultVO) {
          \Drupal::logger('json_migrate')->info(
            \Drupal::translation()
              ->translate('Imported term from term tid %term_tid.',
                array('%term_tid' => $entry->tid))
          );

          $term = $migrationResult->getTerm();
          $itemMessage = $entry->term_name;
          // @todo logging
          //BatchController::logTermMigration($entry, $term, $operation);
        }

      }
    }else {
      drupal_set_message(t('No migration helper defined.'));
    }

    //    Store some result for post-processing in the finished callback.
    //    $context['results'][] = $content;
    $context['message'] = t('Now processing %item', array('%item' => $itemMessage));

    // @todo batch follow up
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

    //    if ($errors = $myWorkerClass->getErrors()) {
    //      if (!isset($context['results']['errors'])) {
    //        $context['results']['errors'] = array();
    //      }
    //      $context['results']['errors'] += $errors;
    //    }
  }


  public static function setBatch($itemsToProcess,
                                  MigrationInterface $migrationHelper = null)
  {
    try {
      $batch = [
        'operations' => [],
        'finished' => [BatchController::class, 'finishBatch'],
        'title' => \Drupal::translation()->translate('Migrating content'),
        'init_message' => \Drupal::translation()->translate('Starting content migration.'),
        'progress_message' => \Drupal::translation()->translate('Completed @current step of @total.'),
        'error_message' => \Drupal::translation()->translate('Content migration has encountered an error.'),
      ];
      $amount = count($itemsToProcess);
      // @todo implement update and delete operations
      $operation = BatchController::OPERATION_CREATE;
      foreach ($itemsToProcess as $entry) {
        $batch['operations'][] = [
          [BatchController::class, 'processBatch'],
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
   * Finish batch.
   */
  public static function finishBatch($success, $results, $operations)
  {
    // @todo we can use here the $results for displaying further information
    // @todo compare source / destination amount of items to confirm import
    if ($success) {
      if (!empty($results['errors'])) {
        foreach ($results['errors'] as $error) {
          drupal_set_message($error, 'error');
          \Drupal::logger('json_migrate')->error($error);
        }
        drupal_set_message(\Drupal::translation()
          ->translate('The content was imported with errors.'), 'warning');
      }
      else {
        drupal_set_message(\Drupal::translation()
          ->translate('The content was imported successfully.'));
      }
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $message = \Drupal::translation()
        ->translate('An error occurred while processing %error_operation with arguments: @arguments', array(
          '%error_operation' => $error_operation[0],
          '@arguments' => print_r($error_operation[1], TRUE)
        ));
      drupal_set_message($message, 'error');
    }
  }

  /**
   * Merely displays a message, could be used for other purpose.
   * @return array
   */
  public function getResults()
  {
    return array(
      '#type' => 'markup',
      '#markup' => $this->t('Batch process finished'),
    );
  }

  /**
   * Used for batch tests, should not be called.
   * @todo remove route
   */
  public function initBatch()
  {
    return new Response(t('To be removed'));
  }
}