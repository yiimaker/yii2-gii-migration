<?php
/**
 * @var $indexes array
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
?>

<?php foreach ($indexes as $index): ?>
        // drop index for column `<?= $index['name'] ?>`
        $this->dropIndex(
            '<?= $index['idx']  ?>',
            $this->tableName
        );
<?php endforeach; ?>

