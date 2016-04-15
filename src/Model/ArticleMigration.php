<?php
namespace Drupal\json_migrate\Model;

use Drupal\node\Entity\Node;

class ArticleMigration extends ContentTypeMigration
                        implements MigrationInterface
{
  /**
   * @inheritdoc
   */
  protected function prepareCustomNodeProperties($entry)
  {
    $properties = array(
      'type' => 'article',
    );
    // @todo add custom properties / fields
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
