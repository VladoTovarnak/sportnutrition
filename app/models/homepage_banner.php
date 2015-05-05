<?php
class HomepageBanner extends AppModel {
	var $name = 'HomepageBanner';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);
	
	var $validate = array(
		'image' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán obrázek banneru'
			)
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán popis banneru'
			)		
		),
		'url' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadána URL banneru'
			)
		)
	);
	
	var $folder = 'images/hp-banner';
}
?>