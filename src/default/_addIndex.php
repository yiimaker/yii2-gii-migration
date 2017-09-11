<?php
/**
 * @var $indexes array
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
?>

<?php foreach ($indexes as $index): ?>
        // creates index for column `<?= $index['name'] ?>`
        $this->createIndex(
            '<?= $index['idx']  ?>',
            $this->tableName,
            '<?= $index['name'] ?>'
        );
<?php endforeach; ?>

