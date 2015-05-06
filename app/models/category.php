<?php
class Category extends AppModel {

	var $name = 'Category';
	
	var $actsAs = array('Tree', 'Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název kategorie'
			)
		),
	);

	var $hasMany = array(
		'CategoriesProduct' => array('dependent' => true)
	);
	
	// id kategorii, ktere se nebudou brat v potaz pri generovani souvisejicich produktu
	var $unactive_categories_ids = array();
	
	// id kategorie s darky, jejiz produkty nechci vypisovat v seznamu produktu podle vyrobce
	var $present_category_id = 4;
	
	function afterSave($created) {
		if ($created) {
			if ($url = $this->buildUrl($this->data)) {
				$category = array(
					'Category' => array(
						'id' => $this->id,
						'url' => $url
					)
				);
				return $this->save($category);
			} else {
				return false;
			}
		}
		return true;
	}
	
	function buildUrl($category) {
		if (isset($category['Category']['name']) && isset($this->id)) {
			return strip_diacritic($category['Category']['name']) . '-c' . $this->id;
		}
		trigger_error('Nejsou potrebna data k vytvoreni url kategorie', E_USER_ERROR);
		return false;
	}
	
	function countProducts($categories) {
		foreach ($categories as &$category) {
			if (!empty($category['children'])) {
				$category['children'] = $this->countProducts($category['children']);
			}
			$category['Category']['productCount'] = $this->countAllProducts($category['Category']['id']);
			$category['Category']['activeProductCount'] = $this->countActiveProducts($category['Category']['id']);
		}
		
		return $categories;
	}
	
	function countAllProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id
			),
			'contain' => array()
		));

		return $result;
	}

	function countActiveProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id,
				'Product.active' => true
			),
			'contain' => array('Product')
		));

		return $result;
	}
	
	function getSubcategoriesMenuList($opened_category_id = null, $logged = false, $order_by_opened = true, $show_all = false) {
		$horizontal_categories_tree_ids = $this->get_horizontal_categories_tree_ids();
		if (in_array($opened_category_id, $horizontal_categories_tree_ids)) {
			$opened_category_id = 0;
		}
		$fields = array('id', 'name'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		// zjistim cestu k otevrene kategorii
		$path = $this->getPath($opened_category_id, $fields, -1);
		// zjistim idcka kategorii v ceste
		$path_ids = Set::extract('/Category/id', $path);
		$order = array();
		
		if ($order_by_opened) {
			// aktualne otevrenou kategorii chci vypsat ve strome na prvnim miste
			if ($opened_category_id) {
				$lead_id = $path_ids[0];
				$order[] = 'FIELD (Category.id, ' . $lead_id . ') DESC';
			// pokud nemam nastavenou aktualne otevrenou kategorii, chci mit rozbalenou kategorii "sportovni vyziva" s id 9
			} else {
				$path_ids[] = 9;
			}
		}
		$order['Category.lft'] = 'asc';
		$path_ids[] = ROOT_CATEGORY_ID;
		
		// je mozne, ze uz jsem v podstromu sportovni vyzivy, proto mozne duplicity smazu
		$path_ids = array_unique($path_ids);
		
		$conditions = array();
		
		$path_condition = "parent_id IN ('" . implode("', '", $path_ids) . "')";
		
		// pokud jsem v podkategorii "sportovni obleceni", chci vykreslit cely jeji podstrom
		$fitness_clothes_cat_ids = $this->subtree_ids(11);
		if (in_array($opened_category_id, $fitness_clothes_cat_ids)) {
			$conditions[] = array(
				'OR' => array(
					$path_condition,
					'id IN (' . implode(',', $fitness_clothes_cat_ids) . ')'
				)
			);
		} else {
			$conditions[] = $path_condition;
		}
		
		// idcka kategorii, ktere nechci ve vertikalnim menu zobrazit
		$unwanted_category_ids = array();
		// TODO - muzu odstranit, az pustim kategorii s dopravou zdarma live
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		if ($free_shipping_category_id =  $this->Setting->findValue('FREE_SHIPPING_CATEGORY_ID')) {
			$unwanted_category_ids[] = $free_shipping_category_id;
		}
		$unwanted_category_ids = array_merge($unwanted_category_ids, $horizontal_categories_tree_ids);
		if (!empty($unwanted_category_ids)) {
			$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_category_ids) . ')';
		}
		
		if (!$show_all) {
			$conditions['active'] = true;
			$conditions['public'] = true;
		}
		
		// pokud je uzivatel prihlaseny, vypisu i kategorie, ktere jsou urceny pouze prihlasenym
		if ($logged) {
			unset($conditions['public']);
		}

		$categories = $this->find('threaded', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.lft', 'Category.url', 'Category.name', 'Category.parent_id'),
			'order' => $order,
		));

		// ke kazde kategorii si zjistim kolik ma v sobe produktu
		$categories = $this->countProducts($categories);

		return array(
			'categories' => $categories, 'path_ids' => $path_ids, 'opened_category_id' => $opened_category_id
		);
	}
	
	function getSubmenuCategories() {
		$submenu_category_ids =  $this->get_horizontal_categories_ids();
		$categories = $this->find('all', array(
			'conditions' => array('Category.id' => $submenu_category_ids),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name', 'Category.url'),
			'order' => array('Category.lft')	
		));

		return ($categories);
	}
	
	/**
	 * Seznam idcek kategorii v podstromu
	 * @param in $category_id
	 */
	function subtree_ids($id) {
		// zjistim idcka kategorii v podstromu
		$category_ids = $this->children($id);
		$category_ids = Set::extract('/Category/id', $category_ids);
		
		$category_ids[] = $id;
		
		return $category_ids;
	}
	
	
	
	/**
	 * Vrati neprodavanejsi produkty v dane kategorii a jejim podstromu
	 */
	function most_sold_products($id = null, $customer_type_id = null, $limit = 2) {
		if (!$id) {
			return false;
		}

		// zjistim idcka kategorii v podstromu
		$category_ids = $this->subtree_ids($id);
		
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;

		$products = $this->CategoriesProduct->Product->find('all', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $category_ids,
				'Image.is_main' => true,
				'Product.active' => true
			),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.price',
				'Product.rate',
					
				'Image.id',
				'Image.name',

				'SUM(OrderedProduct.product_quantity) AS total_quantity'
			),
			'joins' => array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('OrderedProduct.product_id = Product.id')
				),
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'LEFT',
					'conditions' => array('CategoriesProduct.product_id = Product.id')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
			),
			'limit' => $limit,
			'group' => 'Product.id',
			'order' => array('total_quantity' => 'desc')
		));

		return $products;
	}
	
	function get_horizontal_categories_ids() {
		$horizontal_categories_ids = array(2, 4, 5, 6, 7);
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		if ($free_shipping_category_id = $this->Setting->findValue('FREE_SHIPPING_CATEGORY_ID')) {
			$horizontal_categories_ids[] = $free_shipping_category_id;
		}
		
		return $horizontal_categories_ids;
	}
	
	function get_horizontal_categories_tree_ids() {
		$horizontal_categories_ids = $this->get_horizontal_categories_ids();
		$horizontal_categories_tree_ids = array();
		foreach ($horizontal_categories_ids as $hci) {
			$children = $this->children($hci);
			$children = Set::extract('/Category/id', $children);
			$horizontal_categories_tree_ids[] = $hci;
			$horizontal_categories_tree_ids = array_merge($children, $horizontal_categories_tree_ids);
		}
		return $horizontal_categories_tree_ids;
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		// zjistim na co chci presmerovat
		// odstranim cast adresy, ktera mi urcuje, ze se jedna o produkt
		$pattern = preg_replace('/^\/category\//', '', $url);

		// vytahnu si id produktu na sportnutritionu
		if (preg_match('/^[^:]+:(\d+)/', $pattern, $matches)) {
			$sn_id = $matches[1];
	
			// najdu nas kategorii odpovidajici sn adrese
			$category = $this->find('first', array(
				'conditions' => array('Category.sportnutrition_id' => $sn_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.url')
			));
	
			if (!empty($category)) {
				// vratim url pro presmerovani
				$redirect_url = $category['Category']['url'];
			}
		}
	
		return $redirect_url;
	}
	
	function update() {
		// nejdriv natahnu deti rootove kategorie
		$condition = 'propojeni = 0';
		// vytahnu si objednavky ze sportnutritionu
		while ($snCategories = $this->findAllSn($condition)) {
			foreach ($snCategories as $snCategory) {
				if (!$this->hasAny(array('Category.sportnutrition_id' => $snCategory['SnCategory']['id']))) {
					// transformuju do tvaru pro nas shop
					$category = $this->transformSn($snCategory);
					$this->create();
					$this->save($category);
				}
			}
			// posunu se stromem o uroven niz
			$condition = Set::extract('/SnCategory/id', $snCategories);
			$condition = 'propojeni IN (' . implode(',', $condition) . ')';
		}
	}
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			// nejdriv natahnu deti rootove kategorie
			$condition = 'propojeni = 0';
			// vytahnu si objednavky ze sportnutritionu
			while ($snCategories = $this->findAllSn($condition)) {
				foreach ($snCategories as $snCategory) {
					// transformuju do tvaru pro nas shop
					$category = $this->transformSn($snCategory);
					$this->create();
					$this->save($category);
				}
				// posunu se stromem o uroven niz
				$condition = Set::extract('/SnCategory/id', $snCategories);
				$condition = 'propojeni IN (' . implode(',', $condition) . ')';
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM categories AS SnCategory
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$query .= '
			ORDER BY propojeni ASC, poradi ASC
		';
		$snCategories = $this->query($query);
		$this->setDataSource('default');
		return $snCategories;
	}
	
	function findBySnId($snId) {
		$category = $this->find('first', array(
			'conditions' => array('Category.sportnutrition_id' => $snId),
			'contain' => array()
		));
		
		if (empty($category)) {
			trigger_error('Kategorie se sportnutrition_id ' . $snId . ' neexistuje.', E_USER_ERROR);
		}
		
		return $category;
	}
	
	function transformSn($snCategory) {
//		debug($snCategory['SnCategory']['nazev_cz']); die();
		$category = array(
			'Category' => array(
				'parent_id' => $this->getParentId($snCategory),
				'name' => $snCategory['SnCategory']['nazev_cz'],
				'heading' => $snCategory['SnCategory']['header_cz'],
				'breadcrumb' => $snCategory['SnCategory']['header_cz'],
				'title' => $snCategory['SnCategory']['title_cz'],
				'description' => $snCategory['SnCategory']['description_cz'],
				'content' => $snCategory['SnCategory']['text_cz'],
				'sportnutrition_id' => $snCategory['SnCategory']['id'],
				'active' => $snCategory['SnCategory']['active'],
				'public' => !$snCategory['SnCategory']['pouze_prihlasenym']
			)	
		);
		if ($category['Category']['parent_id'] == ROOT_CATEGORY_ID) {
			unset($category['Category']['parent_id']);
		}

		return $category;
	}
	
	function getParentId($snCategory) {
		$parentId = ROOT_CATEGORY_ID;
		if ($snCategory['SnCategory']['propojeni']) {
			$parent = $this->findBySnId($snCategory['SnCategory']['propojeni']);
			if ($parent) {
				$parentId = $parent['Category']['id'];
			}
		}
		return $parentId;
	}
}
?>