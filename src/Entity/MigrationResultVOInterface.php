<?php

namespace Drupal\json_migrate\Entity;


interface MigrationResultVOInterface
{
  public function setError($error);
  public function getError();
}