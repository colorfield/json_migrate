<?php
namespace Drupal\json_migrate\Entity\ContentType;


interface ContentTypeMigrationInterface
{
  function prepareMigration($sourceContentTypeMachineName,
                                   $sourceTranslationMode);
}