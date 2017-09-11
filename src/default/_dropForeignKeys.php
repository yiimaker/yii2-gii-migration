<?php
/**
 * @var $foreignKeys array
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
?>
<?php foreach ($foreignKeys as $foreignKey): ?>
        // drop foreign key for table `<?= $foreignKey['refTable'] ?>`
        $this->dropForeignKey(
            '<?=$foreignKey['fk']?>',
            $this->tableName
        );
<?php endforeach; ?>