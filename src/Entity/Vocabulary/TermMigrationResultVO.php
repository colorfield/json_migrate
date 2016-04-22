<?php

namespace Drupal\json_migrate\Entity\Vocabulary;


use Drupal\json_migrate\Entity\MigrationResultVOInterface;

class TermMigrationResultVO implements MigrationResultVOInterface
{
  private $error;
  private $term;

  /**
   * @return mixed
   */
  public function getTerm()
  {
    return $this->term;
  }

  /**
   * @param mixed $sourceTerm
   */
  public function setTerm($term)
  {
    $this->term = $term;
  }

  public function getError()
  {
    return $this->error;
  }

  public function setError($error)
  {
    $this->error = $error;
  }
}