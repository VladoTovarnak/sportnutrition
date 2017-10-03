<?php
class OrdersController extends AppController {
	var $name = 'Orders';

	var $helpers = array('Form');

	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Order.created' => 'desc'
		),
	);

	function admin_index(){
		// implicitne si vyhledavam do seznamu "otevrene" statusy
		$conditions = array();
		// kdyz chci omezit vypis na urcity status
		$status = null;
		$statuses = null;
		if ( isset( $this->params['named']['status_id'] ) ){
			$status = $this->Order->Status->find('first', array(
				'conditions' => array('Status.id' => $this->params['named']['status_id']),
				'contain' => array(),
				'fields' => array('Status.id', 'Status.name')	
			));
			$conditions = array('Order.status_id' => $this->params['named']['status_id']);
		} else {
			$this->Order->Status->recursive = -1;
			$statuses = $this->Order->Status->find('all');
			foreach ( $statuses as $key => $value ){
				$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
					'conditions' => array('Order.status_id' => $statuses[$key]['Status']['id'])
				));
			}
		}
		$this->set('status', $status);
		$this->set('statuses', $statuses);
		
		$this->paginate['conditions'] = $conditions;
		$this->paginate['contain'] = array(
			'OrderedProduct' => array(
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product'
			),
			'Ordernote' => array(
				'order' => array('Ordernote.created' => 'desc'),
				'Status',
				'Administrator'
			),
			'Status',
			'Shipping',
			'Payment',
			'Customer' => array(
				'CustomerType'
			)
		);

		$this->Order->virtualFields['date'] = 'CONCAT(DATE_FORMAT(DATE(Order.created), "%d.%m.%Y"), " ", TIME(Order.created))';
		$orders = $this->paginate('Order', $conditions);
		unset($this->Order->virtualFields['date']);

		foreach ($orders as &$order) {
			// spocitam si kolik objednavek ma zakaznik celkem 
			$order['Customer']['orders_count'] = $this->Order->Customer->orders_count($order['Customer']['id']);
			foreach ($order['OrderedProduct'] as &$ordered_product) {
				if ((!isset($ordered_product['product_name']) || (empty($ordered_product['product_name']))) && isset($ordered_product['Product']['name'])) {
					$ordered_product['product_name'] = $ordered_product['Product']['name'];
				}
			}
		}
		$this->set('orders', $orders);
		
		$this->Order->virtualFields['total_vat'] = 'SUM(Order.subtotal_with_dph + Order.shipping_cost)';	

		$total_vat = $this->Order->find('first', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Order.total_vat')	
		));
		unset($this->Order->virtualFields['total_vat']);
		$total_vat = round($total_vat['Order']['total_vat']);
		$this->set('total_vat', $total_vat);
		
		$statuses_options = $this->Order->Status->find('list');
		$this->set('statuses_options', $statuses_options);

		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_view($id) {
		// nactu si data o objednavce
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id', 'OrderedProductsAttribute.ordered_product_id', 'OrderedProductsAttribute.attribute_id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.manufacturer_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.tracker_prefix', 'Shipping.tracker_postfix')
				),
				'Customer' => array(
					'fields' => array('Customer.id', 'Customer.first_name', 'Customer.last_name', 'Customer.email', 'Customer.phone')
				),
				'Status' => array(
					'fields' => array('Status.id', 'Status.name', 'Status.color'),
				), 
				'Payment' => array(
					'fields' => array('Payment.id', 'Payment.name')
				),
				'Ordernote' => array(
					'fields' => array('Ordernote.id', 'Ordernote.created', 'Ordernote.note'),
					'Status' => array(
						'fields' => array('Status.id', 'Status.name')
					),
					'Administrator' => array(
						'fields' => array('Administrator.id', 'Administrator.first_name', 'Administrator.last_name')
					)
				)	
			),
			'fields' => array('Order.id', 'Order.created', 'Order.comments', 'Order.subtotal_with_dph', 'Order.shipping_cost', 'Order.status_id', 'Order.customer_id', 'Order.customer_phone', 'Order.customer_email', 'Order.customer_name', 'Order.customer_ico', 'Order.customer_dic', 'Order.customer_street', 'Order.customer_city', 'Order.customer_zip', 'Order.customer_state', 'Order.delivery_name', 'Order.delivery_street', 'Order.delivery_city', 'Order.delivery_zip', 'Order.delivery_state', 'Order.shipping_number', 'Order.variable_symbol', 'Order.shipping_delivery_psc', 'Order.shipping_delivery_info')
		));

		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if ( empty( $order ) ){
			$this->Session->setFlash('Neexistující objednávka!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'), null, true);
		}

		// potrebuju vytahnout mozne statusy
		$statuses = $this->Order->Status->find('list');

		// predam data do view
		$this->set(compact(array('order', 'statuses', 'notes', 'manufacturers')));
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_stats() {
		$virtual_fields = array(
			'date' => 'CONCAT(Month(created), "/", Year(created))',
			'income' => 'SUM(shipping_cost + subtotal_with_dph)',
			'count' => 'COUNT(*)',
			'month' => 'Month(created)',
			'year' => 'Year(created)'
		);
		
		$this->Order->virtualFields = array_merge($this->Order->virtualFields, $virtual_fields);
		
		$no_storno_conditions = array('Order.status_id !=' => 5);
		
		$no_storno_orders = $this->Order->find('all', array(
			'conditions' => $no_storno_conditions,
			'contain' => array(),
			'fields' => array('Order.date', 'Order.income', 'Order.count'),
			'group' => array('Order.month', 'Order.year'),
			'order' => array('Order.year' => 'asc', 'Order.month.asc')
		));
		$this->set('no_storno_orders', $no_storno_orders);
		
		$no_storno_orders_sum = $this->Order->find('first', array(
			'conditions' => $no_storno_conditions,
			'contain' => array(),
			'fields' => array('Order.income', 'Order.count'),
		));
		$this->set('no_storno_orders_sum', $no_storno_orders_sum);
		
		$finished_orders_conditions = array('Order.status_id' => 4);
		
		$finished_orders = $this->Order->find('all', array(
			'conditions' => $finished_orders_conditions,
			'contain' => array(),
			'fields' => array('Order.date', 'Order.income', 'Order.count'),
			'group' => array('Order.month', 'Order.year'),
			'order' => array('Order.year' => 'asc', 'Order.month.asc')
		));
		$this->set('finished_orders', $finished_orders);
		
		$finished_orders_sum = $this->Order->find('first', array(
			'conditions' => $finished_orders_conditions,
			'contain' => array(),
			'fields' => array('Order.income', 'Order.count'),
		));
		$this->set('finished_orders_sum', $finished_orders_sum);
		
		unset($this->Order->virtualFields['date']);
		unset($this->Order->virtualFields['income']);
		unset($this->Order->virtualFields['count']);
		unset($this->Order->virtualFields['month']);
		unset($this->Order->virtualFields['year']);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_print($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána objednávka, kterou chcete vytisknout.');
			$this->redirect(array('action' => 'index'));
		}
		// nactu si data o objednavce
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id', 'OrderedProductsAttribute.ordered_product_id', 'OrderedProductsAttribute.attribute_id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.manufacturer_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.tracker_prefix', 'Shipping.tracker_postfix')
				),
				'Payment' => array(
					'fields' => array('Payment.id', 'Payment.name')
				),
			)
		));
		
		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if ( empty( $order ) ){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('action' => 'index'), null, true);
		}
		
		$this->set('order', $order);
		
		$this->layout = REDESIGN_PATH . 'print';
	}

	function admin_delete($id){
		$this->Order->delete($id, true);
		$this->Session->setFlash('Objednávka byla smazána!');
		$this->redirect(array('action' => 'index'), null, true);
	}

	function admin_edit(){
		// kontrola, zda jsou pro dany status vyzadovana nejake pole
		$valid_requested_fields = array();
		$requested_fields = $this->Order->Status->has_requested($this->data['Order']['status_id']);
		if ( !empty($requested_fields) ){
			// nejaka pole jsou vyzadovana, takze si to musim zkontrolovat
			$this->Order->recursive = -1;
			$order = $this->Order->read(null, $this->data['Order']['id']);
			
			foreach ( $requested_fields as $key => $value ){
				if ( empty($order['Order'][$key]) && empty($this->data['Order'][$key])  ){
					$valid_requested_fields[] = $value;
				}
			}
		}
		
		if ( empty($valid_requested_fields) ){

			// ukladani poznamky o zmene stavu
			// vytvorim si data pro poznamku o zmene objednavky
			$this->data['Ordernote']['administrator_id'] = $this->Session->read('Administrator.id');
			$this->data['Ordernote']['status_id'] = $this->data['Order']['status_id'];
			$this->data['Ordernote']['order_id'] = $this->data['Order']['id'];
	
			// osetrim, zda dochazi ke zmene cisla baliku,
			// pokud ne, unsetnu si cislo baliku
			if ( empty($this->data['Order']['shipping_number']) ){
				unset($this->data['Order']['shipping_number']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidáno číslo balíku: ' . $this->data['Order']['shipping_number'];
			}
			
			// osetrim, zda dochazi ke zmene variabilniho symbolu,
			// pokud ne, unsetnu si variablni symbol
			if ( empty($this->data['Order']['variable_symbol']) ){
				unset($this->data['Order']['variable_symbol']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidán variabilní symbol: ' . $this->data['Order']['variable_symbol'];
			}

			$this->Order->Ordernote->save($this->data, false);
				
			
			// zalozim si idecko, abych updatoval
			$this->Order->id = $this->data['Order']['id'];
			unset($this->data['Order']['id']);
				
			// zmena stavu			
			// ulozim bez validace
			$this->Order->save($this->data, false);

			// odeslat na mail notifikaci zakaznikovi
			$mail_result = $this->Order->Status->change_notification($this->Order->id, $this->data['Order']['status_id']);

			
			$this->Session->setFlash('Objednávka byla změněna!', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		} else {
			$message = implode("<br />", $valid_requested_fields);
			$this->Session->setFlash('Chyba při změně statusu!<br />' . $message, REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		}
	}

	function admin_edit_payment($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('payment_id'));
		$this->Session->setFlash('Způsob platby byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}
	
	function admin_edit_shipping($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('shipping_id'));
		$this->Order->reCount($id);
		$this->Session->setFlash('Způsob dopravy byl změněn.');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}
	
	function admin_edit_status() {
		$data = array(
			'success' => false,
			'message' => null	
		);
		if (!isset($_POST)) {
			$data['message'] = 'Nejsou nastavena data formuláře pro změnu stavu objednávky.';
		} else {
			if (!isset($_POST['id']) || !isset($_POST['statusId']) || !isset($_POST['variableSymbol']) || !isset($_POST['shippingNumber'])) {
				$data['message'] = 'Není nastavena informace potřebná k uložení změny stavu objednávky.';
			} else {
				$id = $_POST['id'];
				$status_id = $_POST['statusId'];
				$variable_symbol = $_POST['variableSymbol'];
				$shipping_number = $_POST['shippingNumber'];
				
				
				// kontrola, zda jsou pro dany status vyzadovana nejake pole
				$valid_requested_fields = array();
				$requested_fields = $this->Order->Status->has_requested($status_id);
				if ( !empty($requested_fields) ){
					// nejaka pole jsou vyzadovana, takze si to musim zkontrolovat
					$order = $this->Order->find('first', array(
						'conditions' => array('Order.id' => $id),
						'contain' => array(),
					));
						
					foreach ($requested_fields as $key => $value) {
						if (empty($order['Order'][$key]) && empty($$key)) {
							$valid_requested_fields[] = $value;
						}
					}
				}
				if (empty($valid_requested_fields)) {
					$order = array(
						'Order' => array(
							'id' => $id,
							'status_id' => $status_id
						),
						'Ordernote' => array(
							0 => array(
								'administrator_id' => $this->Session->read('Administrator.id'),
								'status_id' => $status_id,
							)		
						)
					);
					
					if (!empty($variable_symbol)) {
						$order['Order']['variable_symbol'] = $variable_symbol;
						// hlaska, ze je pridan variabilni symbol
						$order['Ordernote'][0]['note'] = 'přidán variabilní symbol: ' . $variable_symbol;
					}
					
					if (!empty($shipping_number)) {
						$order['Order']['shipping_number'] = $shipping_number;
						// hlaska, ze je pridano cislo baliku
						$order['Ordernote'][0]['note'] = 'přidáno číslo balíku: ' . $shipping_number;
					}

					if ($this->Order->saveAll($order)) {
						if (!$this->Order->Status->change_notification($id, $status_id)) {
							$data['message'] = 'Stav objednávky ' . $id . ' byl úspěšně upraven, ale nepodařilo se odeslat informační email zákazníkovi.';
						}
						$data['success'] = true;
						$data['message'] = 'Stav objednávky ' . $id . ' byl úspěšně upraven.';
					} else {
						$data['message'] = 'Stav objednávky ' . $id . ' se nepodařilo upravit.';
					}
				} else {
					$message = implode(" ", $valid_requested_fields);
					$data['message'] = 'Chyba při změně statusu! ' . $message;
				}
			}
		}
		echo json_encode($data);
		die();
	}
	
	/**
	 * Kontroluje stavy nedorucenych objednavek podle dopravcu.
	 *
	 */
	function admin_track(){
		$this->Order->recursive = -1;
		
		$orders = $this->Order->find('all',
			array('conditions' => array(
					// nekontroluju 4 - dorucene objednavky
					// 8 - objednavka vracena
					// 5 - storno
					// 11 - odeslano (bez cisla baliku)
					"NOT" => array(
						"status_id" => array('4', '8', '5', '11')
					),
 					"shipping_number != ''",
					//'id' => 3914
				),
				'fields' => array('id', 'shipping_id')
			)
		);

		$bad_orders = array();
		foreach( $orders as $order ){
			// rozlisit zpusob doruceni
			switch ( $order['Order']['shipping_id'] ){
				case "2":
				case "20":
				case "14":
				case "29":
					// ceska posta
					$result = $this->Order->track_cpost($order['Order']['id']);
					break;
				break;
				case "3":
				case "28":
				case "16":
					// general parcel
					$result = $this->Order->track_gparcel($order['Order']['id']);
					break;
				break;
				case "15":
				case "17":
					// DPD
					$result = $this->Order->track_dpd($order['Order']['id']);
					break;
				case "7":
				case "18":
					// PPL
					$result = $this->Order->track_ppl($order['Order']['id']);
					break;
				default:
					$result = $order['Order']['id'];
				break;
			}
			
			if ( $result !== true ){
				$bad_orders[] = $order['Order']['id'];
				if (count($bad_orders) > 10) {
					debug($bad_orders);
					die();
				}
			}
		}
		
		$this->set('bad_orders', $bad_orders);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_pohoda_view() {
		$orders_to_export = array();
		$backtrace_url = '/admin/orders';
		$flash_messages = array();
		$orders = array();
		if (isset($this->data)) {

			// je nastavena adresa, na kterou se bude po zpracovani pozadavku presmerovavat
			if (isset($this->data['Order']['backtrace_url'])) {
				$backtrace_url = $this->data['Order']['backtrace_url'];
			}
			// hledam objednavky, ktere jsem chtel exportovat
			foreach ($this->data['Order'] as $order_id => $export) {
				if (isset($export['export']) && $export['export'] && is_int($order_id)) {
					// mam opravdu v systemu objednavky s timto ideckem
					if ($this->Order->hasAny(array('Order.id' => $order_id))) {
						// objednavka je jiz fakturovana
						if ($this->Order->hasAny(array('Order.id' => $order_id, 'Order.invoice' => false))) {
							// zapamatuju si objednavku, kterou chci fakturovat
							$orders[] = $order_id;
						} else {
							$flash_messages[] = 'Objednávka č. ' . $order_id . ' je již vyfakturovaná, není součástí exportu.';
						}
					} else {
						$flash_messages[] = 'Objednávka č. ' . $order_id . ' není v systému, není součástí exportu.';
					}
				}
			}

			if (empty($orders)) {
				$flash_messages[] = 'Žádná objednávka není k fakturaci, export nebyl vytvořen.';
			} else {

				$file_name = $this->Order->create_pohoda_file($orders);

				if ($file_name !== false) {
					if (!$this->Order->set_attribute($orders, 'invoice', true)) {
						$flash_messages[] = 'Nepodařilo se uložit informaci o tom, že objednávky byly vyfakturovány.';
					}
				}

				// objednavky, ktere jsem vyexportoval do xml a byly neprijate, dam do stavu prijate
				$unreceived_orders = $this->Order->find('all', array(
					'conditions' => array('Order.id' => $orders, 'Order.status_id' => 1),
					'contain' => array(),
					'fields' => array('Order.id', 'Order.comments')	
				));
				$unreceived_orders = Set::extract('/Order/id', $unreceived_orders);

				if (!$this->Order->set_attribute($orders, 'status_id', 2)) {
					$flash_messages[] = 'Nepodařilo se uložit informaci o tom, že objednávky byly vyfakturovány.';
				} else {
					foreach ($unreceived_orders as $unreceived_order) {
						$this->Order->Status->change_notification($unreceived_order, 2);
					}
				}

				$flash_messages[] = 'Export objednávek do účetního systému Pohoda naleznete <a href="/admin/orders/pohoda_download/' . urlencode($file_name) . '">zde</a>.';
			}
		} else {
			$flash_messages[] = 'Není zadáno, které objednávky chcete exportovat';
		}
			
		$flash_messages = implode('<br/>', $flash_messages);
		$this->Session->setFlash($flash_messages, REDESIGN_PATH . 'flash_failure');
		$this->redirect($backtrace_url);
	}
	
	function admin_pohoda_download($file_name = null) {
		if ($file_name) {
			header('Content-Type: text/xml');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . basename(DS . POHODA_EXPORT_DIR . DS . $file_name) . '"');
			readfile(POHODA_EXPORT_DIR . DS . $file_name); // do the double-download-dance (dirty but worky)
			die();
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_eform_download($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno ID objednávky, u které chcete stáhnout eform.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->Order->hasAny(array('Order.id' => $id))) {
			$this->Session->setFlash('Objednávka, ke které chcete stánout eform, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
//		debug(file_get_contents('http://64454.w54.wedos.ws/orders/eform/' . $id)); die();
		header('Content-Type: text/xml');
		header('Content-Transfer-Encoding: Binary');
		header('Content-disposition: attachment; filename="' . basename('pohodaorder.xph') . '"');
		readfile('http://' . $_SERVER['HTTP_HOST'] . '/orders/eform/' . $id); // do the double-download-dance (dirty but worky)
		die();
	}
	
	function admin_notify_admin($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno ID objednávky, u které chcete odeslat email.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->Order->hasAny(array('Order.id' => $id))) {
			$this->Session->setFlash('Objednávka, ke které chcete odeslat email, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->Order->notifyAdmin($id);
		$this->Session->setFlash('Informace o nové objednávce byla odeslána', REDESIGN_PATH . 'flash_success');
		$this->redirect(array('action' => 'index'));
	}
	
	function eform($id = null) {
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.tax_class_id', 'Product.pohoda_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					)
				),
				'Shipping' => array(
						'fields' => array('Shipping.id', 'Shipping.name')
				)
			),
			'joins' => array(
				array(
					'table' => 'payments',
					'alias' => 'Payment',
					'type' => 'LEFT',
					'conditions' => array('Order.payment_id = Payment.id')
				),
				array(
					'table' => 'payment_types',
					'alias' => 'PaymentType',
					'type' => 'LEFT',
					'conditions' => array('Payment.payment_type_id = PaymentType.id')
				)
			),
			'fields' => array(
				'Order.id',
				'Order.created',
				'Order.customer_name',
				'Order.customer_dic',
				'Order.customer_ico',
				'Order.customer_street',
				'Order.customer_city',
				'Order.customer_zip',
				'Order.customer_phone',
				'Order.customer_email',
				'Order.shipping_cost',
				'Order.shipping_tax_class',
				'Order.comments',
				'Order.invoice',
		
				'Payment.id',
				'Payment.name',
					
				'PaymentType.id',
				'PaymentType.name'
			),
		));
		
		$date = explode(' ', $order['Order']['created']);
		$order['Order']['date'] = $date[0];
		
		$this->set('order', $order);
		$this->layout = REDESIGN_PATH . 'pohoda';
	}
	
	function address_edit(){
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'content';

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', ROOT_CATEGORY_ID);

		// nastavim si nadpis stranky
		$this->set('page_heading', 'Úprava adresy');
		
		if ( isset($this->data) ){
			// musi byt validni data
			$this->Order->Customer->Address->set($this->data);
			if ( $this->Order->Customer->Address->validates() ){
				switch ( $this->params['named']['type'] ){
					case "d":
						$this->Session->write('Address', $this->data['Address']);
						// pokud mam jako zpusob doruceni geis point (vydejni misto), musim po zmene adresy poslat zakaznika znova na plugin, kterym
						// si zvoli vydejni misto
						// to udelam tak, ze priznak v sesne, ktery mi rika, ze adresa byla vybrana pomoci pluginu, nastavim na false
						if ($this->Session->check('Order.shipping_id')) {
							$shipping_id = $this->Session->read('Order.shipping_id');
							if ($shipping_id == $this->Order->Shipping->GP_shipping_id) {
								$this->Session->write('Address.plugin_check', false);
							// pokud mam jiny zpusob dopravy nez GP a mam priznak								
							} elseif ($this->Session->check('Address.plugin_check')) {
								// tak ho zahodim
								$this->Session->delete('Address.plugin_check');
							}
						}
					break;
					case "f":
						$this->Session->write('Address_payment', $this->data['Address']);
					break;
				}
				$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'), null, true);
			} else {
				$this->Session->setFlash('Některé údaje nejsou správně vyplněny, zkontrolujte prosím formulář.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			// musim rozlisit, kterou adresu edituju
			switch ( $this->params['named']['type'] ){
				case "d":
					$this->data['Address'] = $this->Session->read('Address');
				break;
				case "f":
					$this->data['Address'] = $this->Session->read('Address_payment');
				break;
			}
		}
	}
	
	function add(){
		if ( $this->Session->check('Customer.id') ){
			if ( !isset($this->data) ){
				$this->Order->Customer->Address->recursive = -1;
				$address = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'd'));
				if ( $this->Order->Customer->Address->save($address) ){
					$address_payment = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'f'));
					if ( !$this->Order->Customer->Address->save($address_payment) ){
						$this->Session->setFlash('Vložte prosím Vaši fakturační adresu a klikněte znovu na "Zaplatit".');
						$this->redirect(array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'f'));
					}
				} else {
					$this->Session->setFlash('Vložte prosím Vaši doručovací adresu a klikněte znovu na "Zaplatit".', REDESIGN_PATH . 'flash_failure');
					$this->redirect(array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'd'));
				}
			} else {
				$address = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'd'));
				$address_payment = $this->Order->Customer->Address->find(array('customer_id' => $this->Session->read('Customer.id'), 'type' => 'f'));
				$this->Session->write('Address', $address['Address']);
				$this->Session->write('Address_payment', $address_payment['Address']);
				$this->Session->write('Order', $this->data['Order']);
			}
		}

		// vyzkousim, zda nemuzu preskocit rovnou na rekapitulaci
		if ( $this->Session->check('Customer') && 
			$this->Session->check('Address') &&
			$this->Session->check('Address_payment') &&
			$this->Session->check('Order.shipping_id') 
		) {
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
	
		// potrebuju si vytahnout statistiky o kosiku,
		// abych vedel zda je nejake zbozi v kosi

		// pripojim si model
		App::import('Model', 'CartsProduct');
		$this->Order->CartsProduct = &new CartsProduct;
		// vytahnu si statistiky kosiku
		$cart_stats = $this->Order->CartsProduct->getStats($this->requestAction('/carts/get_id'));

		// zjistim pocet produktu v kosiku
		if ( $cart_stats['products_count'] == 0 ){
			// v kosiku neni zadne zbozi, dam hlasku a presmeruju na kosik
			$this->Session->setFlash('Nemáte žádné zboží v košíku, v objednávce proto nelze pokračovat.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'), null, true);
		}
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'content';
		
		$this->set('_title', 'Detaily objednávky');
		$this->set('_description', 'Informace nutné pro kompletaci objednávky');
		$breadcrumbs = array(array('anchor' => 'Detaily objednávky', 'href' => '/orders/add'));
		$this->set('breadcrumbs', $breadcrumbs);
		
		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', ROOT_CATEGORY_ID);

		// nastavim si nadpis stranky
		$this->set('page_heading', 'Objednávka');
		
		// vytahnu si list pro select shippings
		$shipping_choices = $this->Order->Shipping->find('list', array(
			'conditions' => array('Shipping.active' => true)
		));
		$this->set('shipping_choices', $shipping_choices);
		
		// vytahnu si list pro select payments
		$payment_choices = $this->Order->Payment->find('list', array(
			'conditions' => array('Payment.active' => true)
		));
		$this->set('payment_choices', $payment_choices);

		// formular byl uz odeslan
		if ( isset( $this->data) && !empty($this->data) ){
			if (!isset($this->data['Customer']) && $this->Session->check('Customer')) {
				$this->data['Customer'] = $this->Session->read('Customer');
			}
			
			if ( empty($this->data['Address']['name']) && isset($this->data['Customer'])){
				$this->data['Address']['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
			}

			// validace dat zakaznika
			$this->Order->Customer->set($this->data);
			$valid_customer = $this->Order->Customer->validates();

			// validace dat adresy
			$this->Order->Customer->Address->set($this->data);
			$valid_address = $this->Order->Customer->Address->validates();

			// jsou-li data validni
			if ( $valid_address && $valid_customer ){
				// v prvnim kroku se vklada pouze dorucovaci adresa
				$this->data['Address']['type'] = 'd';

				// poslu si dal data zakaznika, adresy a objednavky
				$this->Session->write('Customer', $this->data['Customer']);
				$this->Session->write('Address', $this->data['Address']);
				$this->Session->write('Order', $this->data['Order']);
				$this->redirect(array('action' => 'recapitulation'), null, true);
			} else {
				$this->Session->setFlash('Pro pokračování v objednávce vyplňte prosím všechna pole.', REDESIGN_PATH . 'flash_failure');
			}
		}
	}
	
	function set_payment_and_shipping() {
		if (isset($this->data)) {
			$this->Session->write('Order', $this->data['Order']);
			// pokud je jako zpusob dopravy vybrano Geis Point (doruceni na odberne misto), presmeruju na plugin pro vyber odberneho
			// mista s tim, aby se po navratu presmeroval na ulozeni informaci o vyberu odberneho mista
			// zpusob dopravy GEIS POINT ma id = 21
			if ($this->data['Order']['shipping_id'] == $this->Order->Shipping->GP_shipping_id) {
				if ($service_url = $this->Order->Shipping->geis_point_url($this->Session)) {
					$this->redirect($service_url);
				} else {
					$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu');
					$this->redirect(array('controller' => 'customers', 'action' => 'order_personal_info'));
				}
			}
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
		
		$payments = $this->Order->Payment->find('all', array(
			'conditions' => array('Payment.active' => true),
			'contain' => array(),
			'order' => array('Payment.order' => 'asc')
		));
		
		$shippings = $this->Order->Shipping->find('all', array(
			'conditions' => array('Shipping.active' => true),
			'contain' => array(),
			'order' => array('Shipping.order' => 'asc')
		));
		foreach ($shippings as &$shipping) {
			$shipping['Shipping']['price'] = $this->Order->get_shipping_cost($shipping['Shipping']['id']);
		}
		
		$this->set(compact('payments', 'shippings'));

		if ($this->Session->check('Order.payment_id')) {
			$this->data['Order']['payment_id'] = $this->Session->read('Order.payment_id');
		}
		
		if ($this->Session->check('Order.shipping_id')) {
			$this->data['Order']['shipping_id'] = $this->Session->read('Order.shipping_id');
		}
		
		if ($this->Session->check('Order.comments')) {
			$this->data['Order']['comments'] = $this->Session->read('Order.comments');
		}
		
		$this->layout = REDESIGN_PATH . 'content';
	}

	function recapitulation(){
		if (!$this->Session->check('Order.shipping_id')) {
			$this->Session->setFlash('Není zvolena doprava pro Vaši objednávku', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}
		
		$order = $this->Session->read('Order');
		$customer = $this->Session->read('Customer');
		
		$shipping_id = $order['shipping_id'];
		// pokud mam zvoleno dodani na vydejni misto geis point, nactu parametry pro doruceni (z GET nebo sesny)
		if ($shipping_id == $this->Order->Shipping->GP_shipping_id) {
			// parametry jsou v GET
			if (isset($this->params['url']['GPName']) && isset($this->params['url']['GPAddress']) && isset($this->params['url']['GPID'])) {
				$gp_name = urldecode($this->params['url']['GPName']);
				$gp_address = urldecode($this->params['url']['GPAddress']);
				$gp_address = explode(';', $gp_address);
				$gp_street = '';
				if (isset($gp_address[0])) {
					$gp_street = $gp_address[0];
				}
				$gp_city = '';
				if (isset($gp_address[1])) {
					$gp_city = $gp_address[1];
				}
				$gp_zip = '';
				if (isset($gp_address[2])) {
					$gp_zip = $gp_address[2];
				}
				$gp_id = urldecode($this->params['url']['GPID']);
				// ulozim do sesny jako dorucovaci adresu
				$this->Session->write('Address.name', $gp_name . ', ' . $gp_id);
				$this->Session->write('Address.street', $gp_street);
				$this->Session->write('Address.street_no', '');
				$this->Session->write('Address.city', $gp_city);
				$this->Session->write('Address.zip', $gp_zip);
				// poznacim si, ze adresa je vybrana pomoci pluginu
				$this->Session->write('Address.plugin_check', true);
			} elseif (!$this->Session->check('Address') || !$this->Session->check('Address.plugin_check') || !$this->Session->read('Address.plugin_check')) {
				// nemam data pro vydejni misto ani v sesne ani v GET, ale potrebuju je, takze presmeruju znova na plugin
				// pro vyber vydejniho mista a z nej se sem vratim
				if ($service_url = $this->Order->Shipping->geis_point_url($this->Session)) {
					$this->redirect($service_url);
				} else {
					$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu.');
					$this->redirect(array('controller' => 'customers', 'action' => 'order_personal_info'));
				}
			}
		}
		
		$address = $this->Session->read('Address');

		if (!$this->Session->check('Address_payment')) {
			$address_payment = $this->Session->read('Address');
			$address_payment['type'] = 'f';
			$this->Session->write('Address_payment', $address_payment);
		}
		$address_payment = $this->Session->read('Address_payment');
		
		// produkty ktere jsou v kosiku
		$cart_products = $this->requestAction('/carts_products/getProducts/');
		$this->set('cart_products', $cart_products);
		
		$order['shipping_cost'] = $this->Order->get_shipping_cost($order['shipping_id'], $order['payment_id']);
		$this->Session->write('Order.shipping_cost', $order['shipping_cost']);

		// data o objednavce
		$this->set('order', $order);
		// data o zakaznikovi
		$this->set('customer', $customer);
		// data o adrese
		$this->set('address', $address);
		// data o adrese fakturacni
		$this->set('address_payment', $address_payment);
		
		// nadpis stranky
		$this->set('page_heading', 'Rekapitulace objednávky');

		// zakladni layout stranky
		$this->layout = REDESIGN_PATH . 'content';
		$this->set('_title', 'Rekapitulace objednávky');
		$this->set('_description', 'Kontrola údajů před odesláním objednávky.');
		$breadcrumbs = array(array('anchor' => 'Rekapitulace objednávky', 'href' => '/rekapitulace-objednavky'));
		$this->set('breadcrumbs', $breadcrumbs);

		// vytahnu si data o zpusobu dopravy
		$shipping = $this->Order->Shipping->get_data($order['shipping_id']);
		// vytahnu si data o zpusobu platby
		$payment = $this->Order->Payment->get_data($order['payment_id']);
		$this->set(compact(array('shipping', 'payment')));
	}

	function shipping_edit(){
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'content';

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', ROOT_CATEGORY_ID);

		// nastavim si nadpis stranky
		$this->set('page_heading', 'Způsob dopravy a platby');
		
		// vytahnu si list pro select shippings
		$shipping_choices = $this->Order->Shipping->find('list', array(
			'conditions' => array('Shipping.active' => true)
		));
		$this->set('shipping_choices', $shipping_choices);
		
		// vytahnu si list pro select payments
		$payment_choices = $this->Order->Payment->find('list', array(
			'conditions' => array('Payment.active' => true)
		));
		$this->set('payment_choices', $payment_choices);
		
		if ( isset($this->data) ){
			$this->Session->write('Order', $this->data['Order']);
			$this->Session->setFlash('Objednávka byla upravena.', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation')); 
		} else {
			$this->data['Order'] = $this->Session->read('Order');
		}
	}
	
	function finalize() {
		if (!$this->Session->check('Order.shipping_id')) {
			$this->Session->setFlash('Není zvolena doprava pro Vaši objednávku', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}

		$sess_customer = $this->Session->read('Customer');
		$customer['Customer'] = $sess_customer;
		$order = $this->Session->read('Order');
		$shipping_id = $order['shipping_id'];
		// pokud mam zvoleno dodani na vydejni misto geis point, nactu parametry pro doruceni (z GET nebo sesny)
		if ($shipping_id == $this->Order->Shipping->GP_shipping_id) {
			// parametry jsou v GET
			if (isset($this->params['url']['GPName']) && isset($this->params['url']['GPAddress']) && isset($this->params['url']['GPID'])) {
				$gp_name = urldecode($this->params['url']['GPName']);
				$gp_address = urldecode($this->params['url']['GPAddress']);
				$gp_address = explode(';', $gp_address);
				$gp_street = '';
				if (isset($gp_address[0])) {
					$gp_street = $gp_address[0];
				}
				$gp_city = '';
				if (isset($gp_address[1])) {
					$gp_city = $gp_address[1];
				}
				$gp_zip = '';
				if (isset($gp_address[2])) {
					$gp_zip = $gp_address[2];
				}
				$gp_id = urldecode($this->params['url']['GPID']);
				// ulozim do sesny jako dorucovaci adresu
				$this->Session->write('Address.name', $gp_name . ', ' . $gp_id);
				$this->Session->write('Address.street', $gp_street);
				$this->Session->write('Address.street_no', '');
				$this->Session->write('Address.city', $gp_city);
				$this->Session->write('Address.zip', $gp_zip);
				// poznacim si, ze adresa je vybrana pomoci pluginu
				$this->Session->write('Address.plugin_check', true);
			} elseif (!$this->Session->check('Address') || !$this->Session->check('Address.plugin_check') || !$this->Session->read('Address.plugin_check')) {
				// nemam data pro vydejni misto ani v sesne ani v GET, ale potrebuju je, takze presmeruju znova na plugin
				// pro vyber vydejniho mista a z nej se sem vratim
				if ($service_url = $this->Order->Shipping->geis_point_url($this->Session, true)) {
					$this->redirect($service_url);
				} else {
					$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu.');
					$this->redirect(array('controller' => 'customers', 'action' => 'order_personal_info'));
				}
			}
		}

		// pridam adresy
		if ($this->Session->check('Address')) {
			$customer['Address'][] = $this->Session->read('Address');
		}
		if ($this->Session->check('Address_payment')) {
			$customer['Address'][] = $this->Session->read('Address_payment');
		}

		// jedna se o neprihlaseneho a nezaregistrovaneho zakaznika
		if (!isset($customer['Customer']['id']) || empty($customer['Customer']['id'])) {
			// musim vytvorit novy zakaznicky ucet, takze vygeneruju login a heslo
			$customer['CustomerLogin'][0]['login'] = $this->Order->Customer->generateLogin($sess_customer);
			$customer_password = $this->Order->Customer->generatePassword($sess_customer);
			$customer['CustomerLogin'][0]['password'] = md5($customer_password);
			$customer['Customer']['confirmed'] = 1;
			$customer['Customer']['registration_source'] = 'eshop';
			$customer['Customer']['customer_type_id'] = 1;

			$c_dataSource = $this->Order->Customer->getDataSource();
			$c_dataSource->begin($this->Order->Customer);
			try {
				$this->Order->Customer->saveAll($customer);
			} catch (Exception $e) {
				$c_dataSource->rollback($this->Order->Customer);
				$this->Session->setFlash('Nepodařilo se uložit data o zákazníkovi, zopakujte prosím dokončení objednávky.');
				$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
			}
			$c_dataSource->commit($this->Order->Customer);
			
			// jedna se o nove zalozeny zakaznicky ucet, takze mu poslu notifikaci, pokud pri registraci uvedl svou emailovou adresu
			$customer['CustomerLogin'][0]['password'] = $customer_password;
			$this->Session->write('cpass', $customer_password);
			$this->Session->write('login', $customer['CustomerLogin'][0]['login']);
			$this->Order->Customer->notify_account_created($customer);
			$customer['Customer']['id'] = $this->Order->Customer->id;
//			$this->Session->write('Customer.id', $customer['Customer']['id']);
			$this->Session->delete('Customer.noreg');
			
		}
		
		// zalistuju zakaznika do mailchimpu
/*		App::import('Vendor', 'MailchimpTools', array('file' => 'mailchimp/mailchimp_tools.php'));
		$this->Order->Customer->MailchimpTools = &new MailchimpTools;
		$this->Order->Customer->MailchimpTools->subscribe($customer['Customer']['email'], $customer['Customer']['first_name'], $customer['Customer']['last_name']);*/
		
		if ( !isset($customer['Customer']['id']) || empty($customer['Customer']['id']) ){
			$this->Order->notify_order_error($customer, $sess_customer);
		}
		
		//data pro objednavku
		$order = $this->Order->build($customer);

		if ($order === false) {
			$this->Session->setFlash('Objednávku se nepodařilo uložit, máte správně zadané adresy?', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
		
		if (empty($order[1])) {
			$this->Session->setFlash('Vaše objednávka neobsahuje žádné produkty. Pravděpodobně byl Váš prohlížeč delší dobu nečinný.<br/>Prosím vložte produkty znovu do košíku a dokončete objednávku.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
		}

		$dataSource = $this->Order->getDataSource();
		$dataSource->begin($this->Order);
		
		if ($this->Order->save($order[0])) {
			// musim ulozit objednavku a smazat produkty z kosiku
			foreach ($order[1] as $ordered_product) {
				$ordered_product['OrderedProduct']['order_id'] = $this->Order->id;
				if (!$this->Order->OrderedProduct->saveAll($ordered_product)) {
					$dataSource->rollback($this->Order);
					$this->Session->setFlash('Objednávku se nepodařilo uložit. Zkuste to prosím znovu.', REDESIGN_PATH . 'flash_failure');
					$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
				}
			}
		} else {
			$dataSource->rollback($this->Order);
			$this->Session->setFlash('Objednávku se nepodařilo uložit. Zkuste to prosím znovu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));			
		}
		
		$this->Order->cleanCartsProducts();
		$dataSource->commit($this->Order);
		
		$this->Order->notifyCustomer($customer['Customer']);

		$this->Order->notifyAdmin();

		// uklidim promenne
		$this->Session->delete('Order');
		if ( $this->Session->check('Discount') ){
			$this->Session->delete('Discount');
		}
		
		// potrebuju na dekovaci strance vedet cislo objednavky
		$this->Session->write('Order.id', $this->Order->id);

		$this->redirect(array('action' => 'finished'), null, true);
	} // konec funkce

	function one_step_order() {
		App::import('Model', 'Cart');
		$this->Order->Cart = &new Cart;
		
		$cart_id = $this->Order->Cart->get_id();
		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->Order->Cart->CartsProduct->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $cart_id),
			'contain' => array(
				'Product' => array(
					'Image' => array(
						'conditions' => array('Image.is_main' => true)
					),
					'fields' => array(
						'Product.id',
						'Product.name',
						'Product.url'
					)
				)
			)
		));
		
		$customer_id = null;
		// pokud je prihlaseny
		if ($this->Session->check('Customer')) {
			$customer = $this->Session->read('Customer');
			if (isset($customer['id']) && !empty($customer['id']) && !isset($customer['noreg'])) {
				// zapamatuju si jeho id
				$customer_id = $customer['id'];
				
				// predvyplnim formular jeho udaji
				$customer = $this->Order->Customer->find('first', array(
					'conditions' => array('Customer.id' => $customer_id),
					'contain' => array(
						'Address' => array(
							// seradim adresy tak, aby prvni byla fakturacni
							'conditions' => array('Address.type' => 'f')
						)
					)
				));
			}
		}

		if (isset($this->data['Order']['shipping_id'])) {
			$payment_id = null;
			if (isset($this->data['Order']['payment_id'])) {
				$payment_id = $this->data['Order']['payment_id'];
			}
			$shipping_price = $this->Order->get_shipping_cost($this->data['Order']['shipping_id'], $payment_id);
		} else {
			App::import('Model', 'Cart');
			$this->Order->Cart = &new Cart;
			$shipping_price = $this->Order->Cart->shippingPrice($customer_id);
		}
		$this->set('shipping_price', $shipping_price);
		
		if (isset($this->data)) {
			
//			debug($this->data); die('vypsana data');
			
			if (isset($this->data['Order']['action'])) {
				switch ($this->data['Order']['action']) {
					// upravuju obsah kosiku
					case 'cart_edit':
						// najdu si produkt a upravim ho
						$cart_product = $this->Order->Cart->CartsProduct->find('first', array(
							'conditions' => array(
								'CartsProduct.id' => $this->data['CartsProduct']['id'],
								'CartsProduct.cart_id' => $cart_id
							),
							'contain' => array(),
							'fields' => array('CartsProduct.id')
						));
						if (!empty($cart_product)) {
							$cart_product['CartsProduct']['quantity'] = $this->data['CartsProduct']['quantity'];
							// nastavil jsem nulove mnozstvi, smazu produkt z kosiku
							if ($cart_product['CartsProduct']['quantity'] == 0) {
								if ($this->Order->Cart->CartsProduct->delete($this->data['CartsProduct']['id'])) {
									$this->Session->setFlash('Zboží bylo z košíku odstraněno.', REDESIGN_PATH . 'flash_success', array('type' => 'shopping_cart'));
								} else {
									$this->Session->setFlash('Zboží se nepodařilo z košíku odstranit, zkuste to prosím ještě jednou', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
								}
							} else {
								if ($this->Order->Cart->CartsProduct->save($cart_product)) {
									$this->Session->setFlash('Množství zboží bylo upraveno', REDESIGN_PATH . 'flash_success', array('type' => 'shopping_cart'));
								} else {
									$this->Session->setFlash('Nepodařilo se upravit množství zboží v košíku, zkuste to prosím ještě jednou.', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
								}
							}
						} else {
							$this->Session->setFlash('Košík daný produkt neobsahuje, nelze jej proto vymazat.', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
						}
						$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order', '#ShoppingCart'));
						break;
					case 'customer_login':
						$login = $this->data['Customer']['login'];
						$pwd_hash = md5($this->data['Customer']['password']);
						$conditions = array(
							'CustomerLogin.login' => $login,
							'Customer.active' => true
						);
						
						// pokus o zalogovani podle SNV - existuje v SNV pro dane prihlasovaci udaje zakaznik?
						$customer = $this->Order->Customer->CustomerLogin->find('first', array(
							'conditions' => $conditions,
							'contain' => array('Customer'),
						));
						
						if (empty($customer)) {
							$this->Session->setFlash('Uživatelský účet se zadaným loginem neexistuje. Zadejte prosím přihlašovací údaje znovu.', REDESIGN_PATH . 'flash_failure', array('type' => 'customer_login'));
							$this->data['Customer']['is_registered'] = 1;
						} else {
							if ($customer['CustomerLogin']['password'] != $pwd_hash) {
								$this->Session->setFlash('Uživatelský účet se zadaným heslem neexistuje. Zadejte prosím přihlašovací údaje znovu. Pokud si heslo nepamatujete, můžete <a href="/obnova-hesla">požádat o jeho obnovení</a>.', REDESIGN_PATH . 'flash_failure', array('type' => 'customer_login'));
								$this->data['Customer']['is_registered'] = 1;								
							} else {
								// ulozim si info o zakaznikovi do session
								$this->Session->write('Customer', $customer['Customer']);
								
								// ze session odstranim data o objednavce,
								// pokud se snazil zakaznik pred prihlasenim neco
								// vyplnovat v objednavce, delalo by mi to bordel
								$this->Session->delete('Order');
								
								// na pocitadle si inkrementuju pocet prihlaseni
								$customer_update = array(
									'Customer' => array(
										'id' => $customer['Customer']['id'],
										'login_count' => $customer['Customer']['login_count'] + 1,
										'login_date' => date('Y-m-d H:i:s')
									)
								);
								$this->Order->Customer->save($customer_update);
								
								// presmeruju
								$this->Session->setFlash('Jste přihlášen(a) jako ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'] . '.', REDESIGN_PATH . 'flash_success', array('type' => 'customer_login'));
								$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
							}
						}
						
						break;
					case 'apply_discount':
						// validace kuponu - ted tam nemusi byt, mame jenom jeden s dopravou zdarma
						if ( isset($this->data['Discount']['validator']) ){
							$this->data['Discount']['validator'] = trim($this->data['Discount']['validator']);
							$this->data['Discount']['validator'] = strtolower($this->data['Discount']['validator']);
							if ( $this->data['Discount']['validator'] == "dpr999" ){
								// dam kod do session
								$this->Session->write('Discount', "dpr999");
								// presmeruju
								$this->Session->setFlash('Slevový kód jsme ověřili a přidali jsme jej do košíku.', REDESIGN_PATH . 'flash_success', array('type' => 'shopping_cart'));
								$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
							}
						} else {
							// presmeruju
							$this->Session->setFlash('Tento slevový kód není platný.', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
							$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
						}
						
						break;
					case 'order_finish':
						// mam vybranou dopravu?
						if (!isset($this->data['Order']['shipping_id']) || empty($this->data['Order']['shipping_id'])) {
							$this->Session->setFlash('Vyberte prosím způsob dopravy, kterým si přejete zboží doručit.', REDESIGN_PATH . 'flash_failure', array('type' => 'shipping_info'));
						} else {
							$shipping_id = $this->data['Order']['shipping_id'];
							// nechci kontrolovat, jestli je zakaznikuv email unikatni (aby i zakaznik, ktery neni prihlaseny, ale jeho email je v systemu, mohl dokoncit objednavku
							if (isset($this->data['Customer']['id']) && empty($this->data['Customer']['id'])) {
								unset($this->data['Customer']['id']);
							}
	
							// jsou data o zakaznikovi validni?
							unset($this->Order->Customer->validate['email']['isUnique']);
							
							$address_data = null;
							// pokud neni zvolena doprava osobnim odberem
							if ($shipping_id == PERSONAL_PURCHASE_SHIPPING_ID) {
								unset($this->data['Address']);
							// je zvolena doprava na postu nebo do balikovny?
							} elseif ($shipping_id == ON_POST_SHIPPING_ID || $shipping_id == BALIKOVNA_POST_SHIPPING_ID) {
								$this->data['Address'][0]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
								$this->data['Address'][1]['name'] = $this->data['Address'][0]['name'];
								$this->data['Address'][1]['street'] = $this->data['Address'][0]['street'];
								$this->data['Address'][1]['street_no'] = $this->data['Address'][0]['street_no'];
								$this->data['Address'][1]['city'] = $this->data['Address'][0]['city'];
								$this->data['Address'][1]['zip'] = $this->data['Address'][0]['zip'];
								$this->data['Address'][1]['state'] = $this->data['Address'][0]['state'];
								
								$address_data = $this->data['Address'];
							} else {
								// dogeneruju si nazev do adresy
								$this->data['Address'][0]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
								$this->data['Address'][1]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
								// pokud mam zadano, ze dodaci adresa je shodna s fakturacni, nakopiruju hodnoty
								if (!$this->data['Customer']['is_delivery_address_different']) {
									$this->data['Address'][1]['name'] = $this->data['Address'][0]['name'];
									$this->data['Address'][1]['street'] = $this->data['Address'][0]['street'];
									$this->data['Address'][1]['street_no'] = $this->data['Address'][0]['street_no'];
									$this->data['Address'][1]['city'] = $this->data['Address'][0]['city'];
									$this->data['Address'][1]['zip'] = $this->data['Address'][0]['zip'];
									$this->data['Address'][1]['state'] = $this->data['Address'][0]['state'];
								}
								$address_data = $this->data['Address'];
							}
							
							$customer_data['Customer'] = $this->data['Customer'];
							if ($address_data) {
								$customer_data['Address'] = $address_data;
							}

							// jestlize jsou data o zakaznikovi validni
							if ($this->Order->Customer->saveAll($customer_data, array('validate' => 'only'))) {
								// jestli neni zakaznik prihlaseny a zaroven existuje zakaznik se zadanou emailovou adresou
								if (!$this->Session->check('Customer.id')) {
									$customer = $this->Order->Customer->find('first', array(
										'conditions' => array('Customer.email' => $this->data['Customer']['email']),
										'contain' => array(),
										'fields' => array('Customer.id')
									));
									// pokud existuje, priradim k objednavce zakaznikovo idcko (at nezakladam noveho a nevznikaji mi ucty s duplicitnim emailem
									if (!empty($customer)) {
										$this->data['Customer']['id'] = $customer['Customer']['id'];
									}
									// pamatuju si, ze zakaznik neni prihlaseny v objednavce (protoze to vsude testuju z historickych duvodu
									// pres customer id v sesne a to je mi ted na nic
									$this->data['Customer']['noreg'] = true;
								}
										
								$this->Session->write('Customer', $this->data['Customer']);
								if (isset($this->data['Address'][1])) {
									$this->Session->write('Address', $this->data['Address'][1]);
								}
								if (isset($this->data['Address'][0])) {
									$this->Session->write('Address_payment', $this->data['Address'][0]);
								}
								
								$this->Session->write('Order', $this->data['Order']);
								// pokud je jako zpusob dopravy vybrano Geis Point (doruceni na odberne misto), presmeruju na plugin pro vyber odberneho
								// mista s tim, aby se po navratu presmeroval na ulozeni informaci o vyberu odberneho mista
								if ($this->data['Order']['shipping_id'] == $this->Order->Shipping->GP_shipping_id) {
									if ($service_url = $this->Order->Shipping->geis_point_url($this->Session, true)) {
										$this->redirect($service_url);
									} else {
										$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu');
										$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order', '#' => 'OrderDetailsCustomer'));
									}
								}
								
								// presmeruju do finalizace objednavky, kde se data ulozena v sesne ulozi do systemu
								$this->redirect(array('controller' => 'orders', 'action' => 'finalize'));
									
							} else {
								// pokud jsem nakopiroval dorucovaci adresu pred ulozenim, protoze zakaznik nerekl, ze je jina, nez fakturacni, tak ji zase vynuluju
								if (!$this->data['Customer']['is_delivery_address_different']) {
									unset($this->data['Address'][1]);
								}
								$this->Session->setFlash('Údaje o zákazníkovi obsahují chybu, opravte ji prosím a formulář uložte znovu.', REDESIGN_PATH . 'flash_failure', array('type' => 'customer_info'));
							}
						}
						break;
				}
			}
		} else {
			if (isset($customer)) {
				$this->data = $customer;
			}
			$this->data['Customer']['is_registered'] = 0;
		}

		// data o zbozi v kosiku
		foreach ($cart_products as $index => $cart_product) {
			// u produktu si pridam jmenne atributy
			// chci tam dostat pole napr (barva -> bila, velikost -> S) ... takze (option_name -> value)
			// pokud znam id subproduktu, tak ma produkt varianty a muzu si je jednoduse vytahnout
			$cart_products[$index]['CartsProduct']['product_attributes'] = array();
			if (!empty($cart_product['CartsProduct']['subproduct_id'])) {
				$subproduct = $this->Order->Cart->CartsProduct->Product->Subproduct->find('first', array(
					'conditions' => array('Subproduct.id' => $cart_product['CartsProduct']['subproduct_id']),
					'contain' => array(
						'AttributesSubproduct' => array(
							'Attribute' => array(
								'Option'
							)
						)
					)
				));
				$product_attributes = array();
				if (!empty($subproduct)) {
					foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
						$product_attributes[$attributes_subproduct['Attribute']['Option']['name']] = $attributes_subproduct['Attribute']['value'];
					}
				}
				$cart_products[$index]['CartsProduct']['product_attributes'] = $product_attributes;
			}
		}
		$this->set('cart_products', $cart_products);
		
		// data pro volbu dopravy a platby
		$payments = $this->Order->Payment->find('all', array(
			'conditions' => array('Payment.active' => true),
			'contain' => array(),
			'order' => array('Payment.order' => 'asc')
		));
		
		$shippings = $this->Order->Shipping->find('all', array(
			'conditions' => array('Shipping.active' => true),
			'contain' => array(),
			'order' => array('Shipping.order' => 'asc')
		));
		foreach ($shippings as &$shipping) {
			$shipping['Shipping']['price'] = $this->Order->get_shipping_cost($shipping['Shipping']['id']);
		}
		
		$this->set(compact('payments', 'shippings'));
		
		if (!isset($this->data['Order']['payment_id']) && $this->Session->check('Order.payment_id')) {
			$this->data['Order']['payment_id'] = $this->Session->read('Order.payment_id');
		}
		
		if (!isset($this->data['Order']['shipping_id']) && $this->Session->check('Order.shipping_id')) {
			$this->data['Order']['shipping_id'] = $this->Session->read('Order.shipping_id');
		}
		
		if (!isset($this->data['Order']['comments']) && $this->Session->check('Order.comments')) {
			$this->data['Order']['comments'] = $this->Session->read('Order.comments');
		}

		// nastavim si titulek stranky
		$this->set('page_heading', 'Objednávka');
		$this->set('_title', 'Objednávka');
		$this->set('_description', 'Objednávka');
		$breadcrumbs = array(array('anchor' => 'Objednávka', 'href' => '/objednavka'));
		$this->set('breadcrumbs', $breadcrumbs);
		
		// link pro navrat z kosiku
		$back_shop_url = '/';
		if ($this->Session->check('last_visited_url')) {
			$back_shop_url = $this->Session->read('last_visited_url');
		}
		$this->set('back_shop_url', $back_shop_url);
		
		// layout
		$this->layout = REDESIGN_PATH . 'order_process';
		
	}
	
	// ajaxova metoda pro zjisteni ceny dopravy v kosiku na zaklade zvolene dopravy a zpusobu platby
	function ajax_shipping_price() {
		$res = array(
			'value' => null
		);
		
		if (isset($_POST['shippingId']) && isset($_POST['paymentId'])) {
			$shipping_id = $_POST['shippingId'];
			$payment_id = $_POST['paymentId'];

			$shipping_price = $this->Order->get_shipping_cost($shipping_id, $payment_id);
			$res['value'] = $shipping_price;
			echo json_encode($res);
		} else {
			// nemam stanovenou dopravu, nebo zpusob platby,
			// musim vratit chybu - vyzva k zvoleni platby
			// a dopravy
			$res = array(
				'error' => 'Y',
				'message' => 'Není zvolena doprava, nebo platba.',
				'value' => '-1'
			);
			echo json_encode($res);
		}
		die();
	}
	
	function finished() {
		$id = $this->Session->read('Order.id');
		if ( empty($id) ){
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'), null, true);
		}

		if (!$this->Session->check('Customer.id') || ($this->Session->check('Customer.id') && $this->Session->check('Customer.noreg'))) {
			// tenhle zaznam mazu jen kdyz se jedna o neprihlaseneho
			$this->Session->delete('Customer');
		}
		// smazu zaznamy o objednavce ze session
		$pass = $this->Session->read('cpass');
		$login = $this->Session->read('login');
		$this->Session->delete('Order');
		$this->Session->delete('Address');
		$this->Session->delete('Address_payment');
		$this->Session->delete('cpass');
		$this->Session->delete('login');
				
		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', ROOT_CATEGORY_ID);

		// nastavim nadpis stranky
		$this->set('page_heading', 'Objednávka byla dokončena');
		
		$conditions = array(
			'Order.id' => $id
		);
		
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array(
					'id', 'product_id', 'product_price_with_dph', 'product_quantity'
				),
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product' => array(
					'fields' => array(
						'id', 'name', 'tax_class_id'
					),
					'TaxClass' => array(
						'fields' => array(
							'id', 'value'
						)
					)
				)
			),
			'Payment'
		);
		
		$fields = array('id', 'subtotal_with_dph', 'shipping_cost', 'customer_city', 'customer_state', 'customer_email');
		
		$order = $this->Order->find('first', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'fields' => $fields
		));

		$jscript_code = '';
		// celkova dan vsech produktu v objednavce
		$tax_value = 0;
		
		// heureka overeno zakazniky
		App::import('Vendor', 'HeurekaOvereno', array('file' => 'HeurekaOvereno.php'));
		try {
			$overeno = new HeurekaOvereno('5c898f377be0c776bcfb82767b52fba2');
			$overeno->setEmail($order['Order']['customer_email']);
			foreach ($order['OrderedProduct'] as $op) {
				$overeno->addProductItemId($op['Product']['id']);
				$overeno->addProduct($op['Product']['name']);
			}
			$overeno->addOrderId($order['Order']['id']);
			$overeno->send();
		} catch (Exception $e) {}

		$fb_content_ids = array();
		
		foreach ( $order['OrderedProduct'] as $op ){
			$sku = $op['Product']['id'];
			$variations = '';
			
			// dan pro konkretni produkt
			$p_tax_value = $op['product_price_with_dph'] - (round($op['product_price_with_dph'] / (1 + ($op['Product']['TaxClass']['value'] / 100)), 0));

			$tax_value = $tax_value + $p_tax_value;
			
			foreach ( $op['OrderedProductsAttribute'] as $opa ) {
				$variations[] = $opa['Attribute']['Option']['name'] . ': ' . $opa['Attribute']['value'];
			}
			
			if ( !empty($variations) ){
				$sku .= ' / ' . implode(' - ', $variations);
				$variations = implode(' - ', $variations);
			}
			
			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each
			$jscript_code .= "
				_gaq.push(['_addItem',
					'" . $order['Order']['id'] . "',           // order ID - required
					'" . $sku ."',           // SKU/code - required
					'" . $op['Product']['name'] . "',        // product name
					'" . $variations . "',   // category or variation
					'" . $op['product_price_with_dph'] . "',          // unit price - required
					'" . $op['product_quantity'] . "'               // quantity - required
				]);
			";
			
			$fb_content_ids[] = "'CZ_" . $op['Product']['id'] . "'";
		}

		$jscript_code = "
			_gaq.push(['_addTrans',
				'" . $order['Order']['id'] . "',           // order ID - required
				'www.' . CUST_ROOT,  // affiliation or store name
				'" . $order['Order']['orderfinaltotal'] . "',          // total - required
				'" . $tax_value . "',           // tax
				'" . $order['Order']['shipping_cost'] . "',              // shipping
				'" . $order['Order']['customer_city'] . "',       // city
				'',     // state or province
				'" . $order['Order']['customer_state'] . "'             // country
			]);
		" . "\n\n" . $jscript_code;
		
		$jscript_code .= "\n\n" . "_gaq.push(['_trackTrans']);"; //submits transaction to the Analytics servers

		$this->set('jscript_code', $jscript_code);

		$fb_content_ids = implode(", ", $fb_content_ids);
		$this->set('fb_content_ids', $fb_content_ids);
		
		$order['Customer']['password'] = $pass;
		$order['Customer']['login'] = $login;
		
		$this->set('order', $order);

		$this->set('_title', 'Potvrzení objednávky');
		$this->set('_description', 'Potvrzení odeslání objednávky do systému ' . CUST_NAME);
		$breadcrumbs = array(array('anchor' => 'Potvrzení objednávky', 'href' => '/orders/finished'));
		$this->set('breadcrumbs', $breadcrumbs);
		
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'order_process';
	}

	function import() {
		$this->Order->import();
		die('here');
	}

	function update() {
		$this->Order->update();
		die('here');
	}
	
	function test() {
		$soap = new SoapClient('https://www.ppl.cz/IEGate/IEGate.asmx?WSDL');
		$GetPackageInfoResponse = $soap->GetPackageInfo(array('PackageID' => '40990066613'));
		debug($GetPackageInfoResponse);
	}
	
	function testHost(){
		print_r ( $_SERVER );
		die('konec vypisu');
	}
} // konec tridy
?>