<?php

namespace CurrencyConverter\Model\Entity;

use Cake\ORM\Entity;

class CurrencyConverter extends Entity
{
	public function initialize(array $config)
    {
        $this->table('currency_converter');
    }
}

?>