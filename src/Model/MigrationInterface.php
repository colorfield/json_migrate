<?php

namespace Drupal\json_migrate\Model;


interface MigrationInterface
{
  function prepareMigration($sourceMachineName, $sourceTranslationMode);
  function batchMigrate();
  function batchCallback($entry);
}