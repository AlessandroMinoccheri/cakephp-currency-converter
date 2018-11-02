<?php

namespace CurrencyConverter\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Validation\Validator;

class CurrencyratesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('currencyrates');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('from')
            ->maxLength('from', 5)
            ->notEmpty('from');

    	$validator
            ->scalar('to')
            ->maxLength('to', 5)
            ->notEmpty('to');

    	$validator
            ->scalar('rate')
            ->maxLength('rate', 10)
            ->notEmpty('rate');

        return $validator;
    }
}
