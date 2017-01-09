<?php
namespace ymaker\gii\migration;

use yii\db\Connection;
use yii\di\Instance;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\validators\RequiredValidator;

/**
 * Class Generator
 * @package ymaker\gii\migration
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class Generator extends \yii\gii\Generator
{

    public $migrationPath = '@console/migrations';
    public $db = 'db';
    public $fields;
    public $foreignKeys;
    public $useTablePrefix = true;
    public $tableName;
    public $name;
    public $migrationName;

    public $translationPostfix = '_translation';
    public $translationRefColumn = 'id';
    public $translationTableColumn = 'model_id';


    public $translationLanguageTableName = 'language';
    public $translationLanguageColumnName = 'language';
    public $translationLanguageColumnParam = null;
    public $translationLanguageColumnType = 'string';
    public $translationLanguageColumnRefName = 'code';

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->fields[] = [
            'name' => 'id',
            'type' => 'primaryKey',
            'params' => null,
            'isNull' => false,
            'defaultValue' => null,
            'isIndex' => true,
            'isUnique' => false,
            'comment' => 'ID',
            'isTranslatable' => false,
        ];
        parent::__construct($config);
        $this->migrationName = $this->migrationName?:date('ymd_His');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Generate create table migration with translation.';
    }

    public function rules()
    {
        return [
            [[
                'migrationName',

                'db',
                'tableName',
                'migrationPath',
                'useTablePrefix',
                'translationPostfix',
                'translationRefColumn',
                'translationLanguageTableName',
                'translationLanguageColumnName',
                'translationLanguageColumnType',
                'translationLanguageColumnRefName',
            ], 'required'],
            [['fields', 'foreignKeys',], 'fieldValidation'],
            [['translationLanguageColumnParam'], 'string'],
        ];
    }

    /**
     * @param $attribute
     */
    public function fieldValidation($attribute)
    {
        $requiredValidator = new RequiredValidator();
        $values = $this->$attribute;
        foreach ($values as $index => $row) {
            $error = null;
            foreach ($row as $key => $value) {
                if (in_array($key, ['name', 'type', 'column', 'refTable', 'refColumn', 'delete', 'update']))
                    if (!$requiredValidator->validate($value, $error)) {
                        $key = $attribute . '[' . $index . '][' . $key . ']';
                        $this->addError($key, $error);
                    }
            }
        }
    }
/** @inheritdoc */
    public function requiredTemplates()
    {
        return ['view.php'];
    }
public function save($files, $answers, &$results)
{
    $res = parent::save($files, $answers, $results);
    $this->migrationName = date('ymd_His');
    return $res;
}

    /**
     * @return string name of the code generator
     */
    public function getName()
    {
        return 'Create Table Migration';
    }


    /**
     * Generates the code based on the current user input and the specified code template files.
     * This is the main method that child classes should implement.
     * Please refer to [[\yii\gii\generators\controller\Generator::generate()]] as an example
     * on how to implement this method.
     * @return CodeFile[] a list of code files to be created.
     */
    public function generate()
    {
        $migrationPath = $this->getMigrationPath();
        $foreignKeys = $this->foreignKeys ?: [];
        $translatableForeignKeys = [];
        $fieldsMap = ArrayHelper::map($this->fields, 'name', 'isTranslatable');

        $translatableTableName = "{$this->tableName}{$this->translationPostfix}";

        foreach ($foreignKeys as $key => $foreignKey) {
            if ($fieldsMap[$foreignKey['column']]) {
                $translatableForeignKeys[$key] = $foreignKeys[$key];
                $translatableForeignKeys[$key]['fk'] = $this->generateForeignKeyName($translatableTableName, $foreignKey['column'], $foreignKey['refTable'], $foreignKey['refColumn']);
                $translatableForeignKeys[$key]['refTable'] = $this->generateTableName($foreignKey['refTable']);
                unset($foreignKeys[$key]);
            } else {
                $foreignKeys[$key]['fk'] = $this->generateForeignKeyName($this->tableName, $foreignKey['column'], $foreignKey['refTable'], $foreignKey['refColumn']);
                $foreignKeys[$key]['refTable'] = $this->generateTableName($foreignKey['refTable']);
            }
        }
        $files[] = $this->createTableCodeFile($this->tableName, $migrationPath, $this->getFields(false), $foreignKeys, $this->getIndexes());
        $translatableFields = $this->getFields(true);
        if (!empty($translatableFields)) {

            $IndexesField = ArrayHelper::index($this->fields, 'name');
            // add model foreign key for translation migration
            array_unshift($translatableForeignKeys, [
                'column' => $this->translationTableColumn,
                'refColumn' => $this->translationRefColumn,
                'refTable' => $this->generateTableName($this->tableName),
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'fk' => $this->generateForeignKeyName($translatableTableName, $this->translationTableColumn, $this->tableName, $this->translationRefColumn),
            ]);
            // add language foreign key for translation migration
            array_unshift($translatableForeignKeys, [
                'column' => $this->translationLanguageColumnName,
                'refColumn' => $this->translationLanguageColumnRefName,
                'refTable' => $this->generateTableName($this->translationLanguageTableName),
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'fk' => $this->generateForeignKeyName($translatableTableName, $this->translationLanguageColumnName, $this->translationLanguageTableName, $this->translationLanguageColumnRefName),
            ]);
            // add language field for translation migration
            array_unshift($translatableFields, [
                'name' => $this->translationLanguageColumnName,
                'type' => $this->getTranslationColumnType($this->translationLanguageColumnType),
                'params' => $this->translationLanguageColumnParam,
                'isNull' => false,
                'defaultValue' => null,
                'isIndex' => true,
                'isUnique' => false,
                'comment' => 'Language',
                'isTranslatable' => false,
            ]);
            // if translation ref column exist
            if (isset($IndexesField[$this->translationRefColumn])) {
                $refColumnType = $IndexesField[$this->translationRefColumn]['type'];
                array_unshift($translatableFields, [
                    'name' => $this->translationTableColumn,
                    'type' => $this->getTranslationColumnType($refColumnType),
                    'params' => null,
                    'isNull' => false,
                    'defaultValue' => null,
                    'isIndex' => true,
                    'isUnique' => false,
                    'comment' => 'Model',
                    'isTranslatable' => false,
                ]);
            }
            $files[] = $this->createTableCodeFile($translatableTableName, $migrationPath, $translatableFields, $translatableForeignKeys, $this->getIndexes(true));
        }
        return $files;
    }

    /**
     * return correct column type for translation migration
     * @param $type
     * @return string
     */
    protected function getTranslationColumnType($type)
    {
        $fieldType = $type;
        if (strcasecmp($fieldType, 'primaryKey') == 0) {
            $fieldType = 'integer';
        } elseif (strcasecmp($fieldType, 'bigPrimaryKey') == 0) {
            $fieldType = 'bigInteger';
        }
        return $fieldType;
    }

    /**
     * index fields
     * @param bool $translatable get only translation fields
     * @return array
     */
    public function getFields($translatable = false)
    {
        return array_filter($this->fields, function ($v) use ($translatable) {
            return $v['isTranslatable'] == $translatable;
        });
    }

    /**
     * generate fk name
     * @param $tableName
     * @param $column
     * @param $refTableName
     * @param $refColumn
     * @return string fk name
     */
    public function generateForeignKeyName($tableName, $column, $refTableName, $refColumn)
    {
        return implode('-', ['fk', $tableName, $column, $refTableName, $refColumn]);
    }

    /**
     * @param $tableName
     * @param $migrationPath
     * @param $fields
     * @param array $foreignKeys
     * @param array $indexes
     * @return CodeFile
     */
    public function createTableCodeFile($tableName, $migrationPath, $fields, $foreignKeys = [], $indexes = [])
    {
        $migrationName = $this->getMigrationName($tableName);
        $tableName = $this->generateTableName($tableName);

        $format = "%s/%s.php";

        return new CodeFile(
            sprintf($format, $migrationPath, $migrationName),
            $this->render('create_table.php', [
                'migrationName' => $migrationName,
                'tableName' => $tableName,
                'fields' => $fields,
                'foreignKeys' => $foreignKeys,
                'indexes' => $indexes,
                'db' => $this->db,
            ])
        );
    }

    /**
     * get index fields
     * @param bool $translatable get only translation index
     * @return array
     */
    public function getIndexes($translatable = false)
    {
        $indexes = array_filter($this->fields, function ($v) use ($translatable) {
            $isIndex = $v['isIndex'];
            $isTranslatable = $v['isTranslatable'];

            if ($translatable) {
                return $isIndex && $isTranslatable;
            }
            return $isIndex && !$isTranslatable;
        });
        foreach ($indexes as $key => $index) {
            $indexes[$key]['idx'] = implode('-', ['idx', $this->tableName, $index['name']]);
        }
        return $indexes ?: [];
    }

    /**
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return \Yii::getAlias($this->migrationPath);
    }

    /**
     * generate migration name
     * @param $table
     * @return string
     */
    protected function getMigrationName($table)
    {
        $dateTime = $this->migrationName;
        return "m{$dateTime}_{$table}";
    }

    /**
     * use table prefix
     * @param $tableName
     * @return string
     */
    protected function generateTableName($tableName)
    {
        return $this->useTablePrefix ? "{{%$tableName}}" : $tableName;
    }


    /**
     * @return array column types
     */
    public function types()
    {
        return [
            'primaryKey' => 'Primary Key',
            'bigPrimaryKey' => 'Big Primary Key',
            'char' => 'Char',
            'string' => 'String',
            'text' => 'Text',
            'smallInteger' => 'Smallint',
            'integer' => 'Integer',
            'bigInteger' => 'Bigint',
            'float' => 'Float',
            'double' => 'Double',
            'decimal' => 'Decimal',
            'dateTime' => 'Datetime',
            'timestamp' => 'Timestamp',
            'time' => 'Time',
            'date' => 'Date',
            'binary' => 'Binary',
            'boolean' => 'Boolean',
            'money' => 'Money',
        ];
    }

    /**
     * @return array data ingerety for foreign key
     */
    public function dataIntegrity()
    {
        return [
            'CASCADE' => 'CASCADE',
            'SET NULL' => 'SET NULL',
            'RESTRICT' => 'RESTRICT',
            'SET DEFAULT' => 'SET DEFAULT',
            'NO ACTION' => 'NO ACTION',
        ];
    }
}