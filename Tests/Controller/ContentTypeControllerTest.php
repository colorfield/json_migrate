<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Tests\ContentTypeController.
 */

namespace Drupal\json_migrate\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * Provides automated tests for the json_migrate module.
 */
class ContentTypeControllerTest extends WebTestBase {

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
  public static function getInfo() {
    return array(
      'name' => "json_migrate ContentTypeController's controller functionality",
      'description' => 'Test Unit for module json_migrate and controller ContentTypeController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests json_migrate functionality.
   */
  public function testContentTypeController() {
    // Check that the basic functions of module json_migrate.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
