<?php

namespace Drupal\json_migrate\Entity\Vocabulary;

/**
 * Entry extracted from the JSON source.
 * Class TermEntryVO
 * @package Drupal\json_migrate\Entity\Vocabulary
 */
class TermEntryVO
{
  private $name;
  private $id;
  private $description; // @todo
  private $vocabularyId;
  private $vocabularyMachineName;

  /**
   * @return mixed
   */
  public function getVocabularyId()
  {
    return $this->vocabularyId;
  }

  /**
   * @param mixed $vocabularyId
   */
  public function setVocabularyId($vocabularyId)
  {
    $this->vocabularyId = $vocabularyId;
  }

  /**
   * @return mixed
   */
  public function getVocabularyMachineName()
  {
    return $this->vocabularyMachineName;
  }

  /**
   * @param mixed $vocabularyMachineName
   */
  public function setVocabularyMachineName($vocabularyMachineName)
  {
    $this->vocabularyMachineName = $vocabularyMachineName;
  }
  private $weight;

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
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

  /**
   * @return mixed
   */
  public function getWeight()
  {
    return $this->weight;
  }

  /**
   * @param mixed $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }

}