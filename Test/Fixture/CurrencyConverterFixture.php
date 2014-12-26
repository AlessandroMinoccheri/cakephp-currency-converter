<?php
class CurrencyConverterFixture extends CakeTestFixture {
    public $table = 'currency_converters';
    public $name = 'CurrencyConverter';

    public $fields = array(
      'id'        => array('type' => 'integer', 'key' => 'primary'),
      'from'      => array('type' => 'string','length' => 255, 'null' => false),
      'to'        => array('type' => 'string','length' => 255, 'null' => false),
      'rates'     => array('type' => 'string','length' => 255, 'null' => false),
      'created'   => array('type' => 'datetime'),
      'modified'  => array('type' => 'datetime'),
    );
}
?>
