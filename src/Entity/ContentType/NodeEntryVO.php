<?php

namespace Drupal\json_migrate\Entity\ContentType;

/**
 * Entry extracted from the JSON source.
 * Class NodeEntryVO
 * @package Drupal\json_migrate\Entity\ContentType
 */
class NodeEntryVO
{
  private $title;
  private $id;

  /**
   * @return mixed
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }


}