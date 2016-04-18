<?php

/**
 * @file
 * Node migration result Value Object.
 */

namespace Drupal\json_migrate\Model\ContentType;

use Drupal\json_migrate\Model\MigrationResultVOInterface;
use Drupal\node\Entity\Node;

class NodeMigrationResultVO implements MigrationResultVOInterface
{
  private $error;
  private $source_node;
  private $has_translations = false;
  private $translated_nodes;

  /**
   * @return mixed
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * @param mixed $errors
   */
  public function setError($error)
  {
    $this->error = $error;
  }

  /**
   * @return mixed
   */
  public function getSourceNode()
  {
    return $this->source_node;
  }

  /**
   * @param Node $source_node
   */
  public function setSourceNode(Node $source_node)
  {
    $this->source_node = $source_node;
  }

  /**
   * @return bool
   */
  public function hasTranslations()
  {
    return $this->has_translations;
  }

  /**
   * @todo description
   */
  public function setTranslationMode()
  {
    $this->has_translations = true;
    $this->translated_nodes = [];
  }

  /**
   * @param Node $translated_node
   */
  public function addTranslatedNode(Node $translated_node, $source_nid)
  {
    $this->translated_nodes[$source_nid] = $translated_node;
  }

  /**
   * @return array of Node
   */
  public function getTranslatedNodes()
  {
    return $this->translated_nodes;
  }

}