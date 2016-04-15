<?php
namespace Drupal\json_migrate\Model;


class ContentTypeMigrationFactory {
  /**
   * @var array
   */
  protected $typeList;

  public function __construct() {
    // @todo a list of other content types + define new Migration concrete classes
    $this->typeList = array(
      'article' => __NAMESPACE__ . '\ArticleMigration',
      'page' => __NAMESPACE__ . '\PageMigration',
    );
  }

  public function createMigration($type)
  {
    if (!array_key_exists($type, $this->typeList)) {
      throw new \InvalidArgumentException("$type is not valid migration class.");
    }
    $className = $this->typeList[$type];

    return new $className();
  }
}
