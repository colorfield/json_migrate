<?php
namespace Drupal\json_migrate\Model\ContentType;


class ContentTypeMigrationFactory {
  /**
   * @var array
   */
  protected $typeList;

  /**
   * Source content types.
   * Used for the
   * - data sources JSON file name
   * - admin form content type selection
   * - migration class factory.
   * @var array
   */
  public $sourceContentTypes;

  public function __construct()
  {
    $this->sourceContentTypes = array (
      // define here the content types and class mapping
      'article' => __NAMESPACE__ . '\ArticleMigration',
      'page' => __NAMESPACE__ . '\PageMigration',
    );
  }

  public function createMigration($type)
  {
    if (!array_key_exists($type, $this->sourceContentTypes)) {
      throw new \InvalidArgumentException("$type is not valid migration class.");
    }

    $className = $this->sourceContentTypes[$type];

    return new $className();
  }
}
