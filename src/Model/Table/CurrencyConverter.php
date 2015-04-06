<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class CurrencyConverterTable extends Table
{
	public function initialize(array $config)
    {
        $this->table('currency_converter');
    }
}

?>