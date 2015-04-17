<?php
class ParsersController extends AppController {
	
	function admin_parse_nutrend() {
		// url xml feedu
		$url = 'http://www.nutrend.cz/db/xml/fullexport.xml';
		// idcka produktu, ktere chci vyparsovat
		$supplier_product_ids = array(
			//http://www.nutrend.cz/madmax/c376/p3/
			12394, 12410, 12415, 12412, 12413,
			12434, 12427, 12407, 12439, 12421,
			12398, 12399, 12432, 12436, 12411,
			12417, 12418, 12420, 12419, 12391,
			
			12395, 12396, 12438, 12404, 12402,
			12426, 12392, 12393, 12433, 12409,
			12405, 12406, 12408, 12428, 12429,
			12400, 12401, 12416, 12435, 12403,
				
			12431,
				
			//http://www.nutrend.cz/energie/c357/
			12442, 12445, 13838, 12508, 12473,
			12533, 12443, 12546, 13841, 12454,
			12463, 13735
		);
		// kam chci produkty natahnout
		App::import('Model', 'Category');
		$this->Category = &new Category;
		$category_id = $this->Category->find('first', array(
			'conditions' => array('Category.name' => 'nutrend nove produkty'),
			'contain' => array(),
			'fields' => array('Category.id')
		));
		if (empty($category_id)) {
			die('neni dana kategorie, kam chci produkty importovat');
		}
		$category_id = $category_id['Category']['id'];
		
		// stahnu feed (je tam vubec)
		if (!$xml = download_url($url)) {
			trigger_error('Chyba při stahování URL ' . $url, E_USER_ERROR);
			die();
		}

		$products = new SimpleXMLElement($xml);
		
		App::import('Model', 'Product');
		$this->Parser->Product = &new Product;
		foreach ($products->SHOPITEM as $feed_product) {
			// budu preskakovat produkty, ktere nechci
			if (!in_array($this->Parser->nutrend_product_supplier_product_id($feed_product), $supplier_product_ids)) {
				continue;
			}
			$product = $this->Parser->nutrend_product($feed_product);

			// produkt jsem v poradku vyparsoval
			if ($product) {
				// pokud mam v systemu produkt s danym id produktu ve feedu poskytovatele, budu hodnoty updatovat
				$db_product = $this->Parser->Product->find('first', array(
					'conditions' => array(
						'Product.supplier_id' => $product['Product']['supplier_id'],
						'Product.supplier_product_id' => $product['Product']['supplier_product_id']
					),
					'contain' => array(),
				));
		
				$data_source = $this->Parser->Product->getDataSource();
				$data_source->begin($this->Parser->Product);
				// produkt uz mam v databazi
				if (!empty($db_product)) {
					$product['Product']['id'] = $db_product['Product']['id'];
					// nastavim active produktu podle active u supplier_category (nastavene v parovani kategorii)
				} else {
					$this->Parser->Product->create();
				}

				// ulozim produkt
				if (!$this->Parser->Product->save($product)) {
					debug($product);
					trigger_error('Nepodarilo se ulozit produkt', E_USER_NOTICE);
					$data_source->rollback($this->Supplier->Product);
					continue;
				}
				$product_id = $this->Parser->Product->id;
					
				// ulozim url produktu
				$product_url_update = array(
					'Product' => array(
						'id' => $product_id,
						'url' => $this->Parser->Product->buildUrl($product)
					)
				);

				if (!$this->Parser->Product->save($product_url_update)) {
					debug($product_url_update);
					trigger_error('Nepodarilo se ulozit URL produktu', E_USER_NOTICE);
					$data_source->rollback($this->Parser->Product);
				}
					
				// OBRAZKY
				// zjistim url obrazku
				$image_url = $this->Parser->nutrend_image_url($feed_product);

				// stahnu a ulozim obrazek, pokud je treba
				$save_image_end = $this->Parser->nutrend_image_save($product_id, $image_url);
		
				// pokud nenastala chyba pri ukladani obrazku, smazu vsechny obrazky u produktu, ktere jiz nejsou aktualni
				if ($save_image_end) {
					$del_images_conditions = array(
						'Image.product_id' => $product_id,
						'Image.supplier_url !=' => $image_url
					);
		
					$this->Parser->Product->Image->deleteAllImages($del_images_conditions);
				}

				// KATEGORIE
				// zjistim kategorii pro produkt z feedu
				if ($category_id) {
					$categories_product = array(
						'CategoriesProduct' => array(
							'category_id' => $category_id,
							'product_id' => $product_id,
						)
					);
					// podivam se, jestli mam dany produkt pridelen do dane naparovane kategorie
					$db_categories_product = $this->Parser->Product->CategoriesProduct->find('first', array(
						'conditions' => $categories_product['CategoriesProduct'],
						'contain' => array()
					));
					// pokud nemam produkt v te kategorii
					if (empty($db_categories_product)) {
						// vlozim ho tam
						$this->Parser->Product->CategoriesProduct->create();
						if ($this->Parser->Product->CategoriesProduct->save($categories_product)) {
							// smazu vsechny ostatni prirazeni produktu do kategorii vznikle naparovanim produktu
							$this->Parser->Product->CategoriesProduct->deleteAll(array(
								'CategoriesProduct.product_id' => $product_id,
								'CategoriesProduct.category_id !=' => $category_id
							));
						} else {
							debug($categories_product);
							trigger_error('Nepodarilo se ulozit prirazeni produktu do kategorie: ' . $product_id . ' - ' . $category_id, E_USER_NOTICE);
						}
					}
				}
					
				// CENY V CENOVYCH SKUPINACH
				$product_prices = array();
				$customer_types = $this->Parser->Product->CustomerTypeProductPrice->CustomerType->find('all', array(
					'contain' => array()
				));
					
				foreach ($customer_types as $customer_type) {
					$customer_type_product_price = array(
						'customer_type_id' => $customer_type['CustomerType']['id'],
						'product_id' => $product_id
					);
		
					$db_unlogged_customer_price = $this->Parser->Product->CustomerTypeProductPrice->find('first', array(
						'conditions' => array($customer_type_product_price),
						'contain' => array(),
						'fields' => array('CustomerTypeProductPrice.id')
					));
		
					if (!empty($db_unlogged_customer_price)) {
						$customer_type_product_price['id'] = $db_unlogged_customer_price['CustomerTypeProductPrice']['id'];
					}
		
					$customer_type_product_price['price'] = null;
					$product_prices[] = $customer_type_product_price;
				}
		
				if (!$this->Parser->Product->CustomerTypeProductPrice->saveAll($product_prices)) {
					debug($product_prices);
					trigger_error('Nepodarilo se ulozit ceny produktu', E_USER_NOTICE);
				}
					
				$data_source->commit($this->Parser->Product);
					
				$supplier_product_ids[] = $product_id;
			} else {
				debug($feed_product);
				trigger_error('Nepodarilo se vyparsovat informace o produktu', E_USER_NOTICE);
				continue;
			}
		}
	
		die('hotovo');
	}
}