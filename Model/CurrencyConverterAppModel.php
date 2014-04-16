<?php
class CurrencyConverterAppModel extends AppModel {
	public $name = 'CurrencyConverter';

	public $validate = array(
		'from' => array(
			'not_empty' => array(
				'rule'=> 'notEmpty',
				'message'=> 'not empty field from'	
			)
		),
		'to' => array(
			'not_empty' => array(
				'rule'=> 'notEmpty',
				'message'=> 'not empty field to'	
			)
		),
		'rates' => array(
			'not_empty' => array(
				'rule'=> 'notEmpty',
				'message'=> 'not empty field rates'	
			)
		)
	);
}
?>