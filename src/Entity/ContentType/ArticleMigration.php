<?php
namespace Drupal\json_migrate\Entity\ContentType;

use Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;

class ArticleMigration extends ContentTypeMigration
                    implements ContentTypeMigrationInterface
{

  /**
   * @inheritdoc
   */
  protected function prepareCustomNodeProperties(&$node_properties, $entry)
  {
    $node_properties['type'] = 'article';
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