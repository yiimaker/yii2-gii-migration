Gii Migration
=============
Yii2 Gii Migration

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiimaker/yii2-gii-migration "*"
```

or add

```
"yiimaker/yii2-gii-migration": "*"
```

to the require section of your `composer.json` file.


Usage
-----
In gii config
```php
'generators' => [
    'migration' => [
        'class' => '\ymaker\gii\migration\Generator',
    ]
],
```


Fields
------

|Field                               |  Type  |       Default          |Description                                                          |
|:-----------------------------------|:------:|:----------------------:|:--------------------------------------------------------------------|
|`$migrationPath`                    |`string`|`'@console/migrations'` |the path to the folder in which the migration file will be generated |
|`$db`                               |`string`|`db`                    |connection to a database                                             |
|`$fields`                           |`array` |`none`                  |table fields                                                         |
|`$foreignKeys`                      |`array` |`none`                  |table foreign keys                                                   |
|`$useTablePrefix`                   |`bool`  |`true`                  |use table prefix                                                     |
|`$tableName`                        |`string`|`none`                  |table name                                                           |
|`$migrationName`                    |`string`|`none`                  |migration name                                                       |
|`$translationPostfix`               |`string`|`'_translation'`        |postfix for translation table name                                   |
|`$translationRefColumn`             |`string`|`'id'`                  |name for model column                                                |
|`$translationTableColumn`           |`string`|`'model_id'`            |name for translation model column                                    |
|`$translationLanguageTableName`     |`string`|`'language'`            |language table name                                                  |
|`$translationLanguageColumnRefName` |`string`|`code`                  |language column name in language table                               |
|`$translationLanguageColumnName`    |`string`|`'language'`            |language column name for translation table                           |
|`$translationLanguageColumnType`    |`string`|`string`                |language column type                                                 |
|`$translationLanguageColumnParam`   |`string`|`null`                  |language column param                                                |
