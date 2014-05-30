<?php
class OrderedProductsController extends AppController {
   var $name = 'OrderedProducts';

	function admin_delete($id){
		// musim zjistit cislo objednavky, ve ktere se tento produkt nachazi
		$this->OrderedProduct->recursive = -1;
		$order = $this->OrderedProduct->read(null, $id);

		// spocitam kolik produktu obsahuje objednavka
		$count = $this->OrderedProduct->find('count', array('conditions' => array('OrderedProduct.order_id' => $order['OrderedProduct']['order_id'])));
		if ( $count > 1 ){
			$this->OrderedProduct->delete($id, true);
			$this->Session->setFlash('Produkt byl z objednavky odstranen.');
		} else {
			$this->Session->setFlash('Objednávka obsahuje pouze jeden produkt, chcete-li smazat celou objednávku,
			učiňte tak v seznamu objednávek, nebo nejprve přidejte jiný produkt.');
		}
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $order['OrderedProduct']['order_id']));
	}
   
	function admin_edit($id = null){
		$this->OrderedProduct->Order->reCount($id);
		
		// nactu si objednavku
		$order = $this->OrderedProduct->Order->read(null, $id);
		
		// seznam stavu objednavky, kdy je povoleno editovat ji
		$allowed_edit_statuses = array(0 => '1', '2');
		
		// neni-li povoleno editovat, odeslu na detail objednavky a vypisu hlasku
		if ( !in_array($order['Order']['status_id'], $allowed_edit_statuses) AND $this->Session->read('Administrator.id') != 3 ){
			$this->Session->setFlash('Objednávka je ve stavu, který nepovoluje její editaci.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'view', $id), null, true);
		}
		
		if ( isset($this->data) ){
			switch( $this->data['OrderedProduct']['change_switch'] ){
				// pridavam produkt do objednavky
				case "add_product":
					foreach ( $this->data['OrderedProduct'] as $product ){
						if (isset($product['add_it']) && $product['add_it'] == 'přidat') {
							// zkontroluju, jestli nebyla zadana custom cena
							// a prepisu ji, pokud ano
							if ( isset($product['custom_price']) && !empty($product['custom_price']) ){
								$product['product_price_with_dph'] = $product['custom_price'];
							}
							
							// pripravim si data pro ukladani novych atributu
							$subs = array();
							// nachystam si podminku pro hledani.
							// budu chtit vedet, jestli vkladany produkt uz v objednavce neni, tzn pokud je tam produkt, tak kontrolovat,
							// jestli je tam i s danymi atributy
							$attribute_ids = array();
							if ( !empty($product['Option']) ){
								foreach ( $product['Option'] as $attribute_id ){
									$attribute_ids[] = $attribute_id;
								}
							}							
							// najdu si, vsechny produkty v objednavce, jestli uz neni v objednavce zahrnuty
							$p = $this->OrderedProduct->find('all', array(
								'conditions' => array('order_id' => $id, 'product_id' => $product['product_id'])
							));
							// jestlize v objednavce tenhle produkt uz je
							if ( count($p) > 0 ){
								// musim prekontrolovat zda jsou stejne atributy
								foreach ( $p as $p2 ){
									$n_conditions = array();
									// pokud nema atributy, nevlozi se do podminek...
									if ( !empty($attribute_ids) ){
										$n_conditions = array('attribute_id' => $attribute_ids);
									}
									$n_conditions = array_merge($n_conditions, array('ordered_product_id' => $p2['OrderedProduct']['id']));
									// ... a vyhledavani v atributech vrati nulu
									$count = $this->OrderedProduct->OrderedProductsAttribute->find('count', array(
										'conditions' => $n_conditions
									));

									if ( $count == sizeof($attribute_ids) ){
										$data = array(
											'product_quantity' => $p2['OrderedProduct']['product_quantity'] + $product['product_quantity']
										);
										// zvysuju pocet
										$this->OrderedProduct->id = $p2['OrderedProduct']['id'];
										$this->OrderedProduct->save($data, false, array('product_quantity'));
										$this->Session->setFlash('Množství produktů bylo upraveno.', REDESIGN_PATH . 'flash_success');
										$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
									}
								}
							}
						
							// sem se dostanu, jen kdyz pridavam novy produkt do objednavky
							$data = array(
								'order_id' => $id,
								'product_id' => $product['product_id'],
								'product_price_with_dph' => $product['product_price_with_dph'],
								'product_quantity' => $product['product_quantity'],
								'product_name' => $this->OrderedProduct->generate_product_name($product['product_id'])
							);
							$this->OrderedProduct->create();
							$this->OrderedProduct->save($data, false);
							
							if ( !empty($product['Option']) ){
								foreach ( $product['Option'] as $attribute_id ){
									// nachystam data
									$ordered_product_attribute = array(
										'attribute_id' => $attribute_id,
										'ordered_product_id' => $this->OrderedProduct->id
									);
									
									$this->OrderedProduct->OrderedProductsAttribute->create();
									$this->OrderedProduct->OrderedProductsAttribute->save($ordered_product_attribute, false);
								}
							}
							$this->Session->setFlash('Produkt byl přidán k objednávce.', REDESIGN_PATH . 'flash_success');
							$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
						}
					}
					die();
				break;
				case "product_query":
					$query_products = $this->OrderedProduct->Product->find('all', array(
						'conditions' => array(
							"Product.name LIKE '%%" . $this->data['OrderedProduct']['query'] . "%%'",
							'Product.active' => true
						),
						'recursive' => -1
					));
					
					$count = count($query_products);
					for ( $i = 0; $i < $count; $i++ ){
						$query_products[$i]['Subs'] = $this->OrderedProduct->Product->get_subproducts($query_products[$i]['Product']['id']);
					}
					$this->set('query_products', $query_products);
				break;
				case "price_change":
					if ( isset($this->data['OrderedProduct']['custom_price']) && !empty($this->data['OrderedProduct']['custom_price']) ){
						$this->data['OrderedProduct']['product_price_with_dph'] = $this->data['OrderedProduct']['custom_price'];
					}
					
					$this->OrderedProduct->id = $this->data['OrderedProduct']['id'];
					$this->OrderedProduct->save($this->data, false, array('product_price_with_dph'));
					// musim upravit i celkovou cenu objednavky...???
					$this->Session->setFlash('Cena produktu byla změněna.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id), null, true);

				break;
				case "quantity_change":
					$this->OrderedProduct->id = $this->data['OrderedProduct']['id'];
					$this->OrderedProduct->save($this->data, false, array('product_quantity'));
					$this->Session->setFlash('Množství bylo změněno.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id), null, true);
				break;
				case "attributes_change":
					// smazu puvodni atributy
					$this->OrderedProduct->OrderedProductsAttribute->deleteAll(array('ordered_product_id' => $this->data['OrderedProduct']['id']));

					// pripravim si data pro ukladani novych atributu
					foreach ( $this->data['OrderedProduct']['Option'] as $attribute_id ){
						// nachystam data
						$ordered_product = array(
							'OrderedProductsAttribute' => array(
								'attribute_id' => $attribute_id,
								'ordered_product_id' => $this->data['OrderedProduct']['id']
							)
						);
						unset($this->OrderedProduct->OrderedProductsAttribute->id);
						$this->OrderedProduct->OrderedProductsAttribute->save($ordered_product, false);
					}
					$this->Session->setFlash('Atributy byly změněny.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
				break;
			}
		}

		// nactu si objednane produkty -- produkty z teto objednavky
		$products = $this->OrderedProduct->find('all', array(
			'conditions' => array('OrderedProduct.order_id' => $id),
			'contain' => array(
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product',
				'Order'
			)
		));
		
		// seradim atributy podle option id, aby se vypisovaly vzdycky stejne
		foreach ($products as $key => $product) {
			if (!empty($product['OrderedProductsAttribute'])) {
				usort($product['OrderedProductsAttribute'], array('OrderedProductsController', 'sort_by_option_id'));
				$products[$key] = $product;
			}
		}
		
		$count = count($products);
		for ( $i = 0; $i < $count; $i++ ){
			// produktum pridelim jejich mozne varianty
			$products[$i]['Subs'] = $this->OrderedProduct->Product->get_subproducts($products[$i]['Product']['id']);
		}

		// vytahnu si list pro select shippings
		$shipping_choices = $this->OrderedProduct->Order->Shipping->find('list', array(
			'conditions' => array('Shipping.active' => true)
		));
		$this->set('shipping_choices', $shipping_choices);
		
		// vytahnu si list pro select payments
		$payment_choices = $this->OrderedProduct->Order->Payment->find('list');
		$this->set('payment_choices', $payment_choices);
		
		$this->set('order', $order);
		$this->set('id', $id);
		$this->set('products', $products);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	

	function sort_by_option_id($a, $b) {

		if ($a['Attribute']['option_id'] == $b['Attribute']['option_id']) {

			return 0;

		}

		if ($a['Attribute']['option_id'] < $b['Attribute']['option_id']) {

			return -1;

		}

		return 1;

	}

	
}
?>