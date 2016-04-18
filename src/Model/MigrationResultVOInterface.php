<?php

namespace Drupal\json_migrate\Model;


interface MigrationResultVOInterface
{
  public function setError($error);
  public function getError();
}