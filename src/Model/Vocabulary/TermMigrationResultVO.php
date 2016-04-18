<?php

namespace Drupal\json_migrate\Model\Vocabulary;


use Drupal\json_migrate\Model\MigrationResultVOInterface;

class TermMigrationResultVO implements MigrationResultVOInterface
{
  private $error;
  private $sourceTerm;

  /**
   * @return mixed
   */
  public function getSourceTerm()
  {
    return $this->sourceTerm;
  }

  /**
   * @param mixed $sourceTerm
   */
  public function setSourceTerm($sourceTerm)
  {
    $this->sourceTerm = $sourceTerm;
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