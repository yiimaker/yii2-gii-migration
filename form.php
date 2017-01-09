<?php
/**
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
use unclead\multipleinput\MultipleInput;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator ymaker\gii\migration\Generator */
\ymaker\gii\migration\assets\JQueryUIAsset::register($this);
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'migrationName')->hiddenInput()->label(false);
echo $form->field($generator, 'fields')->widget(MultipleInput::className(), [
    'addButtonPosition' => MultipleInput::POS_HEADER,
    'attributeOptions' => [

    ],
    'min' => 1,
    'rowOptions' => [
        'id' => 'sortable',
    ],
    'columns' => [
        [
            'name' => 'name',
            'type' => 'textInput',
            'title' => 'Name',
        ],
        [
            'name' => 'type',
            'type' => 'dropDownList',
            'title' => 'Type',
            'defaultValue' => 'string',
            'items' => $generator->types(),
        ],
        [
            'name' => 'params',
            'type' => 'textInput',
            'title' => 'Params',
        ],
        [
            'name' => 'notNull',
            'type' => 'checkbox',
            'title' => 'Not NULL',
        ],
        [
            'name' => 'defaultValue',
            'type' => 'textInput',
            'title' => 'Default value',
        ],
        [
            'name' => 'isIndex',
            'type' => 'checkbox',
            'title' => 'Index',
        ],
        [
            'name' => 'isUnique',
            'type' => 'checkbox',
            'title' => 'Unique',
        ],
        [
            'name' => 'comment',
            'type' => 'textInput',
            'title' => 'Comment',
            'enableError' => false,
        ],
        [
            'name' => 'isTranslatable',
            'type' => 'checkbox',
            'title' => 'Translatable',
            'enableError' => false,
        ],

    ]
]);

echo $form->field($generator, 'foreignKeys')->widget(MultipleInput::className(), [
    'addButtonPosition' => MultipleInput::POS_HEADER,
    'attributeOptions' => [

    ],
    'min' => 0,
    'rowOptions' => [
        'id' => 'sortable',
    ],
    'columns' => [
        [
            'name' => 'column',
            'type' => 'textInput',
            'title' => 'Column',
            'enableError' => true,
        ],
        [
            'name' => 'refTable',
            'type' => 'textInput',
            'title' => 'Ref. Table',
        ],
        [
            'name' => 'refColumn',
            'type' => 'textInput',
            'title' => 'Ref. Column',
        ],
        [
            'name' => 'delete',
            'type' => 'dropDownList',
            'title' => 'Delete',
            'items' => $generator->dataIntegrity(),
            'defaultValue' => 'SET NULL',
        ],
        [
            'name' => 'update',
            'type' => 'dropDownList',
            'title' => 'Update',
            'items' => $generator->dataIntegrity(),
            'defaultValue' => 'CASCADE',
        ],
    ]
]);

echo Collapse::widget([
    'items' => [
        [

            'label' => \Yii::t('app', 'Translation'),
            'content' => [
                $form->field($generator, 'translationPostfix'),
                $form->field($generator, 'translationTableColumn'),
                $form->field($generator, 'translationRefColumn'),

                $form->field($generator, 'translationLanguageTableName'),
                $form->field($generator, 'translationLanguageColumnName'),
                $form->field($generator, 'translationLanguageColumnRefName'),
                $form->field($generator, 'translationLanguageColumnType')->dropDownList($generator->types()),
                $form->field($generator, 'translationLanguageColumnParam'),
            ],

            'contentOptions' => [
            ]
        ],
    ]
]);

echo Collapse::widget([
    'items' => [
        [
            'label' => \Yii::t('app', 'More'),
            'content' => [
                $form->field($generator, 'migrationPath'),
                $form->field($generator, 'db'),
            ],

            'contentOptions' => [
            ]
        ],
    ]
]);


$js = <<<js
    $(function() {
        $( "#sortable" ).parent().sortable();
    } );
    jQuery('.multiple-input').on('afterAddRow', function(e){
        $( "#sortable" ).parent().sortable();
    });
js;


$this->registerJs($js, \yii\web\View::POS_END);
?>