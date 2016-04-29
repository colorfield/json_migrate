<?php
namespace Drupal\json_migrate\Entity\ContentType;

use Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;

class PageMigration extends ContentTypeMigration
                    implements ContentTypeMigrationInterface
{

  /**
   * @inheritdoc
   */
  protected function prepareCustomNodeProperties(&$node_properties, $entry)
  {
    $node_properties['type'] = 'page';
    // @todo add custom properties
  }

  /**
   * @inheritdoc
   */
  protected function setCustomNodeTranslationProperties(Node &$node, $entry)
  {
    // @todo add fields that must be translated
  }
}