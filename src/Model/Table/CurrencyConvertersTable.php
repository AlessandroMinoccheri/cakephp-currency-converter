<?php

namespace CurrencyConverter\Model\Table;

use Cake\ORM\Table;

class CurrencyConvertersTable extends Table
{
	public function initialize(array $config)
    {
        $this->table('currency_converters');
    }
}

?>