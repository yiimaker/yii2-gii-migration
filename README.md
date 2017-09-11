<p align="center">
    <a href="https://github.com/yiimaker" target="_blank">
        <img src="https://avatars1.githubusercontent.com/u/24204902" height="100px">
    </a>
    <h1 align="center">Migration generator for Gii</h1>
    <br>
</p>

[![Total Downloads](https://poser.pugx.org/yiimaker/yii2-gii-migration/downloads)](https://packagist.org/packages/yiimaker/yii2-gii-migration)
[![Latest Stable Version](https://poser.pugx.org/yiimaker/yii2-gii-migration/v/stable)](https://packagist.org/packages/yiimaker/yii2-gii-migration)
[![Latest Unstable Version](https://poser.pugx.org/yiimaker/yii2-gii-migration/v/unstable)](https://packagist.org/packages/yiimaker/yii2-gii-migration)

Installation
------------
#### Install package
Run command
```
composer require yiimaker/yii2-gii-migration
```
or add
```json
"yiimaker/yii2-gii-migration": "~1.0"
```
to the require section of your composer.json.

Usage
-----
Configure generator in Gii module configuration
```php
'modules' => [
    'gii' => [
        // ...
        'generators' => [
            // ...
            'migration' => [
                'class' => \ymaker\gii\migration\Generator::class,
            ],
        ],
    ],
],
```

Fields
======

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

License
-------
[![License](https://poser.pugx.org/yiimaker/yii2-gii-migration/license)](https://packagist.org/packages/yiimaker/yii2-gii-migration)

This project is released under the terms of the BSD-3-Clause [license](LICENSE).

Copyright (c) 2017, Yii Maker