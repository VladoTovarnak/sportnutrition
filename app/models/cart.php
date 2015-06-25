<?php
class Cart extends AppModel {
	var $name = 'Cart';

	var $hasMany = array(
		'CartsProduct' => array(
			'className' => 'CartsProduct',
			'dependent' => true
		)
	);
	
	function get_id() {
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		// zkusim najit v databazi kosik
		// pro daneho uzivatele
		$data = $this->find(array('rand' => $this->Session->read('Config.rand'), 'userAgent' => $this->Session->read('Config.userAgent')));

		// kosik jsem v databazi nenasel,
		// musim ho zalozit
		if ( empty($data) ){
			return $this->_create();
		} else {
			return $data['Cart']['id'];
		}
	}

	function _create(){
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		$this->data['Cart']['rand'] = $this->Session->read('Config.rand');
		$this->data['Cart']['userAgent'] = $this->Session->read('Config.userAgent');
		$this->data['Cart']['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$this->save($this->data);
		return $this->getLastInsertID();
	}
	
	// cena dopravy za obsah kosiku pro daneho uzivatele
	function shippingPrice($customer_id = null) {
		/*
		 * DOPRAVA MUZE BYT ZDARMA ZE 2 DUVODU:
		 * 	1) mam v objednavce produkt z akcni kategorie "doprava zdarma"
		 * 	2) cena objednavky je nad minimalni nenulovou mez, od ktere je nejaka doprava zdarma
		 * 
		 * ZAROVEN NEMUZE MIT DOPRAVU ZDARMA VOC ZAKAZNIK ???
		 */
		
		if ($customer_id) {
			App::import('Model', 'Customer');
			$this->Customer = &new Customer;
			if ($this->Customer->is_voc($customer_id)) {
				// VOC ma cenu dopravy nastavenou v settings
				return VOC_SHIPPING_PRICE;
			}
		}
		
		// ad 1)
		// id kosiku
		$id = $this->get_id();
		// vytahnu si produkty v kosiku
		$cart_products = $this->CartsProduct->find('all', array(
			'conditions' => array('cart_id' => $id),
			'contain' => array(),
			'fields' => array('product_id')
		));
		
		foreach ($cart_products as $cart_product) {
			// pokud mam danou kategorii, kde jsou produkty zdarma, muze byt doprava zdarma
			if (FREE_SHIPPING_CATEGORY_ID) {
				// pokud je nektery produkt z kategorie "doprava zdarma", potom je postovne za objednavku zdarma
				$product_id = $cart_product['CartsProduct']['product_id'];
				if ($this->CartsProduct->Product->in_category($product_id, FREE_SHIPPING_CATEGORY_ID)) {
					return 0;
				}
			}
		}
		
		/*
		 * ad 2)
		 * zjistim nejmensi moznou cenu objednavky, od ktere je doprava zdarma (mimo osobni odber)
		 * a pokud mam objednavku alespon v dane hodnote, je mozna doprava zdarma
		 */
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		$shipping = $this->Shipping->lowestFreeShipping();
		
		// zjistim cenu zbozi v kosiku
		$total_price = $this->totalPrice();
		
		$shipping_price = 0;
		if ($total_price < $shipping['Shipping']['free']) {
			$shipping_price = $shipping['Shipping']['price'];
		}
		return $shipping_price;
	}
	
	/* vrati cenu zbozi v kosiku */
	function totalPrice($id = null) {
		if (!$id) {
			// id kosiku
			$id = $this->get_id();
		}
		// vytahnu si vsechny produkty z db a spoctu si to
		$carts_products = $this->CartsProduct->find('all', array(
			'conditions' => array('cart_id' => $id),
			'contain' => array(),
			'fields' => array('quantity', 'price_with_dph')
		));
			
		$total_price = 0;
		foreach ($carts_products as $carts_product) {
			$total_price += $carts_product['CartsProduct']['price_with_dph'] * $carts_product['CartsProduct']['quantity'];
		}
	
		return $total_price;
	}
}
?>