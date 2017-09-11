<?php
namespace ymaker\gii\migration\assets;
use yii\web\AssetBundle;

/**
 * Class JQueryUIAsset
 * @package ymaker\gii\migration\assets
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class JQueryUIAsset extends AssetBundle
{
     public $sourcePath = '@bower/jquery-ui';
     public $js = [
         'jquery-ui.js',
         'ui/widgets/sortable.js',
     ];
     public $depends = [
         'yii\web\JqueryAsset',
     ];
}