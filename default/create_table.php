<?php
/**
 * @var $this \yii\web\View
 * @var $tableName string
 * @var $migrationName string
 * @var $db string
 * @var $fields array
 * @var $foreignKeys array
 * @var $indexes array
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
?>
<?='<?php'?>

use yii\db\Migration;
/**
* Handles the creation of table `<?=$tableName?>`.
*/
class <?=$migrationName?> extends Migration
{
    public $db = '<?= $db ?>';

    public $tableName = '<?= $tableName ?>';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
    <?php foreach ($fields as $field): ?>
        '<?=$field['name']?>' => $this-><?=$field['type']?>(<?=$field['params']?:''?>)-><?= $field['notNull'] ?'notNull': 'null' ?>()<?=$field['isUnique']?'->unique()':''?><?= !empty($field['comment']) ? "->comment('{$field['comment']}')":'' ?>,
    <?php endforeach; ?>
    ]);
<?php if (!empty($indexes)): ?>
<?=$this->render('_addIndex', [
    'indexes' => $indexes,
]);?>
<?php endif; ?>
<?php if (!empty($foreignKeys)): ?>
<?=$this->render('_addForeignKeys', [
       'foreignKeys' => $foreignKeys,
]);?>
<?php endif; ?>

    }

    public function safeDown()
    {
<?php if (!empty($indexes)): ?>
<?=$this->render('_dropIndex', [
    'indexes' => $indexes,
]);?>
<?php endif; ?>
<?php if (!empty($foreignKeys)): ?>
<?=$this->render('_dropForeignKeys', [
    'foreignKeys' => $foreignKeys,
]);?>
<?php endif; ?>
        $this->dropTable($this->tableName);
    }
}

