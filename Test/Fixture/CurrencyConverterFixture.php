<?php
class CurrencyConverterFixture extends CakeTestFixture {

    public $fields = array(
      'id'       => array('type' => 'integer', 'key' => 'primary'),
      'from' => array('type' => 'string'),
      'to'     => array('type' => 'string'),
      'rates'     => array('type' => 'decimal'),
      'created'     => array('type' => 'datetime'),
      'modified'     => array('type' => 'datetime'),
      'indexes'  => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
    );

    /*public $records = array(
        array(
          'id' => 1,
          'from' => 'EUR',
          'to' => 'GBP',
          'rates' => '2',
          'created' => '2014-03-18 10:39:23',
          'modified' => '2014-03-18 10:39:23'
        ),
        array(
          'id' => 2,
          'from' => 'GBP',
          'to' => 'EUR',
          'rates' => '1',
          'created' => '2014-03-18 10:39:23',
          'modified' => '2014-03-18 10:39:23'
        )
    );*/
}
?>
