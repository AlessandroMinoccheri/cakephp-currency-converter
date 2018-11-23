<?php

namespace CurrencyConverter\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CurrencyratesFixture extends TestFixture {
    
    public $fields = [
        'id' => ['type' => 'integer'],
        'from_currency' => ['type' => 'string', 'null' => false, 'length' => 5],
        'to_currency' => ['type' => 'string', 'null' => false, 'length' => 5],
        'rate' => ['type' => 'float', 'null' => false, 'length' => 10],
        'created' => ['type' => 'datetime', 'null' => true],
        'modified' => ['type' => 'datetime', 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [
        [
            'id' => 1,
            'from_currency' => 'EUR',
            'to_currency' => 'GBP',
            'rate' => 0.8,
            'created' => '2018-11-01 12:00:00',
            'modified' => '2018-11-01 12:00:00'
        ],
    ];
}

?>
