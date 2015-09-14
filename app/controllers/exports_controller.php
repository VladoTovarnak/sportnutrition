<?
class ExportsController extends AppController{
	var $name = 'Exports';
	
	function get_products($comparator_id) {
		// natahnu si model Product
		App::Import('model', 'Product');
		$this->Product = &new Product;
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		// idcka kategorii s darky
		$present_category_ids = $this->Tool->present_category_ids;
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
		
		// google merchant center : pokud neznam dilci hodnotu priznaku pro vlozeni produktu do feedu, nechci tam produkty vlozit
		if ($comparator_id == 3) {
			$conditions['ComparatorProductClickPrice.feed'] = true;
		// jinak (u zbozi a heureky) : pokud neznam dilci hodnotu priznaku pro vlozeni produktu do feedu (je null nebo 0), orientuju se podle te globalni
		} else {
			$conditions['OR'] = array(
				array('ComparatorProductClickPrice.feed' => true),
				array('OR' => array(
						array('ComparatorProductClickPrice.feed IS NULL AND Product.feed = 1'),
						array('ComparatorProductClickPrice.feed = 0 AND Product.feed = 1')
					)
				)
			);
		}
		
		if (!empty($present_ids)) {
			$conditions[] = 'Product.id NOT IN (' . implode(',', $present_ids) . ')';
		}
		
		// kategorie chci vybirat jine, nez doprava zdarma
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$category_id_condition = '';
		if ($free_shipping_category_id = $this->Setting->findValue('FREE_SHIPPING_CATEGORY_ID')) {
			$category_id_condition = ' AND CategoriesProduct.category_id != ' . $free_shipping_category_id;
		}
		
		$contain = array(
			'TaxClass' => array(
				'fields' => array('id', 'value')
			),
			'Manufacturer' => array(
				'fields' => array('id', 'name')
			)
		);
		
		// do feedu pro zbozi chci k produktu vkladat varianty
		if ($comparator_id == 2) {
			$contain = array_merge($contain, array(
				'Subproduct' => array(
					'conditions' => array(
						'Subproduct.active' => true			
					),
					'AttributesSubproduct' => array(
						'Attribute' => array(
							'fields' => array(
								'Attribute.id', 'Attribute.value' 
							),
							'Option' => array(
								'fields' => array(
									'Option.id', 'Option.name'
								)
							)
						)
					)
				)	
			));
		}

		$this->Product->virtualFields['price'] = $this->Product->price;
		$products = $this->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => $contain,
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
					'conditions' => array('CategoriesProduct.product_id = Product.id' . (!empty($category_id_condition) ? $category_id_condition : ''))
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => array('Category.id = CategoriesProduct.category_id AND Category.active=1')
				),
				array(
					'table' => 'comparator_product_click_prices',
					'alias' => 'ComparatorProductClickPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = ComparatorProductClickPrice.product_id AND ComparatorProductClickPrice.comparator_id = ' . $comparator_id)
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.short_description',
				'Product.url',
				'Product.zbozi_name',
				'Product.heureka_name',
				'Product.price',
				'Product.ean',
				
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
				'Manufacturer.name',
					
				'ComparatorProductClickPrice.id',
				'ComparatorProductClickPrice.click_price'
			),
			'order' => array('Product.id' => 'asc'),
//			'limit' => 10
		));
		unset($this->Product->virtualFields['price']);

		$res = array();
		foreach ($products as $i => &$product) {
			// kazdy produkt chci ve vystupu pouze jednou
			$to_res = true;
			foreach ($res as $r) {
				if ($r['Product']['id'] == $product['Product']['id']) {
					$to_res = false;
					break;
				}
			}
			if ($to_res) {
				$product['Product']['name'] = str_replace('&times;', 'x', $product['Product']['name']);
				$product['Product']['short_description'] = str_replace('&times;', 'x', $product['Product']['short_description']);
				$res[] = $product;				
			}

		}
		return $res;
	}
	
	function seznam_cz() {
		// nastavim si layout do ktereho budu cpat data v XML
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products(2);
		$this->set('products', $products);
		
		// produkty zobrazovane na detailu na firmy.cz
		$this->set('firmy_cz_products', array(762, 971, 880, 363, 654));
	}
	
	function heureka_cz() {
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products(1);

		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Sport | Sportovní výživa | Aminokyseliny' => array(15, 57, 58, 59, 60, 87, 88, 89, 61, 62),
			'Sport | Sportovní výživa | Proteiny' => array(16, 67, 68, 69, 70),
			'Sport | Sportovní výživa | Sacharidy a gainery' => array(17, 71, 72, 73),
			'Sport | Sportovní výživa | Kreatin' => array(18, 63, 64),
			'Sport | Sportovní výživa | Anabolizéry a NO doplňky' => array(78, 20),
			'Sport | Sportovní výživa | Spalovače tuků' => array(21, 74, 75, 76),
			'Sport | Sportovní výživa | Kloubní výživa' => array(22, 65, 66),
			'Sport | Sportovní výživa | Vitamíny a minerály' => array(23, 81, 82),
			'Sport | Sportovní výživa | Ostatní sportovní výživa' => array(19, 77, 80, 28, 25, 26, 14, 6, 7, 9),
			'Sport | Sportovní výživa | Stimulanty a energizéry' => array(79),
			'Sport | Sportovní výživa | Iontové nápoje' => array(24, 83, 84, 85, 86),
			'Sport | Fitness | Opasky, háky a fitness rukavice' => array(40, 41),
			'Dům a zahrada | Bydlení a doplňky | Kuchyně | Kuchyňské náčiní | Shakery' => array(42),
			'Oblečení a móda' => array(11),
			'Oblečení a móda | Pánské oblečení' => array(90),
			'Oblečení a móda | Pánské oblečení | Pánské kalhoty' => array(45),
			'Oblečení a móda | Pánské oblečení | Pánské šortky' => array(46),
			'Oblečení a móda | Pánské oblečení | Pánská trička' => array(47),
			'Oblečení a móda | Pánské oblečení | Pánské mikiny' => array(48),
			'Oblečení a móda | Pánské oblečení | Pánské mikiny' => array(48),
			'Oblečení a móda | Módní doplňky | Zimní čepice' => array(51),
			'Oblečení a móda | Dámské oblečení' => array(91, 50),
			'Oblečení a móda | Dámské oblečení | Dámské mikiny' => array(92),
			'Oblečení a móda | Dámské oblečení | Dámská trička' => array(93),
			'Oblečení a móda | Dámské oblečení | Dámské kalhoty' => array(49),
			'Oblečení a móda | Dámské oblečení | Dámské šortky' => array(94),
			'Sport | Fitness | Činky a příslušenství' => array(29),
			'Sport | Fitness | Rotopedy' => array(30),
			'Sport | Fitness | Eliptické trenažéry' => array(31),
			'Sport | Fitness | Steppery' => array(32),
			'Sport | Fitness | Ostatní fitness nářadí' => array(33, 43, 44, 13),
			'Sport | Fitness | Běžecké pásy' => array(34),
			'Sport | Fitness | Veslovací trenažéry' => array(35),
			'Sport | Fitness | Cyklotrenažéry' => array(36),
			'Sport | Fitness | Posilovací lavice' => array(37),
			'Sport | Fitness | Posilovací věže' => array(38)
		);
		
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		// vytahnu si vsechny zpusoby dopravy
		$shippings = $this->Shipping->find('all', array(
			// do exportu budu davat jen PPL, GP a CP do ruky
			'conditions' => array('Shipping.id' => array(2,3,7)),
			'contain' => array(),
			'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.price', 'Shipping.free', 'Shipping.heureka_id')
		));
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$free_shipping_category_id = $this->Setting->findValue('FREE_SHIPPING_CATEGORY_ID');

		foreach ($products as $index => $product) {
			// pokud je kategorie produktu sparovana s heurekou, nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$products[$index]['CATEGORYTEXT'] = $name;
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

			$products[$index]['shippings'] = array();

			foreach ($shippings as $shipping) {
				$shipping_name = $shipping['Shipping']['heureka_id'];
				
				// pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
				$shipping_price = ceil($shipping['Shipping']['price']);
				if ($shipping['Shipping']['free'] != 0 && $product['Product']['price'] > $shipping['Shipping']['free']) {
					$shipping_price = 0;
				// pokud je produkt v kategorii "doprava zdarma", je doprava zdarma
				} elseif ($free_shipping_category_id && $this->Product->in_category($product['Product']['id'], $free_shipping_category_id)) {
					$shipping_price = 0;
				}

				$products[$index]['shippings'][] = array(
					'name' => $shipping_name,
					'price' => $shipping_price
				);
			}
		}

		$this->set('products', $products);
	}
	
	function google_merchant() {
		// bez layoutu
		$this->autoLayout = false;
		
		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Zdraví a krása > Zdravotní péče > Fitness a výživa' => array(1, 2, 6, 7, 9, 25, 26, 28, 14),	
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Doplňky na zvýšení růstu svalové hmoty' => array(15, 57, 58, 59, 60, 87, 88, 89, 61, 62, 16, 67, 68, 69, 70, 17, 71, 72, 73, 18, 63, 64, 19, 77, 78, 79, 80, 20),
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Vitamíny a výživové doplňky' => array(21, 74, 75, 76, 22, 65, 66, 23, 81, 82, 24, 83, 84, 85, 86),
			'Média > Knihy > Naučná a odborná literatura > Knihy o zdraví a fitness' => array(12),
			'Sportovní potřeby > Cvičení a fitness' => array(10, 40, 41, 42, 43, 44, 13, 33, 38),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení' => array(11, 90, 91, 50, 51),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní kalhoty' => array(45, 49),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní šortky' => array(46, 94),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní trika' => array(47, 93),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Mikiny' => array(48, 92),
			'Sportovní potřeby > Cvičení a fitness > Činky' => array(29),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Spinningová kola' => array(30, 36),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Šlapací trenažéry' => array(31),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Běžecké trenažéry' => array(34),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Veslařské trenažéry' => array(35),
			'Sportovní potřeby > Cvičení a fitness > Vzpěračské lavice' => array(37),
		);
		
		$products = $this->get_products(3);
		
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		foreach ($products as $index => &$product) {
			// pokud je produkt v kategorii 77 - pripravky s tribulusem - nechci ho do feedu
			$categories = $this->Category->CategoriesProduct->find('all', array(
				'conditions' => array('CategoriesProduct.product_id' => $product['Product']['id']),
				'contain' => array(),
				'fields' => array('CategoriesProduct.category_id')
			));
			$categories = Set::extract('/CategoriesProduct/category_id', $categories);
			if (in_array(77, $categories)) {
				unset($products[$index]);
				continue;
			}
			// chci odchytit produkty, ktere maji v nekde textu "tribulus"
			if (preg_match('/tribulus/i', $product['Product']['name']) || preg_match('/tribulus/i', $product['Product']['short_description'])) {
				unset($products[$index]);
				continue;
			}
			
			$product['Product']['category_text'] = '';
			// pokud je kategorie produktu sparovana , nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$product['Product']['category_text'] = $name;
					break;
				}
			}

			$product['Product']['type_text'] = $this->Category->getPath($product['CategoriesProduct']['category_id']);
			$product['Product']['type_text'] = Set::extract('/Category/name', $product['Product']['type_text']);
			$product['Product']['type_text'] = implode(' | ', $product['Product']['type_text']);
		}
		$this->set('products', $products);
	}
	
	function facebook() {
		// bez layoutu
		$this->autoLayout = false;
		
		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Zdraví a krása > Zdravotní péče > Fitness a výživa' => array(1, 2, 6, 7, 9, 25, 26, 28, 14),
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Doplňky na zvýšení růstu svalové hmoty' => array(15, 57, 58, 59, 60, 87, 88, 89, 61, 62, 16, 67, 68, 69, 70, 17, 71, 72, 73, 18, 63, 64, 19, 77, 78, 79, 80, 20),
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Vitamíny a výživové doplňky' => array(21, 74, 75, 76, 22, 65, 66, 23, 81, 82, 24, 83, 84, 85, 86),
			'Média > Knihy > Naučná a odborná literatura > Knihy o zdraví a fitness' => array(12),
			'Sportovní potřeby > Cvičení a fitness' => array(10, 40, 41, 42, 43, 44, 13, 33, 38),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení' => array(11, 90, 91, 50, 51),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní kalhoty' => array(45, 49),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní šortky' => array(46, 94),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní trika' => array(47, 93),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Mikiny' => array(48, 92),
			'Sportovní potřeby > Cvičení a fitness > Činky' => array(29),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Spinningová kola' => array(30, 36),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Šlapací trenažéry' => array(31),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Běžecké trenažéry' => array(34),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Veslařské trenažéry' => array(35),
			'Sportovní potřeby > Cvičení a fitness > Vzpěračské lavice' => array(37),
		);
		
		$products = $this->get_products(4);
		
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		// vytahnu si vsechny zpusoby dopravy
		$shippings = $this->Shipping->find('all', array(
			// do exportu budu davat jen PPL, GP a CP do ruky
			'conditions' => array('Shipping.id' => array(2,3,7)),
			'contain' => array(),
			'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.price', 'Shipping.free', 'Shipping.heureka_id')
		));
		
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$free_shipping_category_id = $this->Setting->findValue('FREE_SHIPPING_CATEGORY_ID');
		
		foreach ($products as $index => &$product) {
			$product['Product']['category_text'] = '';
			// pokud je kategorie produktu sparovana , nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$product['Product']['category_text'] = $name;
					break;
				}
			}
			
			$products[$index]['shippings'] = array();
			
			foreach ($shippings as $shipping) {
				$shipping_name = $shipping['Shipping']['heureka_id'];
			
				// pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
				$shipping_price = ceil($shipping['Shipping']['price']);
				if ($shipping['Shipping']['free'] != 0 && $product['Product']['price'] > $shipping['Shipping']['free']) {
					$shipping_price = 0;
					// pokud je produkt v kategorii "doprava zdarma", je doprava zdarma
				} elseif ($free_shipping_category_id && $this->Product->in_category($product['Product']['id'], $free_shipping_category_id)) {
					$shipping_price = 0;
				}
			
				$products[$index]['shippings'][] = array(
					'name' => $shipping_name,
					'price' => $shipping_price
				);
			}
		}
		$this->set('products', $products);
	}
}
?>