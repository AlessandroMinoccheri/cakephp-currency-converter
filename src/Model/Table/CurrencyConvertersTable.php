<?php

namespace CurrencyConverter\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Behavior\TimestampBehavior;

class CurrencyConvertersTable extends Table
{
	public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');

        $this->table('currency_converters');
    }
}
