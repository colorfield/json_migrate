<?php
namespace Drupal\json_migrate\Model;

use Drupal\node\Entity\Node;

interface MigrationInterface
{
  function prepareMigration($sourceContentTypeMachineName,
                                   $sourceTranslationMode);

  /*
  function prepareCustomNodeProperties($entry);

  function setCustomNodeTranslationProperties(Node &$node, $entry);
  */
}