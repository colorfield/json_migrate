The process that is currently covered is exporting JSON as files from the legacy CMS to import them on a second step.
There is currently no web service implementation (e.g. a REST path that can be used as the JSON data source).

## Get JSON data source files

For Drupal 7, you can use node export (https://www.drupal.org/project/node_export).
Install it on your Drupal 7 website then you can get the options with `drush help ne-export`.

A basic export of a content type can be done via
`drush ne-export--type=article --file=article.txt`

I had some issues with the --file option that produced no results.
A basic workaround is to redirect the output by hand
```
touch article.txt
drush ne-export--type=article > article.txt
```

## Make your JSON files available by Drupal 8

```
mkdir drupal_8_path/sites/default/files/migrate
cp drupal_7_export_path/article.txt drupal_8_path/sites/default/files/migrate/article.txt
```

## Define a class mapping

On json_migrate/src/Model, you can define your custom migration.

1. Define the source content types on ContentTypeMigration::SOURCE_CONTENT_TYPES the keys must be the same as the file names.
2. Create a class that inherits from ContentTypeMigration and implements the MigrationInterface. Each class must implement the migrate() method.
3. Map the Migration classes to the content types on the ContentTypeMigrationFactory.