<?php
class Product extends AppModel {

	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $hasAndBelongsToMany = array(
		'Cart' => array('className' => 'Cart'),
		'Flag' => array('className' => 'Flag')
	);

	var $hasMany = array(
		'Subproduct' => array(
			'dependent' => true
		),
		'Image' =>array(
			'dependent' => true
		),
		'ProductDocument' => array(
			'dependent' => true	
		),
		'CartsProduct' => array(
			'dependent' => true
		),
		'Comment' => array(
			'dependent' => true
		),
		'CategoriesProduct' => array(
			'dependent' => true
		),
		'RelatedProduct' => array(
			'dependent' => true
		),
		'CustomerTypeProductPrice' => array(
			'dependent' => true
		),
		'RecommendedProduct' => array(
			'dependent' => true
		),
		'OrderedProduct',
		'ComparatorProductClickPrice' => array(
			'dependent' => true
		)
	);

	var $belongsTo = array(
		'Manufacturer' => array('className' => 'Manufacturer',
			'foreignKey' => 'manufacturer_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => ''),
		'TaxClass' => array('className' => 'TaxClass',
			'foreignKey' => 'tax_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => ''),
		'Availability',
		'ProductType'
	);
	
	var $order = array('Product.active' => 'desc', 'Product.priority' => 'asc');
	
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'message' => 'Název produktu musí být vyplněn!'
		),
		'short_description' => array(
			'rule' => array('minLength', 1),
			'message' => 'Krátký popis produktu musí být vyplněn!'
		),
		'retail_price_with_dph' => array(
			'rule' => array('minLength', 1),
			'message' => 'Cena produktu musí být vyplněna!'
		),
		'tax_class_id' => array(
			'rule' => array('minLength', 1),
			'message' => 'Není vybrána žádná daňová třída!'
		),
/*		'ean' => array(
			'length13' => array(
				'rule' => array('between', 12, 13),
				'message' => 'EAN musí mít 13 znaků',
				'allowEmpty' => true
			)
		) */
	);
	
	var $price = 'FLOOR(IF(CustomerTypeProductPrice.price, CustomerTypeProductPrice.price, IF(Product.discount_common, Product.discount_common, Product.retail_price_with_dph)))';
	
	var $virtualFields = array(
		'rate' => 'ROUND(COALESCE(Product.overall_rate / Product.voted_count))'	
	);
	
	var $product_types = null;
	
	var $sorting_options = array(0 => 'Doporučujeme', 'Nejprodávánější', 'Nejlevnější', 'Nejdražší', 'Abecedy');
	
	function __construct() {
		parent::__construct();
		$this->product_types = $this->ProductType->find('list', array(
			'fields' => array('ProductType.id', 'ProductType.text')
		));
	}
	
	function beforeValidate() {
		// udelam si kontrolu, jestli je vyplneny titulek a url
		if (array_key_exists('title', $this->data['Product']) && empty($this->data['Product']['title'])){
			$this->data['Product']['title'] = $this->data['Product']['name'];
		}
		// zkontroluju, jestli jsou vyplnene heading, breadcrumb, zbozi a related name
		if (array_key_exists('heading', $this->data['Product']) && empty($this->data['Product']['heading'])) {
			$this->data['Product']['heading'] = $this->data['Product']['name'];
		}
		if (array_key_exists('breadcrumb', $this->data['Product']) && empty($this->data['Product']['breadcrumb'])) {
			$this->data['Product']['breadcrumb'] = $this->data['Product']['name'];
		}
		if (array_key_exists('related_name', $this->data['Product']) && empty($this->data['Product']['related_name'])) {
			$this->data['Product']['related_name'] = $this->data['Product']['name'];
		}
		if (array_key_exists('zbozi_name', $this->data['Product']) && empty($this->data['Product']['zbozi_name'])) {
			$this->data['Product']['zbozi_name'] = $this->data['Product']['name'];
		}
		if (array_key_exists('heureka_name', $this->data['Product']) && empty($this->data['Product']['heureka_name'])) {
			$this->data['Product']['heureka_name'] = $this->data['Product']['name'];
		}
	}
	
	function beforeSave() {
		// uprava pole s cenou, aby se mohlo vkladat take s desetinnou carkou
		if (array_key_exists('retail_price_with_dph', $this->data['Product'])) {
			$this->data['Product']['retail_price_with_dph'] = str_replace(',', '.', $this->data['Product']['retail_price_with_dph']);
			$this->data['Product']['retail_price_with_dph'] = floatval($this->data['Product']['retail_price_with_dph']);
		}
		if (!empty($this->data['Product']['discount_common'])) {
			$this->data['Product']['discount_common'] = floatval(str_replace(',', '.', $this->data['Product']['discount_common']));
		}
		if (array_key_exists('CustomerTypeProductPrice', $this->data)) {
			foreach ($this->data['CustomerTypeProductPrice'] as &$ctpp) {
				$ctpp['price'] = str_replace(',', '.', $ctpp);
				$ctpp['price'] = floatval($ctpp);
			}
		}
		
		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			// vygeneruju url
			if ($url = $this->buildUrl($this->data)) {
				$product = array(
					'Product' => array(
						'id' => $this->id,
						'url' => $url
					)
				);
				return $this->save($product);
			} else {
				return false;
			}
		}
	
		return true;
	}
	
	function buildUrl($product) {
		if (isset($product['Product']['name']) && isset($this->id)) {
			return strip_diacritic($product['Product']['name']) . '-p' . $this->id;
		}
		trigger_error('Nejsou potrebna data k vytvoreni url produktu', E_USER_ERROR);
		return false;
	}
	
	function assign_discount_price($product){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;

		$discount_price = $product['Product']['retail_price_with_dph'];
		
		// vychozi sleva je obecna sleva
		if ($product['Product']['discount_common'] > 0 && $product['Product']['discount_common'] < $discount_price) {
			$discount_price = $product['Product']['discount_common'];
		}
		
		// jestlize je uzivatel prihlaseny
		if ($this->Session->check('Customer')) {
			// zjistim, jestli je pro dany typ uzivatele zadana sleva produktu a pokud ano, jestli je mensi, nez sleva obecna
			$customer = $this->Session->read('Customer');
			// pokud ma uzivatel prirazeny customer_type
			if (isset($customer['customer_type_id'])) {
				// najdu typ daneho customera, abych podle poradi typu mohl vzit nejblizsi vyssi slevu 
				$customer_type = $this->CustomerTypeProductPrice->CustomerType->find('first', array(
					'conditions' => array('CustomerType.id' => $customer['customer_type_id']),
					'contain' => array(),
					'fields' => array('CustomerType.order')
				));
				
				// najdu cenu produktu, ktera odpovida dane skupine customeru (nebo nejblizsi slevu v hierarchii typu smerem dolu)
				$discount = $this->CustomerTypeProductPrice->find('first', array(
					'conditions' => array(
						'CustomerType.order <=' => $customer_type['CustomerType']['order'],
						'CustomerTypeProductPrice.product_id' => $product['Product']['id'],
						'CustomerTypeProductPrice.price IS NOT NULL'
					),
					'contain' => array('CustomerType'),
					'fields' => array('CustomerTypeProductPrice.price'),
					'order' => array('CustomerType.order' => 'desc')
				));
	
				// podivam se, jestli je zadana sleva pro prihlasene a je mensi, nez obecna sleva
				if (!empty($discount) && $discount['CustomerTypeProductPrice']['price'] && $discount['CustomerTypeProductPrice']['price'] > 0 && $discount['CustomerTypeProductPrice']['price'] < $discount_price) {
					// kdyz jo, tak ji dam jako vyslednou slevu
					$discount_price = $discount['CustomerTypeProductPrice']['price'];
				}
			}
		}
		
		return $discount_price;
	}
	
	function copy_images($new_product_id, $images){
		if ( !empty($images) ){
			foreach ( $images as $image ){
				// zkopiruju fyzicky na disku
				if (file_exists('product-images/' . $image['name'])){
					// zjistim si jmeno obrazku a musim ho prejmenovat
					$image_name = explode('.', $image['name']);
					$i = 1;
					while ( file_exists('product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						$i = $i + 1;
					}
						
					// vim jake muzu dat nove jmeno obrazku
					// obstaram na disku kopii obrazku
					if ( !copy('product-images/' . $image['name'], 'product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
						
					if ( !copy('product-images/small/' . $image['name'], 'product-images/small/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat SMALL obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
						
					if ( !copy('product-images/medium/' . $image['name'], 'product-images/medium/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat MEDIUM obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
				} else {
					return 'Nepodařilo se nalézt obrázek ' . $image['name'] . ' na disku.';
				}
	
				// vyresetuju si ID obrazku
				unset($this->Image->id);
	
				$new_image_data = array(
					'name' => $image_name[0] . '_' . $i . '.jpg',
					'product_id' => $new_product_id,
					'is_main' => $image['is_main']
				);
	
				if ( !$this->Image->save($new_image_data) ){
					return 'Nepodařilo se uložit nový obrázek ' . $new_image_data['name'] . ' do databáze.';
				}
			}
		}
		return true;
	}

	function get_subproducts($id){
		$options = $this->Subproduct->AttributesSubproduct->Attribute->Option->find('list');
		
		// projdu si existujici atributy a k nim si priradim subprodukty
		$subs = array();
		$hasAttributes = false;
		$this->Subproduct->unbindModel(array('belongsTo' => array('Product')));
		$subproducts = $this->Subproduct->find('list', array(
			'conditions' => array('product_id' => $id),
			'contain' => array()
		));
		//
		foreach ( $options as $option => $value ){
			
			$attributes = $this->Subproduct->AttributesSubproduct->find('all',
				array(
					'conditions' => array(
						'AttributesSubproduct.subproduct_id' => $subproducts,
						'Attribute.option_id' => $option
					),
					'contain' => array(
						'Attribute'
					),
					'order' => array('Attribute.sort_order' => 'asc'),
					'fields' => array('DISTINCT Attribute.id', 'Attribute.value')
				)
			);

			if ( $this->Subproduct->AttributesSubproduct->getNumRows() > 0 ){
				$hasAttributes = true;
			}
			$subs[$option] = array('Option' => array('name' => $options[$option], 'id' => $option));
			foreach ( $attributes as $attribute ){
				$subs[$option]['Value'][] = array(
					'id' => $attribute['Attribute']['id'],
					'name' => $options[$option],
					'value' => $attribute['Attribute']['value'],
				);
			}
		}
		if ( !$hasAttributes ){
			$subs = null;
		}
		
		return $subs;
	}
	
	function checkSubproductChoices($data){
		// zkontrolujeme, jestli pri vkladani do kosiku
		// jsou zvolene vsechny atributy produktu,
		// ktere se zvolit daji

		// nacitam si vsechny options ktere produkt muze mit
		$options = $this->optionsList($data['Product']['id']);

		// pokud nejake muze mit, musim zkontrolovat zda jsou zadany
		if ( !empty($options) ){
			foreach ( $options as $option ){
				$index = $option['Attribute']['option_id'];
				if ( !isset($data['Product']['Option'][$index]) || empty($data['Product']['Option'][$index])  ){
					return false;
				}
			}
		}
		return true;
	}

	function optionsList($id){
		return $this->query('SELECT DISTINCT (Attribute.option_id) FROM attributes Attribute, subproducts s WHERE s.product_id = ' . $id . ' AND s.attribute_id = Attribute.id');
	}

			/**
	 * z atributu produktu tvori vsechny jejich mozne kombinace 
	 *
	 * @param pole atributu $array
	 * @return pole vsech moznych kombinaci vstupnich atributu
	 */
	function combine($array) {
		$res = array();
		if (!empty($array)) {
			$first = current($array);
			array_shift($array);
			$tail = $array;
			if (empty($tail)) {
				foreach ($first as $item) {
					$res[] = array($item);
				}
			} else {
				foreach ($first as $item) {
					foreach ($this->combine($tail) as $j) {
						$res[] = array_merge(array($item), $j);
					}
				}
			}
		}
		return $res;
	}
	
	function sort_by_price($products, $direction){
		function sort_by_final_price_desc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $a_final_price < $b_final_price;
		}

		function sort_by_final_price_asc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $b_final_price < $a_final_price;
		}

		usort($products, 'sort_by_final_price' . $direction);
		return $products;
	}
	
	/**
	 * Updatuje zasobnik v minulosti navstivenych produktu
	 * @param array $stack
	 * @param int $product_id
	 * @return multitype:array
	 */
	function update_stack($stack, $product_id) {
		// v zasobniku muze byt max 7 produktu
		$stack_size = 7;
		// najdu produkt, ktery zakaznik navstivil
		$product = $this->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name', 'Product.url')
		));
		if (!empty($product)) {
			// pokud uz zakaznik ma neco v zasobniku navstivenych produktu
			if ($stack) {
				// pokud jiz mam v zasobniku prave navstiveny produkt, vypustim ho
				$filter_func = function($element) use ($product_id) {
					return $element['Product']['id'] != $product_id;
				};
				$stack = array_filter($stack, $filter_func);

				// pridam prave navstiveny produkt na zacatek
				array_unshift($stack, $product);
				
				// vypustim vsechny produkty na konci zasobniku, aby jeho velikost byla max $stack_size
				$stack = array_slice($stack, 0, $stack_size);
			} else {
				// jinak vytvorim zasobnik, kde bude navstiveny produkt nahore
				$stack = array(0 => $product);
			}
		}
		return $stack;
	}
	
	/**
	 * Vrati 4 nejvice prodavane produkty k zadanemu
	 * @param int $id
	 */
	function similar_products($id, $customer_type_id) {
		$present_category_ids = $this->CategoriesProduct->Category->subtree_ids(4);
		
		$products = $this->OrderedProduct->find('all', array(
			'conditions' => array(
				'OrderedProduct.product_id' => $id,
				'CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')'
			),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				$this->price . ' AS price',
				'Product.retail_price_with_dph',
				'SUM(OtherOrderedProduct.product_quantity) AS ordered_quantity',
				'Image.id',
				'Image.name',
				'CategoriesProduct.*'
			),
			'joins' => array(
				array(
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'INNER',
					'conditions' => array('Order.id = OrderedProduct.order_id')
				),
				array(
					'table' => 'ordered_products',
					'alias' => 'OtherOrderedProduct',
					'type' => 'INNER',
					'conditions' => array('Order.id = OtherOrderedProduct.order_id AND OtherOrderedProduct.product_id != OrderedProduct.product_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = OtherOrderedProduct.product_id AND Product.active = 1')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Product.id = Image.product_id AND Image.is_main = "1"')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CategoriesProduct.product_id AND CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')')
				)
			),
			'group' => array('OtherOrderedProduct.product_id'),
			'order' => array('ordered_quantity' => 'desc'),
			'limit' => 4
		));

		return $products;
	}
	
	function right_sidebar_products($id, $customer_type_id) {
		$product = $this->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array('CategoriesProduct'),
			'fields' => array('Product.id')	
		));
		
		$products = array();
		if (!empty($product['CategoriesProduct'])) {
			$products = $this->find('all', array(
				'conditions' => array(
					'Product.active' => true,
					'CategoriesProduct.category_id' => $product['CategoriesProduct'][0]['category_id'],
					'Availability.cart_allowed' => true,
					'Product.id !=' => $product['Product']['id']
				),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'categories_products',
						'alias' => 'CategoriesProduct',
						'type' => 'INNER',
						'conditions' => array('Product.id = CategoriesProduct.product_id')
					),
					array(
						'table' => 'availabilities',
						'alias' => 'Availability',
						'type' => 'INNER',
						'conditions' => array('Product.availability_id = Availability.id')
					),
					array(
						'table' => 'images',
						'alias' => 'Image',
						'type' => 'INNER',
						'conditions' => array('Product.id = Image.product_id AND Image.is_main = 1')
					),
					array(
						'table' => 'customer_type_product_prices',
						'alias' => 'CustomerTypeProductPrice',
						'type' => 'LEFT',
						'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
					)
				),
				'fields' => array(
					'Product.id',
					'Product.name',
					$this->price . ' AS price',
					'Product.url',
					'Product.retail_price_with_dph',
					'Image.id',
					'Image.name'
				),
				'limit' => 3
			));
		}
		return $products;
	}
	
	function paginateCount($conditions, $recursive, $extra) {
		$parameters = compact('conditions');
		if ($recursive != $this->recursive) {
			$parameters['recursive'] = $recursive;
		}
		$parameters = array_merge($parameters, $extra);
		$parameters['fields'] = array('id');
		$count = $this->find('all', $parameters);
		return count($count);
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		// zjistim na co chci presmerovat
		// odstranim cast adresy, ktera mi urcuje, ze se jedna o produkt
		if (preg_match('/^\/product\//', $url)) {
			$pattern = preg_replace('/^\/product\//', '', $url);
			
			// vytahnu si id produktu na sportnutritionu
			if (preg_match('/^[^:]+:(\d+)/', $pattern, $matches)) {
				$sn_id = $matches[1];
			}
		} elseif (preg_match('/^\/produkty-id\/(\d+)/', $url, $matches)) {
			$sn_id = $matches[1];
		}

		if (isset($sn_id) && !empty($sn_id)) {
			// najdu nas produkt odpovidajici sn adrese
			$product = $this->find('first', array(
				'conditions' => array('Product.id' => $sn_id),
				'contain' => array(),
				'fields' => array('Product.id', 'Product.url')
			));
			if (!empty($product)) {
				// vratim url pro presmerovani
				$redirect_url = $product['Product']['url'];
			}
		}

		return $redirect_url;
	}
	
	function update() {
		$snProducts = $this->findAllSn();
		foreach ($snProducts as $snProduct) {
			$product = $this->transformSn($snProduct);
			
			// podivam se, jestli mam produkt s timto sn id uz v systemu
			$dbProduct = $this->find('first', array(
				'conditions' => array('Product.sportnutrition_id' => $snProduct['SnProduct']['id']),
				'contain' => array(),
				'fields' => array('Product.id')
			));
			// pokud takovej mam, updatuju stavajici
			if (!empty($dbProduct)) {
				$product['Product']['id'] = $dbProduct['Product']['id'];
			} else {
				$this->create();				
			}

			if (!$this->save($product)) {
				debug($product);
				debug($this->validationErrors);
				$this->save($product, false);
			}
		}
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			// jeden produkt muze mit v selectu vice radku (protoze je tam LEFT JOIN productparams, tzn pokud je vice productparams navazanych na jeden produkt,
			// v selectu je pro produkt vice radku)
			$snProducts = $this->findAllSn();
			foreach ($snProducts as $snProduct) {
				$product = $this->transformSn($snProduct);
				$db_product = $this->find('first', array(
					'conditions' => array(
						'Product.sportnutrition_id' => $product['Product']['sportnutrition_id']
					),
					'contain' => array(),
					'fields' => array('Product.id', 'Product.product_type_id')
				));
				// pokud ma prave vyparsovany produkt nastaveno, ze je doplnek stravy nebo potravina vhodna pro sportovce
				if ($product['Product']['product_type_id']) {
					if (!empty($db_product) && !$db_product['Product']['product_type_id']) {
						$product['Product']['id'] = $db_product['Product']['id'];
					}
				} else {
					if (!empty($db_product) && $db_product['Product']['product_type_id']) {
						continue;
					}
				}
				$this->create();
				if (!$this->save($product)) {
					debug($product);
					debug($this->validationErrors);
					$this->save($product, false);
				}
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM products AS SnProduct
				LEFT JOIN productpricing AS SnProductPricing ON (SnProduct.id = SnProductPricing.product_id)
				LEFT JOIN productparams AS SnProductParam ON (SnProduct.id = SnProductParam.product_id)
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snProducts = $this->query($query);
		$this->setDataSource('default');
		return $snProducts;
	}
	
	function findBySnId($snId) {
		$product = $this->find('first', array(
			'conditions' => array('Product.sportnutrition_id' => $snId),
			'contain' => array()
		));
	
		return $product;
	}
	
	function transformSn($snProduct) {
		$manufacturer = $this->Manufacturer->findBySnId($snProduct['SnProduct']['vyrobce']);
		$manufacturer_id = null;
		if (!empty($manufacturer)) {
			$manufacturer_id = $manufacturer['Manufacturer']['id'];
		}
		
		$availability = $this->Availability->findBySnId($snProduct['SnProductPricing']['dostupnost']);
		$availability_id = 1;
		if (!empty($availability)) {
			$availability_id = $availability['Availability']['id'];
		}
		
		$snProduct['SnProduct']['nazev_cz'] = trim($snProduct['SnProduct']['nazev_cz']);
		$snProduct['SnProduct']['nadpis_cz'] = trim($snProduct['SnProduct']['nadpis_cz']);
		
		$product = array(
			'Product' => array(
				'id' => $snProduct['SnProduct']['id'],
				'name' => $snProduct['SnProduct']['nazev_cz'],
				'heading' => $snProduct['SnProduct']['nadpis_cz'],
				'breadcrumb' => $snProduct['SnProduct']['nazev_cz'],
				'related_name' => $snProduct['SnProduct']['nazev_cz'],
				'zbozi_name' => $snProduct['SnProduct']['nazev_cz'],
				'title' => $snProduct['SnProduct']['title_cz'],
				'short_description' => (!empty($snProduct['SnProduct']['popisek_cz']) ? $snProduct['SnProduct']['popisek_cz'] : $snProduct['SnProduct']['nazev_cz']),
				'description' => $snProduct['SnProduct']['popis_cz'],
				'retail_price_with_dph' => (!empty($snProduct['SnProductPricing']['cenapuvodni']) ? $snProduct['SnProductPricing']['cenapuvodni'] : 55555),
				'discount_common' => $snProduct['SnProductPricing']['cena2'],
				'active' => $snProduct['SnProduct']['active'],
				'priority' => $snProduct['SnProduct']['priorita'],
				'is_top_produkt' => $snProduct['SnProduct']['atribut_topprodukt'],
				'is_akce' => $snProduct['SnProduct']['atribut_akce'],
				'is_doprava_zdarma' => $snProduct['SnProduct']['atribut_dopravazdarma'],
				'is_novinka' => $snProduct['SnProduct']['atribut_novinka'],
				'is_sleva' => $snProduct['SnProduct']['atribut_sleva'],
				'is_doprodej' => $snProduct['SnProduct']['atribut_doprodej'],
				'is_montaz' => $snProduct['SnProduct']['atribut_montaz'],
				'is_firmy_cz' => $snProduct['SnProduct']['atribut_firmycz'],
				'is_slide_akce' => $snProduct['SnProduct']['atribut_slideakce'],
				'feed' => $snProduct['SnProduct']['generovat_do_feedu'],
				'guarantee' => $snProduct['SnProduct']['zaruka'],
				'fees' => $snProduct['SnProduct']['poplatky'],
				'video' => $snProduct['SnProduct']['video'],
				'weight' => $snProduct['SnProduct']['vaha'],
				'tax_class_id' => ($snProduct['SnProduct']['dph'] == 21 ? 1 : ($snProduct['SnProduct']['dph'] == 15 ? 2 : null)),
				'manufacturer_id' => $manufacturer_id,
				'availability_id' => $availability_id,
				'pohoda_id' => $snProduct['SnProductPricing']['kod'],
				'sportnutrition_id' => $snProduct['SnProduct']['id'],
				'product_type_id' => $this->get_product_type_id($snProduct['SnProductParam']['hodnota_cz'])
			)
		);

		return $product;
	}
	
	function get_product_type_id($product_type) {
		$zvlastni_vyziva_hlasky = array(
			0 => 'Potravina určená pro zvláštní výživu-vhodné pro sportovce. Ukládejte mimo dosah dětí! Není určeno pro děti, těhotné a kojící ženy. Uchovejte v suchu a temnu při teplotě 5-25 stupňů C. Po otevření spotřebujte do 120 dnů. Neobsahuje látky dopingového charakteru. Výrobce neručí za škody způsobené nesprávným užíváním a skladováním výrobku. V případě zdravotních problémů se před užíváním poraďte s lékařem. Výrobek je vyroben v závodu se zpracováním mléčných produktů, sóje a vajec, oříšků a pšenice.',
			'Potravina určena pro zvláštní výživu, vhodná pro sportovce.',
			'Potraviny pro zvláštní výživu - vhodné pro sportovce ',
			'Potravina určené pro zvláštní výživu, vhodné pro sportovce',
			'Potravina určená pro zvláštní výživu.- Vhodné pro sportovce.',
			'Potravina pro zvláštní výživu - vhodné pro sportovce.',
			'Potraviny určené pro zvláštní výživu, vhodné pro sportovce.',
			'Potraviny určená pro zvláštní výživu, vhodná pro sportovce.',
			'Potraviny určené pro zvláštní výživu, vhodná pro sportovce.',
			'Potravina určená pro zvláštní výživu, vhodné pro sportovce.',
			'potravina určená pro zvláštní výživu - vhodná pro sportovce',
			'Potravina určená pro zvláštní výživu - Vhodné pro sportovce.',
			'potravina určená pro zvláštní výživu, vhodná pro sportovce',
			'Potravina určená pro zvláštní výživu-vhodné pro sportovce. Ukládejte mimo dosah dětí! Není určeno pro děti, těhotné a kojící ženy. Uchovejte v suchu a temnu při teplotě 5-25 stupňů C. Po otevření spotřebujte do 120 dnů. Neobsahuje látky dopingového charakteru. Výrobce neručí za škody způsobené nesprávným užíváním a skladováním výrobku. V případě zdravotních problémů se před užíváním poraďte s lékařem. Výrobek je vyroben v závodu se zpracováním mléčných produktů, sóje a vajec, oříšků a pšenice. ',
			'Potravina určená pro zvláštní výživu. Vhodné pro sportovce.',
			'Určeno pro zvláštní výživu - Vhodné pro sportovce.',
			'Potravina určená pro zvláštní výživu - vhodné pro sportovce',
			' Potravina pro zvláštní výživu - vhodné pro sportovce ',
			'Potravina určená pro zvláštní výživu. Vhodné pro sportovce',
			'Potravina určená pro zvláštní výživu-vhodná pro sportovce',
			'Potravina určená pro zvláštní výživu-Vhodné pro sportovce.',
			'Potravina určená pro zvláštní výživu-vhodné pro sportovce',
			'Potravina určená pro zvláštní výživu-vhodné pro sportovce. Ukládejte mimo dosah dětí! Není určeno pro děti, těhotné a kojící ženy. Výrobek není určen jako náhrada pestré stravy a zdravého životního stylu. Uchovejte v suchu a temnu při teplotě 5-25 stupňů C. Po otevření spotřebujte do 120 dnů. Neobsahuje látky dopingového charakteru. Výrobce neručí za škody způsobené nesprávným užíváním a skladováním výrobku. V případě zdravotních problémů se před užíváním poraďte s lékařem. Výrobek je vyroben v závodu se zpracováním mléčných produktů, sóje a vajec, oříšků a pšenice. '
		);
		
		$doplnek_stravy_hlasky = array(
			0 => 'Doplněk stravy',
			'Doplňky stravy',
			'Doplněk stravy. Není určeno jako náhrada pestré stravy! Nepřekračujte doporučenou denní dávku. Ukládejte mimo dosah dětí! Není určeno pro děti, těhotné a kojící ženy. Uchovejte v suchu a temnu při teplotě 5-30 stupňů C. Po otevření spotřebujte do 120 dnů. Neobsahuje látky dopingového charakteru. Výrobce neručí za škody způsobené nesprávným užíváním a skladováním výrobku. V případě zdravotních problémů se před užíváním poraďte s lékařem. Výrobek je vyroben v závodu se zpracováním mléčných produktů, sóje a vajec, oříšků a pšenice, ryb a korýšů',
			'Dopněk stravy'
		);
		
		$product_type_id = null;
		if (in_array($product_type, $zvlastni_vyziva_hlasky)) {
			$product_type_id = 1;
		} elseif (in_array($product_type, $doplnek_stravy_hlasky)) {
			$product_type_id = 2;
		}
		return $product_type_id;
	}
	
	function findWithAttributesSn($condition = null) {
		$this->setDataSource('sportnutrition');
		
		$query = '
			SELECT DISTINCT SnProduct.product_id AS sportnutrition_id
			FROM products_povinne_select AS SnProduct
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '	
			';
		}
		$query .= '
			ORDER BY sportnutrition_id ASC
		';
		
		$products = $this->query($query);
		$this->setDataSource('default');
		return $products;
	}
	
	function get_action_products($customer_type_id, $limit = 3) {
		// nejprodavanejsi produkty
		$category_most_sold = $this->CategoriesProduct->Category->most_sold_products(2, $customer_type_id, $limit);

		return $category_most_sold;
	}
	
	function image_name($name, $suffix = 'jpg') {
		if (is_numeric($name)) {
			$product = $this->find('first', array(
				'conditions' => array('Product.id' => $name),
				'contain' => array(),
				'fields' => array('Product.name')
			));
			$name = $product['Product']['name'];
		}
		// vygeneruju nazev obrazku
		$image_name = strip_diacritic($name . '.' . $suffix, false);
		// zjistim, jestli nemusim obrazek cislovat
		$image_name = $this->Image->checkName('product-images/' . $image_name);
		$image_name = explode("/", $image_name);
		$image_name = $image_name[count($image_name) -1];
		return $image_name;
	}
	
	function in_category($id, $category_id) {
		return $this->CategoriesProduct->hasAny(array('product_id' => $id, 'category_id' => $category_id));
	}
}
?>