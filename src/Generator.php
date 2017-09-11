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

    /**
     * @var string the path to the folder in which the migration file will be generated
     */
    public $migrationPath = '@console/migrations';
    /**
     * @var string connection to a database
     */
    public $db = 'db';
    /**
     * @var array table fields
     */
    public $fields;
    /**
     * @var array table foreign keys
     */
    public $foreignKeys;
    /**
     * @var bool use table prefix
     */
    public $useTablePrefix = true;
    /**
     * @var string table name
     */
    public $tableName;
    /**
     * @var string migration name
     */
    public $migrationName;

    /**
     * @var string postfix for translation table name
     */
    public $translationPostfix = '_translation';
    /**
     * @var string name for model column
     */
    public $translationRefColumn = 'id';
    /**
     * @var string name for translation model column
     */
    public $translationTableColumn = 'model_id';

    /**
     * @var string language table name
     */
    public $translationLanguageTableName = 'language';
    /**
     * @var string language column name in language table
     */
    public $translationLanguageColumnRefName = 'code';
    /**
     * @var string language column name for translation table
     */

    public $translationLanguageColumnName = 'language';
    /**
     * @var string language column type
     */
    public $translationLanguageColumnType = 'string';
    /**
     * @var mixed language column param
     */
    public $translationLanguageColumnParam = null;

    public function hints()
    {
        return [
            'migrationPath' => 'The path to the folder in which the migration file will be generated',
            'db' => 'Connection to a database',
            'tableName' => 'This is the name of the DB table',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class will return the table name as <code>{{%post}}</code>.',

            'translationPostfix' => 'Postfix for translation table name. <code>{$tableName}_{$translationPostfix}</code>',
            'translationRefColumn' => 'Name for model column',
            'translationTableColumn' => 'Name for translation model column',
            'translationLanguageTableName' => 'This is the name of the Language table',
            'translationLanguageColumnRefName' => 'Language column name in language table',
            'translationLanguageColumnName' => 'Language column name for translation table',
            'translationLanguageColumnType' => 'Language column type in translation table',
            'translationLanguageColumnParam' => 'Language column param in language table',
        ];
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (!isset($this->fields)) {
            $this->fields[] = [
                'name' => 'id',
                'type' => 'primaryKey',
                'params' => null,
                'notNull' => true,
                'defaultValue' => null,
                'isIndex' => true,
                'isUnique' => false,
                'comment' => 'ID',
                'isTranslatable' => false,
            ];
        }
        $this->migrationName = $this->migrationName ?: date('ymd_His');
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
            [['foreignKeys',], 'columnExistValidation'],
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

    public function columnExistValidation($attribute)
    {
        $raws = $this->$attribute;
        foreach ($raws as $index => $raw) {
            $rawColumn = $raw['column'];
            if (!in_array($rawColumn, $this->getFieldNames())) {
                $this->addError("{$attribute}[$index][column]", "\"$rawColumn\" can not declared in fields");
        }

        }
    }

    protected function getFieldNames()
    {
        return ArrayHelper::getColumn($this->fields, 'name');
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

        $tableName = $this->tableName;
        $translatableTableName = "{$tableName}{$this->translationPostfix}";

        $fieldsMap = ArrayHelper::map($this->fields, 'name', 'isTranslatable');

        foreach ($foreignKeys as $key => $foreignKey) {
            if (key_exists($foreignKey['column'], $fieldsMap)) {
                if ($fieldsMap[$foreignKey['column']]) {
                    $translatableForeignKeys[$key] = $foreignKeys[$key];
                    $translatableForeignKeys[$key]['fk'] = $this->generateForeignKeyName($translatableTableName, $foreignKey['column'], $foreignKey['refTable'], $foreignKey['refColumn']);
                    $translatableForeignKeys[$key]['refTable'] = $this->generateTableName($foreignKey['refTable']);
                    unset($foreignKeys[$key]);
                } else {
                    $foreignKeys[$key]['fk'] = $this->generateForeignKeyName($tableName, $foreignKey['column'], $foreignKey['refTable'], $foreignKey['refColumn']);
                    $foreignKeys[$key]['refTable'] = $this->generateTableName($foreignKey['refTable']);
                }
            }
        }
        $files[] = $this->createTableCodeFile($tableName, $migrationPath, $this->getFields(false), $foreignKeys, $this->getIndexes());
        // translatable migration
        $translatableFields = $this->getFields(true);
        if (!empty($translatableFields)) {

            $IndexesField = ArrayHelper::index($this->fields, 'name');
            // add model foreign key for translation migration
            $this->insertForeignKey(
                $translatableForeignKeys,
                $this->createForeignKey(
                    $translatableTableName,
                    $this->translationTableColumn,
                    $this->translationRefColumn,
                    $tableName
                ));
            // add language foreign key for translation migration
            $this->insertForeignKey(
                $translatableForeignKeys,
                $this->createForeignKey(
                    $translatableTableName,
                    $this->translationLanguageColumnName,
                    $this->translationLanguageColumnRefName,
                    $this->translationLanguageTableName
                ));

            // add language field for translation migration
            $this->insertField($translatableFields,
                $this->createField(
                    $this->translationLanguageColumnName,
                    $this->getTranslationColumnType($this->translationLanguageColumnType),
                    $this->translationLanguageColumnParam,
                    null,
                    'Language',
                    false,
                    true,
                    false,
                    false
                ));
            // if translation ref column exist
            if (isset($IndexesField[$this->translationRefColumn])) {
                $refColumnType = $IndexesField[$this->translationRefColumn]['type'];
                $this->insertField($translatableFields,
                    $this->createField(
                        $this->translationTableColumn,
                        $this->getTranslationColumnType($refColumnType),
                        null,
                        null,
                        'Model',
                        false,
                        true,
                        false,
                        false
                    ));
            }
            // add primary key
            $this->insertField($translatableFields,
                $this->createField(
                    'id',
                    'primaryKey',
                    $this->translationLanguageColumnParam,
                    null,
                    'ID',
                    false,
                    true,
                    false,
                    false
                ));
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
     * @param string $columnName
     * @param string $columnType
     * @param mixed $params
     * @param mixed $defaultValue
     * @param string $comment
     * @param bool $notNull
     * @param bool $isIndex
     * @param bool $isUnique
     * @param bool $isTranslatable
     * @return array
     */
    protected function createField($columnName, $columnType, $params, $defaultValue, $comment, $notNull = true, $isIndex = false, $isUnique = false, $isTranslatable = false)
    {
        return [
            'name' => $columnName,
            'type' => $columnType,
            'params' => $params,
            'notNull' => $notNull,
            'defaultValue' => $defaultValue,
            'isIndex' => $isIndex,
            'isUnique' => $isUnique,
            'comment' => $comment,
            'isTranslatable' => $isTranslatable,
        ];
    }

    /**
     * @param array $fields
     * @param array $field
     * @param bool $begin
     */
    protected function insertField(&$fields, $field, $begin = true)
    {
        if ($begin) {
            array_unshift($fields, $field);
        } else {
            array_push($fields, $field);
        }
    }

    /**
     * @param string $table for foreign key name
     * @param string $column
     * @param string $refColumn
     * @param string $refTable
     * @param string $delete
     * @param string $update
     * @return array
     */
    protected function createForeignKey($table, $column, $refColumn, $refTable, $delete = 'CASCADE', $update = 'CASCADE')
    {
        return [
            'column' => $column,
            'refColumn' => $refColumn,
            'refTable' => $this->generateTableName($refTable),
            'delete' => $delete,
            'update' => $update,
            'fk' => $this->generateForeignKeyName($table, $column, $refTable, $refColumn),
        ];

    }

    /**
     * @param array $foreignKeys
     * @param array $foreignKey
     * @param bool $begin
     */
    protected function insertForeignKey(&$foreignKeys, $foreignKey, $begin = true)
    {
        if ($begin) {
            array_unshift($foreignKeys, $foreignKey);
        } else {
            array_push($foreignKeys, $foreignKey);
        }
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
        return "m{$dateTime}_create_{$table}_table";
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