<?php
class CategoriesProductsController extends AppController {
	var $name = 'CategoriesProducts';
	
	var $helpers = array('Form');
	
	var $paginate = array(
		'limit' => 50,
		'order' => array(
			'CategoriesProduct.product_id' => 'desc'
		),
	);
		
	function admin_add(){
		if (isset($this->data)){
			if ($this->CategoriesProduct->hasAny($this->data['CategoriesProduct'])) {
				$this->Session->setFlash('Produkt je již přiřazen do kategorie.', REDESIGN_PATH . 'flash_failure');
			} else {
				$this->CategoriesProduct->create();
				if ($this->CategoriesProduct->save($this->data)) {
					$this->Session->setFlash('Produkt byl přiřazen do kategorie.', REDESIGN_PATH . 'flash_success');
				} else {
					$this->Session->setFlash('Produkt nemohl být zkopírován, došlo k chybě.', REDESIGN_PATH . 'flash_failure');
				}
			}
			$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $this->data['CategoriesProduct']['product_id'], (isset($this->params['named']['category_id']) ? $this->params['named']['category_id'] : null)));
		} else {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
	}
		
	// presun produktu mezi kategoriemi
	function admin_edit($id){
		// presune produkt do vybrane kategorie
		if ( !isset($this->data) ){ // jeste nebyl odeslan form

			// nactu si data
			$this->data = $this->CategoriesProduct->read(null, $id);

			// nactu si strom kategorii
			$categories = $this->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('1'))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			if ($this->CategoriesProduct->save($this->data)) {
				$this->Session->setFlash('Produkt byl přesunut.');
				$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $this->data['CategoriesProduct']['category_id']), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být přesunut.');
			}
		}
	}
	
	function admin_set_primary($id) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, které přiřazení do kategorie má být označeno jako primární', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$categories_product = $this->CategoriesProduct->find('first', array(
			'conditions' => array('CategoriesProduct.id' => $id),
			'contain' => array()
		));
		// zjistim si prirazeni produktu do kategorie, ktere chci oznacit jako primarni
		if (empty($categories_product)) {
			if (!$id) {
				$this->Session->setFlash('Přiřazení do kategorie, které má být označeno jako primární, neexistuje', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('controller' => 'products', 'action' => 'index'));
			}
		}
		
		$save = array(
			array(
				'id' => $categories_product['CategoriesProduct']['id'],
				'primary' => true
			)
		);
		// zjistim primarni prirazeni produktu do kategorie
		$other_product_cps = $this->CategoriesProduct->find('all', array(
			'conditions' => array(
				'CategoriesProduct.id !=' => $categories_product['CategoriesProduct']['id'],
				'CategoriesProduct.product_id' => $categories_product['CategoriesProduct']['product_id'],
				'CategoriesProduct.primary' => true
			),
			'contain' => array()
		));
		
		// zrusim primarni prirazeni
		foreach ($other_product_cps as $other_product_cp) {
			$save[] = array(
				'id' => $other_product_cp['CategoriesProduct']['id'],
				'primary' => false
			);
		}
		
		if ($this->CategoriesProduct->saveAll($save)) {
			$this->Session->setFlash('Přiřazení do kategorie bylo označeno jako primární', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Primární přiřazení do kategorie se nepodařilo označit', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $categories_product['CategoriesProduct']['product_id']));
	}
	
	// smaze prirazeni produktu do kategorie
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámé přiřazení do kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$categories_product = $this->CategoriesProduct->find('first', array(
			'conditions' => array('CategoriesProduct.id' => $id),
			'contain' => array(),
			'fields' => array('CategoriesProduct.id', 'CategoriesProduct.product_id')	
		));
		
		if (empty($categories_product)) {
			$this->Session->setFlash('Neexistující přiřazení do kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->CategoriesProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl odstraněn z kategorie.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit z kategorie.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $categories_product['CategoriesProduct']['product_id'], (isset($this->params['named']['category_id']) ? $this->params['named']['category_id'] : null)));
	}
	
	function view($id = null) {
		// presmerovani starych URL, kdyz byly zadany parametry filtru produktu (podle vyrobce, prichute...)
		if (isset($_GET['sorting']) || isset($_GET['paging']) || isset($_GET['manufacturer_id']) || isset($_GET['attribute_id'])) {
			$parameters = array();
			if (isset($_GET['sorting']) && $_GET['sorting'] != '0') {
				$parameters[] = 's=' . $_GET['sorting'];
			}
			if (isset($_GET['paging']) && $_GET['paging'] != '0') {
				$parameters[] = 'p=' . $_GET['paging'];
			}
			if (isset($_GET['manufacturer_id']) && $_GET['manufacturer_id'] != '') {
				$parameters[] = 'm=' . $_GET['manufacturer_id'];
			}
			if (isset($_GET['attribute_id']) && $_GET['attribute_id'] != '') {
				$parameters[] = 'a=' . $_GET['attribute_id'];
			}
			$parameters = implode('&', $parameters);
			$url = '/' . $this->params['url']['url'] . '/?' . $parameters;
			$this->redirect($url);
		}
		
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'category';
		
		// nastaveni formu pro vyber poctu produktu na strance
		define('ALL_STRING', 'vše');
		$paging_options = array(0 => 16, 24, 32, ALL_STRING);
		$this->set('paging_options', $paging_options);
		
		$sorting_options = $this->CategoriesProduct->Product->sorting_options;
		$this->set('sorting_options', $sorting_options);
		
		$sorting = 0;
		if (isset($_GET['s'])) {
			$sorting = $_GET['s'];
		}
		$this->data['CategoriesProduct']['s'] = $sorting;
		
		$paging = 0;
		if (isset($_GET['p'])) {
			$paging = $_GET['p'];
		}
		$this->data['CategoriesProduct']['p'] = $paging;
		
		// nastavim si pro menu IDecko kategorie,
		// kterou momentalne prohlizim
		$this->set('opened_category_id', $id);
		// nactu si info o kategorii
		$category = $this->CategoriesProduct->Category->find('first', array(
			'conditions' => array('Category.id' => $id, 'Category.active' => true),
			'contain' => array()	
		));
		
		if (empty($category)) {
			$this->cakeError('error404');
		}
		
		// k informaci o kategorii pridam text o sleve pro registrovane
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$category_text = $this->Setting->findValue('CATEGORYTEXT');
		
		$category['Category']['content'] = $category['Category']['content'] . ($category_text && !empty($category_text) ? $category_text : '');

		$this->set('category', $category);
		// nastavim head tagy

/*		if (isset($this->params['named']['page']) && $this->params['named']['page'] > 1) {
			$_title .= ' - stránka ' . $this->params['named']['page'];
			$_description .= ' - stránka ' . $this->params['named']['page'];
		}*/
		
		
		// nejprodavanejsi produkty
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		$category_ids = $this->CategoriesProduct->Category->children($category['Category']['id']);
		$category_ids = Set::extract('/Category/id', $category_ids);
		$category_ids[] = $id;
		
		$limit = $paging_options[$this->data['CategoriesProduct']['p']];
		
		$conditions = array(
			'CategoriesProduct.category_id' => $category_ids,
			'Product.active' => true,
			'Product.price >' => 0
		);

		$manufacturer_id = null;
		if (isset($_GET['m']) && !empty($_GET['m'])) {
			$manufacturer_id = $_GET['m'];
			$manufacturer_id_arr = explode(',', $manufacturer_id);
			if ($this->CategoriesProduct->Product->Manufacturer->filter_limit && count($manufacturer_id_arr) == $this->CategoriesProduct->Product->Manufacturer->filter_limit) {
				$manufacturer_id = '';
			} else {
				$conditions = array_merge($conditions, array('Product.manufacturer_id' => $manufacturer_id_arr));
				$this->data['CategoriesProduct']['m'] = $manufacturer_id;
			}
		}
		
		$joins = array(
			array(
				'table' => 'ordered_products',
				'alias' => 'OrderedProduct',
				'type' => 'LEFT',
				'conditions' => array('OrderedProduct.product_id = Product.id')
			),
			array(
				'table' => 'categories_products',
				'alias' => 'CategoriesProduct',
				'type' => 'INNER',
				'conditions' => array('CategoriesProduct.product_id = Product.id')
			),
			array(
				'table' => 'images',
				'alias' => 'Image',
				'type' => 'LEFT',
				'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
			),
			array(
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPrice',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			)
		);
		
		if (isset($_GET['a']) && !empty($_GET['a'])) {
			$attribute_id = $_GET['a'];
			$attribute_id_arr = explode(',', $attribute_id);
			$conditions = array_merge($conditions, array('AttributesSubproduct.attribute_id' => $attribute_id_arr));
			$this->data['CategoriesProduct']['a'] = $attribute_id;
			
			$add_joins = array(
				array(
					'table' => 'subproducts',
					'alias' => 'Subproduct',
					'type' => 'LEFT',
					'conditions' => array('Product.id = Subproduct.product_id'),
				),
				array(
					'table' => 'attributes_subproducts',
					'alias' => 'AttributesSubproduct',
					'type' => 'LEFT',
					'conditions' => array('Subproduct.id = AttributesSubproduct.subproduct_id')
				)
			);
			
			$joins = array_merge($joins, $add_joins);
		}

		$this->paginate['Product'] = array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.sold',
				'Product.price',
				'Product.rate',
					
				'Image.id',
				'Image.name',
				
				'Availability.id',
				'Availability.cart_allowed'

			),
			'joins' => $joins,
			'group' => 'Product.id',
			'limit' => $limit
		);
		// pokud je vybrano, ze se maji vypsat vsechny produkty
		if ($limit == ALL_STRING) {
			$this->paginate['Product']['show'] = 'all';
		}
		
		// sestavim podminku pro razeni podle toho, co je vybrano
		$order = array('Availability.cart_allowed' => 'desc');
		if (isset($this->data['CategoriesProduct']['s'])) {
			switch ($this->data['CategoriesProduct']['s']) {
				// vychozi razeni podle priority
				case 0: $order = array_merge($order, array('Product.priority' => 'asc')); break;
				// nastavim razeni podle prodejnosti
				case 1: $order = array_merge($order, array('Product.sold' => 'desc')); break;
				// nastavim razeni podle ceny
				case 2: $order = array_merge($order, array('Product.price' => 'asc')); break;
				case 3: $order = array_merge($order, array('Product.price' => 'desc')); break;
				// nastavim razeni podle nazvu
				case 4: $order = array_merge($order, array('Product.name' => 'asc')); break;
				default: $order = array();
			}
		}
		
		$this->paginate['Product']['order'] = $order;

		$this->CategoriesProduct->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;
		$products = $this->paginate('Product');

		// opetovne vypnuti virtualnich poli, nastavenych za behu
		unset($this->CategoriesProduct->Product->virtualFields['sold']);
		unset($this->CategoriesProduct->Product->virtualFields['price']);

		$this->set('products', $products);

		$listing_style = 'products_listing_grid';
		
		$this->set('listing_style', $listing_style);
		
		// nastavim meta informace o dane strance
		list($_title, $_heading, $_description, $breadcrumbs) = $this->CategoriesProduct->Category->getAboutData($id, $manufacturer_id);
		$this->set(compact('_title', '_description', '_heading', 'breadcrumbs'));
		
		// pokud mam ve filtru vybrane vyrobce, nechci vypisovat nejprodavanejsi produkty v kategorii
		if (!$manufacturer_id) {
			$category_most_sold = $this->CategoriesProduct->Category->most_sold_products($id, $customer_type_id);
			$this->set('category_most_sold', $category_most_sold);
			
			// nastavim zobrazovany banner
			$category_banner = $this->CategoriesProduct->Category->categoryBanner($id);
			$this->set('category_banner', $category_banner);
		}
		
		$action_products = $this->CategoriesProduct->Product->get_action_products($customer_type_id, 4);
		$this->set('action_products', $action_products);
	}

	function view_named($id = null){
		
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'no_right';

		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		$joins = array(
			array(
				'table' => 'ordered_products',
				'alias' => 'OrderedProduct',
				'type' => 'LEFT',
				'conditions' => array('OrderedProduct.product_id = Product.id')
			),
			array(
				'table' => 'categories_products',
				'alias' => 'CategoriesProduct',
				'type' => 'INNER',
				'conditions' => array('CategoriesProduct.product_id = Product.id')
			),
			array(
				'table' => 'images',
				'alias' => 'Image',
				'type' => 'LEFT',
				'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
			),
			array(
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPrice',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			)
		);
		
		// nastavim si ktere produkty chci zobrazit
		$conditions[2] = array('Product.id' => array(3521, 1190, 4591)); // karnitiny
		$conditions[1] = array('Product.id' => array(4176, 4565, 4167)); // proteiny
		
		$this->paginate['Product'] = array(
			'conditions' => $conditions[$id],
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.sold',
				'Product.price',
				'Product.rate',
					
				'Image.id',
				'Image.name',
				
				'Availability.id',
				'Availability.cart_allowed'

			),
			'joins' => $joins,
			'group' => 'Product.id',
			'limit' => 10
		);
		
		$this->CategoriesProduct->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;
		
		$products = $this->paginate('Product');
		$this->set('products', $products);
		
		$listing_style = 'products_listing_grid';
		$this->set('listing_style', $listing_style);
		
		// cislo dnesniho dne (0 - nedele az 6 sobota)
		$day_in_week = date("w");

		// dokdy v ten den muzu objednat, aby to ten den jeste odeslo
		$default_time_deadline = "13:00:00";
		
		// nastaveni doruceni a textove reprezentace pro kazdy den v tydnu
		$delivery_settings = array(
			0 => array( // nedele
				'deadline' => '23:59:59',
				'textual' => 'v úterý' 
			),
			1 => array( // pondeli
				'deadline' => $default_time_deadline,
				'textual' => 'v úterý' 
			),
			2 => array( // utery
				'deadline' => $default_time_deadline,
				'textual' => 've středu' 
			),
			3 => array( // streda
				'deadline' => $default_time_deadline,
				'textual' => 've čtvrtek' 
			),
			4 => array( // ctvrtek
				'deadline' => $default_time_deadline,
				'textual' => 'v pátek' 
			),
			5 => array( // patek
				'deadline' => $default_time_deadline,
				'textual' => 'v pondělí' 
			),
			6 => array( // sobota
				'deadline' => "23:59:59",
				'textual' => 'v úterý' 
			)
		);
		
		// pokud jsem uz dneska za deadlinem, tak posunu o jeden den dopredu
		if (time() >= strtotime($delivery_settings[$day_in_week]['deadline'])){
			$day_in_week = $day_in_week + 1;
			if ( $day_in_week > 6 ){
				$day_in_week = 0;
			}
		}
		$this->set('delivery_textual', $delivery_settings[$day_in_week]['textual']);
		$this->set('id', $id);
	}
	
	function cancel_filter($id) {
		$this->Session->delete('filter');
		
		$this->CategoriesProduct->Category->contain();
		$category = $this->CategoriesProduct->Category->read(null, $id);
		$url = '/' . $category['Category']['url'];
		if (isset($this->params['named']['ls']) && $this->params['named']['ls'] == 'list') {
			$url .= '/ls:list';
		}
		$this->redirect($url);
	}
	
	function sort_by_availability_and_price($a, $b) {
		if ($a['Product']['Availability']['cart_allowed'] && !$b['Product']['Availability']['cart_allowed']) {
			return -1;
		} elseif (!$a['Product']['Availability']['cart_allowed'] && $b['Product']['Availability']['cart_allowed']) {
			return 1;
		} elseif ($a['Product']['Availability']['cart_allowed'] == $b['Product']['Availability']['cart_allowed']) {
			return $a['Product']['discount_price'] > $b['Product']['discount_price'];
		}
	}
	
	function import($truncate = true) {
		$this->CategoriesProduct->import($truncate);
		die('here');
	}
}
?>