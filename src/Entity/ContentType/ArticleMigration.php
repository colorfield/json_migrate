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
  protected function prepareCustomNodeProperties($entry)
  {
    $properties = array(
      'type' => 'article',
    );
    // @todo add custom properties
    return $properties;
  }

  /**
   * @inheritdoc
   */
  protected function setCustomNodeTranslationProperties(Node &$node, $entry)
  {
    // @todo add fields that must be translated
  }
}