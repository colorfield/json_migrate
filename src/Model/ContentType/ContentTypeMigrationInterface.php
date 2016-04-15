<?php
namespace Drupal\json_migrate\Model\ContentType;


interface ContentTypeMigrationInterface
{
  function prepareMigration($sourceContentTypeMachineName,
                                   $sourceTranslationMode);
}