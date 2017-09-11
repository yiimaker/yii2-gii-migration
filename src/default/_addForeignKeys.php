<?php
/**
 * @var $foreignKeys array
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
?>
<?php foreach ($foreignKeys as $foreignKey): ?>
        // add foreign key for table `<?= $foreignKey['refTable'] ?>`
        $this->addForeignKey(
            '<?=$foreignKey['fk']?>',
            $this->tableName,
            '<?=$foreignKey['column']?>',
            '<?=$foreignKey['refTable']?>',
            '<?=$foreignKey['refColumn']?>',
            '<?=$foreignKey['delete']?>',
            '<?=$foreignKey['update']?>'
        );
<?php endforeach; ?>