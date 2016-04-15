<?php
/**
 * @file
 * Contains \Drupal\json_migrate\Controller\ContentTypeController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\json_migrate\Model\MigrationInterface;
use Drupal\json_migrate\Model\ContentTypeMigrationFactory;

/**
 * Class ContentTypeController.
 *
 * @package Drupal\json_migrate\Controller
 */
class ContentTypeController extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager, Connection $database)
  {
    $this->entity_type_manager = $entity_type_manager;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * Migrate.
   *
   * @return string
   */
  public function migrate($name, $translation_mode)
  {
    $migration = new ContentTypeMigrationFactory();
    try {
      $contentTypeMigration = $migration->createMigration($name);
      if($contentTypeMigration instanceof MigrationInterface) {
        // should return a list of contents to migrate to be passed to the batch
        $contentTypeMigration->prepareMigration($name, $translation_mode);
      }
    }catch(\InvalidArgumentException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

    return array(
        '#type' => 'markup',
        '#markup' => $this->t('Migration for the content type @name', array('@name' => $name)),
    );
  }

}
