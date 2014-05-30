<?
class ExportsController extends AppController{
	var $name = 'Exports';
	
	function get_products() {
		// natahnu si model Product
		App::Import('model', 'Product');
		$this->Product = &new Product;
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		// idcka kategorii s darky
		$present_category_ids = array(54, 67, 68);
		// samostatne neprodejne darky
		$presents = $this->Product->find('all', array(
			'conditions' => array('CategoriesProduct.category_id IN (' . implode(',', $present_category_ids) . ')'),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('CategoriesProduct.product_id = Product.id')
				)
			),
			'fields' => array('Product.id', 'Product.name')
		));
		
		//ids samotstatne neprodejnych darku
		$present_ids = Set::extract('/Product/id', $presents);
		
		$conditions = array(
			"Product.short_description != ''",
			'Availability.cart_allowed' => true,
			'Product.active' => true,
		);
		
		if (!empty($present_ids)) {
			$conditions[] = 'Product.id NOT IN (' . implode(',', $present_ids) . ')';
		}
		$this->Product->virtualFields['price'] = $this->Product->price;
		$products = $this->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'TaxClass' => array(
					'fields' => array('id', 'value')
				),
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
			),
			'joins' => array(
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				),
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('CategoriesProduct.product_id = Product.id')
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => array('Category.id = CategoriesProduct.category_id')
				)
			),
			'fields' => array(
				'DISTINCT Product.id',
				'Product.name',
				'Product.short_description',
				'Product.url',
				'Product.zbozi_name',
				'Product.price',
				
				'Image.id',
				'Image.name',
					
				'Availability.id',
				'Availability.name',
					
				'CategoriesProduct.id',
				'CategoriesProduct.product_id',
				'CategoriesProduct.category_id',
					
				'Category.id',
				'Category.name',
					
				'TaxClass.id',
				'TaxClass.value',
					
				'Manufacturer.id',
				'Manufacturer.name'
			)
		));
		unset($this->Product->virtualFields['price']);
		
		foreach ($products as $i => $product) {
			$products[$i]['Product']['name'] = str_replace('&times;', 'x', $products[$i]['Product']['name']);
			$products[$i]['Product']['short_description'] = str_replace('&times;', 'x', $products[$i]['Product']['short_description']);
		}

		return $products;
	}
	
	function seznam_cz(){
		// nastavim si layout do ktereho budu cpat data v XML
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products();
		$this->set('products', $products);
		
		// produkty zobrazovane na detailu na firmy.cz
		$this->set('firmy_cz_products', array(762, 971, 880, 363, 654));
	}
	
	function heureka_cz() {
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products();
		
		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Aminokyseliny' => array(7, 21, 22, 23, 24),
			'Anabolizéry a NO doplňky' => array(12),
			'Gely' => array(),
			'Iontové nápoje' => array(),
			'Kloubní výživa' => array(9),
			'Kreatin' => array(10, 32, 33, 34),
			'Nutriční doplňky' => array(58),
			'Proteiny' => array(13, 40, 41, 42),
			'Sacharidy a gainery' => array(16, 43, 44, 45),
			'Stimulanty a energizéry' => array(),
			'Tyčinky' => array(),
			'Vitamíny a minerály' => array(20)
		);

		foreach ($products as $index => $product) {
			// pokud je kategorie produktu sparovana s heurekou, nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$products[$index]['CATEGORYTEXT'] = 'Sportovní výživa | ' . $name;
					break;
				}
			}

			// jinak se vytvori retezec ze stromu kategorii v obchode
			if (!isset($products[$index]['CATEGORYTEXT'])) {
				$path = $this->Product->CategoriesProduct->Category->getPath($product['CategoriesProduct']['category_id']);
				$keys = Set::extract('/Category/name', $path);
				unset($keys[0]);
				$products[$index]['CATEGORYTEXT'] = implode(' | ', $keys);
			}
		}
	
		$this->set('products', $products);
		
		// udaje o moznych variantach dopravy
		App::import('Model', 'Shipping');
		$this->Shipping = new Shipping;
		// vytahnu si vsechny zpusoby dopravy
		$shippings = $this->Shipping->find('all', array(
			'conditions' => array('NOT' => array('Shipping.heureka_id' => null)),
			'contain' => array(),
			'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.price', 'Shipping.free', 'Shipping.heureka_id')
		));
		$this->set('shippings', $shippings);
	}
}
?>