<?
class Shipping extends AppModel {
	var $name = 'Shipping';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'active'
		)
	);
	
	var $belongsTo = array('TaxClass');

	var $hasMany = array('Order');
	
	var $validate = array(
		'name' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Vyplňte prosím název způsobu dopravy.'
			),
			'isUnique' => array(
				'rule' => array('isUnique', 'name'),
				'message' => 'Tento způsob dopravy již existuje! Zvolte prosím jiný název způsobu dopravy.'
			)
		),
		'price' => array(
	        'rule' => 'numeric',  
	        'message' => 'Uveďte prosím cenu za dopravu v korunách.',
	    ),
	    'free' => array(
	        'rule' => 'numeric',  
	    	'allowEmpty' => true,
	    	'message' => 'Uveďte prosím cenu objednávky v korunách, od které je doprava zdarma.',
	    )
	);
	
	function delete($id) {
		// pred "smazanim" (deaktivaci) musim dopravu presunout na konec seznamu aktivnich doprav
		while (!$this->islast($id)) {
			$this->moveDown($id);
		}
		
		$shipping = array(
			'Shipping' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		return $this->save($shipping);
	}

	function get_data($id){
		$shipping = $this->find('first', array(
			'conditions' => array('Shipping.id' => $id),
			'contain' => array(),
		));
		
		return $shipping;
	}

	function get_cost($id, $order_total){
		$shipping = $this->find('first', array(
			'conditions' => array('Shipping.id' => $id),
			'contain' => array(),
			'fields' => array('Shipping.id', 'Shipping.price', 'Shipping.free')	
		));
		
		$price = $shipping['Shipping']['price'];
		
		if (intval($shipping['Shipping']['free'] > 0) && $order_total > intval($shipping['Shipping']['free'])) {
			$price = 0;
		}
		return $price;
	}
	
	function findBySnName($snName) {
		$shipping = $this->find('first', array(
			'conditions' => array('Shipping.sn_name' => $snName),
			'contain' => array()
		));
		
		return $shipping;
	}

}
?>