<?php

namespace Drupal\json_migrate\Entity;


interface MigrationInterface
{
  function prepareMigration($sourceMachineName, $sourceTranslationMode);
  function batchMigrate();
  function batchCallback($entry);
}