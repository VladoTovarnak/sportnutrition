<?php
class SearchesController extends AppController {

	var $name = 'Searches';

	/**
	 * Vyhledavani produktu v administraci.
	 *
	 */
	function admin_do(){
		if ( isset($this->data) ){
			$this->data['Search']['query'] = trim($this->data['Search']['query']);
			
			App::import('Model', 'Product');
			$this->Product = &new Product;
			
			// vysledky s celym retezcem
			$products = $this->Product->find('all', array(
				'conditions' => array(
					'OR' => array(
						array("Product.name LIKE '%%" . $this->data['Search']['query'] . "%%'"),
						array('Product.id' => $this->data['Search']['query'])
					)
				),
				'contain' => array('CategoriesProduct')
			));

			// vysledky s rozsekanym retezcem
			$split_query = explode(" ", $this->data['Search']['query']);
			$count_split = count($split_query);
			
			if ( $count_split > 1 ){
				$not_ids = array();
				for ( $i = 0; $i < count($products); $i++ ){
					$not_ids[] = $products[$i]['Product']['id'];
				}
				
				$split_conditions = array();
				for ( $i = 0; $i < $count_split; $i++ ){
					$split_conditions[] = "Product.name LIKE '%%" . $split_query[$i] . "%%'";
				}

				// vysledky s rozsekanym retezcem
				$products2 = $this->Product->find('all', array(
					'conditions' => array('AND' => $split_conditions, 'NOT' => array('Product.id' => $not_ids))
				));
				$products = am($products, $products2);
			}
			
			for ( $i = 0; $i < count($products); $i++ ){
				for ( $j = 0; $j < count($products[$i]['CategoriesProduct']); $j++ ){
					$products[$i]['CategoriesProduct'][$j]['path'] = $this->Product->CategoriesProduct->Category->getpath($products[$i]['CategoriesProduct'][$j]['category_id']);
				}
			}

			$this->set('products', $products);
		}
	}
	
	function index($query = null, $start = 0){
		// layout
		$this->layout = 'default_front_end';
		
		// nastavim nadpis
		$this->set('page_heading', 'Vyhledávání');

		if ( !empty($query) ){
			$XML = $this->Search->doSearch($query, $start);
			$this->set('xml', $XML);
		}
	}
	
	function parsequery(){
		$target = array('controller' => 'searches', 'action' => 'index');
		if ( isset($this->data) && !empty($this->data['Search']['q']) ){
			$target[0] = urlencode($this->data['Search']['q']);
			$target[1] = '0';
		}
		$this->redirect($target, null, true);
	}

	/**
	 * Vyhledavani produktu v obchode.
	 *
	 * @param string $id
	 */
	function do_search(){
		$this->layout = REDESIGN_PATH . 'category';
		$this->set('_title', 'Vyhledávání produktů');
		$this->set('_description', 'Vyhledávač produktů v obchodě ' . CUST_NAME);
		
		// nastaveni formu pro vyber poctu produktu na strance
		define('ALL_STRING', 'vše');
		$paging_options = array(0 => 16, 24, 32, ALL_STRING);
		$this->set('paging_options', $paging_options);
		
		$sorting_options = array(0 => 'Doporučujeme', 'Nejprodávánější', 'Nejlevnější', 'Nejdražší', 'Abecedy');
		$this->set('sorting_options', $sorting_options);
		
		if (isset($_GET['q'])) {
			$this->data['Search']['q'] = $_GET['q'];
		}
		
		if (isset($_GET['sorting'])) {
			$this->data['Search']['sorting'] = $_GET['sorting'];
		}
		
		if (isset($_GET['paging'])) {
			$this->data['Search']['paging'] = $_GET['paging'];
		}
		
		if (!isset($this->data['Search']['sorting']) || empty($this->data['Search']['sorting'])) {
			$this->data['Search']['sorting'] = 0;
		}
		
		if (!isset($this->data['Search']['paging']) || empty($this->data['Search']['paging'])) {
			$this->data['Search']['paging'] = 0;
		}
		
		$products = array();
		$customer_type_id = 2;

		if (!empty($this->data) && isset($this->data['Search']['q'])){
			// hledany vyraz musim ocistit
			// od mezer na zacatku a konci celeho vyrazu
			$queries = trim($this->data['Search']['q']);
			
			// od vice mezer za sebou
			while ( eregi("  ", $queries) ){
				$queries = str_replace("  ", " ", $queries);
			}
			
			// zjistim jestli se nejedna o viceslovny nazev produktu
			$queries = explode(" ", $queries);
			
			$or = array();
			foreach ( $queries as $key => $value ){
				$or[] = array(
					'OR' => array(
						'Product.id' => $value,
						"Product.name LIKE '%%" . $value . "%%'",
						"Product.title LIKE '%%" . $value . "%%'",
						"Product.heading LIKE '%%" . $value . "%%'",
						"Product.related_name LIKE '%%" . $value . "%%'",
						"Product.zbozi_name LIKE '%%" . $value . "%%'",
						"Product.short_description LIKE '%%" . $value . "%%'",
						"Product.description  LIKE '%%" . $value . "%%'",
						"Manufacturer.name  LIKE '%%" . $value . "%%'",
					)
				);
			}
			
			// idcka kategorii ze stromu s kategoriemi pro darky - root podstromu ma id 54
			App::import('Model', 'Product');
			$this->Search->Product = new Product;
			$present_category_ids = $this->Search->Product->CategoriesProduct->Category->subtree_ids(54);
			
			$conditions = array(
				'AND' => array(
					// podminka z formu pro vyhledavani
					$or,
					// chci jen aktivni produkty
					'Product.active' => true,
					// ktere nejsou jako darek k nakupu
					'CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')'
				)
			);
			
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
					'conditions' => array('Product.id = CategoriesProduct.product_id')
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
				),
				array(
					'table' => 'manufacturers',
					'alias' => 'Manufacturer',
					'type' => 'LEFT',
					'conditions' => array('Manufacturer.id = Product.manufacturer_id')
				)
			);
			
			$limit = $paging_options[$this->data['Search']['paging']];

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
					'Product.price',
					'Product.rate',
						
					'Image.id',
					'Image.name',
						
					'Availability.id',
					'Availability.cart_allowed'
				),
				'joins' => $joins,
				'group' => 'Product.id',
				'limit' => $limit,
			);
			
			// pokud je vybrano, ze se maji vypsat vsechny produkty
			if ($limit == ALL_STRING) {
				$this->paginate['Product']['show'] = 'all';
			}
			
			// sestavim podminku pro razeni podle toho, co je vybrano
			$order = array('Availability.cart_allowed' => 'desc');
			if (isset($this->data['Search']['sorting'])) {
				switch ($this->data['Search']['sorting']) {
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
	
			$this->Search->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
			// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
			// vezmu jako cenu produktu obycejnou cenu
			$this->Search->Product->virtualFields['price'] = $this->Search->Product->price;

			$products = $this->paginate('Product');

			// opetovne vypnuti virtualnich poli, nastavenych za behu
			unset($this->Search->Product->virtualFields['sold']);
			unset($this->Search->Product->virtualFields['price']);
		}
		$this->set('products', $products);
		
		$breadcrumbs = array(array('href' => $this->params['url']['url'], 'anchor' => 'Vyhledávání produktů'));
		$this->set('breadcrumbs', $breadcrumbs);
		
		$this->set('listing_style', 'products_listing_grid');
		
		App::import('Model', 'Product');
		$this->Search->Product = &new Product;
		$action_products = $this->Search->Product->get_action_products($customer_type_id, 4);
		$this->set('action_products', $action_products);
	}
}
?>