<?php
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
	
	var $GP_shipping_id = 28;
	
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

	function get_cost($id, $payment_id, $order_total, $is_voc = false) {
		// pokud je doprava po CR (mimo osobniho odberu) a soucasne je zakaznik VOC, je cena vzdy 95 KC
		if ($is_voc && in_array($id, array(2, 3, 7, 18, 14, 28))) {
			$price = VOC_SHIPPING_PRICE;
//			$price = 0;
		} else {
			$shipping = $this->find('first', array(
				'conditions' => array('Shipping.id' => $id),
				'contain' => array(),
				'fields' => array('Shipping.id', 'Shipping.price', 'Shipping.free')	
			));
			
			$price = $shipping['Shipping']['price'];
			
			// u sobotniho doruceni (id = 20) neni doprava zdarma, pouze u objednavky nad 2000 se odecte 80,-
			if ($shipping['Shipping']['id'] == 20 && $order_total >= 2000 && !$is_voc) {
				$price = 50;
			// pokud je zvolena doprava na slovensko (id = 16) a platba prevodem (id = 2), je sleva z dopravy 70,-
			} elseif ($id == 16 && $payment_id == 2) {
				$price -= 70;
			} elseif (intval($shipping['Shipping']['free'] > 0) && $order_total > intval($shipping['Shipping']['free'])) {
				$price = 0;
			}
			
			App::import('Helper', 'Session');
			$this->Session = new SessionHelper;
			
			// cena postovneho je spocitana dle kosiku
			// zkontroluju jeste jestli tam neni kupon
			// pro dopravu zdarma nad 999 Kc
			if ( $this->Session->check("Discount") ){
				$discount = $this->Session->read("Discount");
				if ( $discount == "dpr999" ){
					// v session jsem nasel slevovy kupon na dopravu zdarma,
					// zkontroluju jestli je tam zbozi za vic nez 999 Kc
					// pokud ano, tak je doprava za nulu
					if ( $order_total > 999 ){
						$price = 0;
					}
				}
			}
			
		}
		return $price;
	}
	
	function get_tax_class_description($id) {
		$shipping = $this->find('first', array(
			'conditions' => array('Shipping.id' => $id),
			'contain' => array('TaxClass'),
			'fields' => array('Shipping.id', 'TaxClass.id', 'TaxClass.description')	
		));
		
		if (!isset($shipping['TaxClass']['description'])) {
			return 'none';
		}
		return $shipping['TaxClass']['description'];
	}
	
	function geis_point_url($session, $one_step_order = false) {
		$address = $session->read('Address');
		if (!$address) {
			return false;
		}
		$cust_address = $address['street'];
		if (!empty($address['street_no'])) {
			$cust_address .= ' ' . $address['street_no'];
		}
		$cust_address .= ';' . $address['city'] . ';' . $address['zip'];
		$cust_address = urlencode($cust_address);
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/rekapitulace-objednavky';
		if ($one_step_order) {
			$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/finalize';
		}
		$redirect_url = urlencode($redirect_url);
		$service_url = 'http://plugin.geispoint.cz/map.php';
		$service_url = $service_url . '?CustAddress=' . $cust_address . '&ReturnURL=' . $redirect_url;
		
		return $service_url;
	}
	
	function findBySnName($snName) {
		$shipping = $this->find('first', array(
			'conditions' => array('Shipping.sn_name' => $snName),
			'contain' => array()
		));
		
		return $shipping;
	}
	
	/*
	 * Vrati mi zpusob dopravy, ktery ma nejmensi hodnotu ceny objednavky, od kdy je doprava zdarma a zaroven ma nejnizsi cenu dopravneho
	 */
	function lowestFreeShipping() {
		$shipping = $this->find('first', array(
			'conditions' => array(
				'Shipping.free IS NOT NULL',
				'Shipping.free >' => 0,
				'Shipping.active' => true
			),
			'contain' => array(),
			'order' => array('Shipping.free' => 'asc', 'Shipping.price' => 'asc')
		));
		return $shipping;
	}

}
?>