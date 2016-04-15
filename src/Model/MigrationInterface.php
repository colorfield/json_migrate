<?php
namespace Drupal\json_migrate\Model;

interface MigrationInterface 
{
  public function prepareMigration($sourceContentTypeMachineName,
                                   $sourceTranslationMode);
}