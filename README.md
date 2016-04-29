The process that is covered is exporting contents by types as JSON files (one file per content type) from the legacy CMS to import them on a second step.
There is currently no web service implementation (e.g. a REST path that can be used as the JSON data source).

## Export : get JSON data source files

### Nodes by content types

For Drupal 7, you can use node export (https://www.drupal.org/project/node_export).
Install it on your Drupal 7 website then you can get the options with `drush help ne-export`.

A basic export of a content type can be done via
`drush ne-export --type=article --file=article.txt`

I had some issues with the --file option that produces no results.
A basic workaround is to redirect the output by hand :
```
touch article.txt
drush ne-export --type=article > article.txt
```
### Terms by Vocabularies

For Drupal 7, the contrib module Views Datasource (https://www.drupal.org/project/views_datasource) provides a sub module Views JSON.
- Install Views JSON
```
drush dl views_datasource
drush en views_json
```
- Import the view in the drupal7_vocabulary_view.txt
- Go to this path on the Drupal 7 website : /admin/json/export/vocabulary/{vocabulary_machine_name}
- Save the result in drupal_8_path/sites/default/files/migrate/vocabulary/{vocabulary_machine_name}.txt
- Repeat for every needed vocabulary

## Import : make your JSON files available by Drupal 8

### Nodes by content types

```
mkdir -p drupal_8_path/sites/default/files/migrate/content-type
cp drupal_7_export_path/{content_type_name}.txt drupal_8_path/sites/default/files/migrate/content-type/{content_type_name}.txt
```

### Debug

Helpers prints the data structure with Kint.

Debugging of the json export can be done via
- admin/migrate/json/debug/source/content_type/{content_type_name}/{number_of_items_to_print}
- admin/migrate/json/debug/source/vocabulary/{vocabulary_name}/{number_of_items_to_print}

Debugging of the result can be done via
- admin/migrate/json/debug/destination/print/{entity_type}/{entity_id}

Where entity type can be node, term or file.

## Define a class mapping

The class mapping allows to define your custom migration rules.


### Vocabularies

Currently, there is very limited support for terms (no i18n and no vocabulary fields).
The migration rules takes care of the following :
- mapping of the vocabulary source machine name to the destination machine name
- logging of the term source tid and the destination tid
- weight

If hierarchy is needed, it can be obtained via the Views Tree contrib module.

Vocabularies, as references of content types should be migrated first.

1. Define the mapping between the vocabularies source and destination machine names in json_migrate/src/Entity/Vocabulary/VocabularyMigration class. The keys are used for the JSON file names.
2. Go to admin/migrate/json/admin/migrate/json/vocabularies and select the vocabulary to migrate.

### Content types

If there are entity references from a content type to another, the referenced content types should be migrated first.

1. In json_migrate/src/Entity/ContentType, create the migration classes that inherits from ContentTypeMigration and implements the MigrationInterface.
2. Define the mapping between the content types machine name and the migration classes in json_migrate/src/Entity/ContentType/ContentTypeMigrationFactory. The keys are used for the JSON file names.
3. Go to admin/migrate/json/content-types and select the translation options / content type to migrate.
