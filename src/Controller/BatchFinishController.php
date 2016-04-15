<?php
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;

class BatchFinishController extends ControllerBase
{

  /**
   * Finish batch.
   */
  public static function finishBatch($success, $results, $operations)
  {
    // @todo we can use here the $results for displaying further information
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
   * Merely displays a message, should be used for other purpose.
   * @return array
   */
  public function getResults()
  {
    return array(
      '#type' => 'markup',
      '#markup' => $this->t('Batch process finished'),
    );
  }
}