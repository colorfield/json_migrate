<?php
namespace Drupal\json_migrate\Entity\ContentType;

use Drupal\node\Entity\Node;

class ArticleMigration extends ContentTypeMigration
                       implements ContentTypeMigrationInterface
{

  private $images;
  private $tags;

  /**
   * @inheritdoc
   */
  protected function prepareCustomNodeProperties(&$node_properties, $entry)
  {
    $node_properties['type'] = 'article';

    // @todo add other custom properties

    // attach images from uri
    $this->images = $this->attachImagesFromURI($entry->field_image);
    if(!empty($this->images)) {
      $node_properties['field_image'] = $this->images;
    }

    // terms
    $this->tags = $this->setTermReferences($entry->field_tags);
    if(!empty($this->tags)) {
      $node_properties['field_tags'] = $this->tags;
    }
  }

  /**
   * @inheritdoc
   */
  protected function setCustomNodeTranslationProperties(Node &$node, $entry)
  {
    // @todo add fields that must be translated
  }
}