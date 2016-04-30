<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\DestinationEntityDebugController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\json_migrate\Entity\EntityCreator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DestinationEntityDebugController.
 *
 * @package Drupal\json_migrate\Controller
 */
class DestinationEntityDebugController extends ControllerBase
{

  protected $entityTypeManager;

  public function __construct(EntityTypeManager $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Create a single entity from an entry.
   * @todo move in PHPUnit.
   *
   * @return string
   *   Return Hello string.
   */
  public function createEntity($entity_type, $source_file) {

    // @todo fetch an entry from source file
    // @todo module exits Kint
    $ec = new EntityCreator();
    $message = '';

    switch($entity_type) {
      case 'node':
        //$file = $ec->createFileFromURI('public://logo.png');
        //$node = $ec->createNodeWithImage(array(), $file);
        $file1 = $ec->createFileFromURI('public://image1.png');
        $file2 = $ec->createFileFromURI('public://image2.png');
        $files = [$file1, $file2];
        $node = $ec->createNodeWithImages(array(), $files);
        kint($node);
        $message = $this->t('Node id %id created', array('%id' => $node->id()));
        break;
      case 'term':
        $term = $ec->createTerm('tags', 'test tag', 'test desc');
        kint($term);
        $message = $this->t('Term id %id created', array('%id' => $term->id()));
        break;
      case 'file':
        $file = $ec->createFileFromURI('public://logo.png');
        kint($file);
        $message = $this->t('File id %id created', array('%id' => $file->id()));
        break;
    }

    $output = $this->t('Debug purpose only, to be moved in PHPUnit.');
    $output .= $message;

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /**
   * Loads an entity and prints it with Kint.
   * Used to print results after migration or get the structure of the entity
   * to help defining the concrete classes (e.g. PageMigration).
   *
   * @param $entity_type
   * @param $entity_id
   * @return array
   */
  public function printDebug($entity_type, $entity_id)
  {
    $allowedEntityTypes = ['node', 'term', 'file'];
    $entity = null;
    if(in_array($entity_type, $allowedEntityTypes)) {
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
    }else {
      drupal_set_message(t('Define a type : "node", "term" or "file".'), 'error');
    }

    // @todo if module exists kint
    if(function_exists('kint')) {
      kint($entity);
    }else{
      drupal_set_message($this->t('Install the Kint (drush en devel kint) module to view the results.'), 'error');
    }

    $build = array(
      '#type' => 'markup',
      '#markup' => $this->t('Destination debug info for the @ename entity, id @eid.',
        array('@ename' => $entity_type, '@eid' => $entity_id)
      ),
    );
    return $build;
  }
}
