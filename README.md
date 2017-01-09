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