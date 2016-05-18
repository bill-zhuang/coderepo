<?php
/* @var $model_name string model name */
/* @var $table_name string table name */
/* @var $primary_id array table primary id */
/* @var $table_fields array table keys */

function field2format($field) {
    return implode('', array_map('ucfirst', explode('_', $field)));
}
echo "<?php\n";
?>

class Application_Model_DBObject_<?php echo $model_name, PHP_EOL; ?>
{
<?php foreach ($table_fields as $field) {
    echo str_repeat(' ', 4) . 'protected $_' . $field['Field'] . ' = null;' . PHP_EOL;
} ?>

    public function getSaveData()
    {
        $data = [];
<?php foreach ($table_fields as $field) { ?>
        if ($this->get<?php echo field2format($field['Field']); ?>() !== null) {
            $data['<?php echo $field['Field']; ?>'] = $this->get<?php echo field2format($field['Field']); ?>();
        }
<?php } ?>
        return $data;
    }

<?php foreach ($table_fields as $field) { ?>
    public function get<?php echo field2format($field['Field']); ?>()
    {
        return $this->_<?php echo $field['Field']; ?>;
    }

    public function set<?php echo field2format($field['Field']); ?>($<?php echo $field['Field']; ?>)
    {
        $this->_<?php echo $field['Field']; ?> = $<?php echo $field['Field']; ?>;
        return $this;
    }
<?php echo PHP_EOL;} ?>
}