<?php
class Address extends AppModel {

	var $name = 'Address';

	var $belongsTo = array('Customer');

	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím jméno a příjmení, nebo název společnosti.'
			)
		),
		'street' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím název ulice.'
			)
		),
		'zip' => array(
			'rule' => array('between', 5, 6),
			'message' => 'Vyplňte prosím správné PSČ.'
		),
		'city' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím název města.'
			)
		)
	);
}
?>