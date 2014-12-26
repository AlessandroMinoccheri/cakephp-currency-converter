<?php
class CurrencyConverterFixture extends CakeTestFixture {

    public $name = 'CurrencyConverter';

    public $fields = array(
      'id'       => array('type' => 'integer', 'key' => 'primary'),
      'from' => array('type' => 'string'),
      'to'     => array('type' => 'string'),
      'rates'     => array('type' => 'decimal'),
      'created'     => array('type' => 'datetime'),
      'modified'     => array('type' => 'datetime'),
      'indexes'  => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
    );

    public $records = array();
}
?>
