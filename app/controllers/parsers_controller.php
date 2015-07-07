<?php
class ParsersController extends AppController {
	
	function admin_parse($manufacturer_id = null) {
		if (!$manufacturer_id) {
			die('Zadejte id vyrobce');
		}
		$this->Parser->manufacturer_id = $manufacturer_id;
		// url xml feedu
		$url = 'http://www.madmax-shop.cz/export/zbozi.xml';
		
		// kam chci produkty natahnout
		App::import('Model', 'Category');
		$this->Category = &new Category;
		$category_id = $this->Category->find('first', array(
			'conditions' => array('Category.name' => 'madmax nove produkty'),
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
				$images_urls = $this->Parser->images_urls($feed_product);
				$del_images_conditions = array();
				foreach ($images_urls as $image_url) {
					// stahnu a ulozim obrazek, pokud je treba
					$save_image_end = $this->Parser->nutrend_image_save($product_id, $image_url);
			
					// pokud nenastala chyba pri ukladani obrazku, smazu vsechny obrazky u produktu, ktere jiz nejsou aktualni
					if ($save_image_end) {
						$del_images_conditions[] = array(
							'Image.product_id' => $product_id,
							'Image.supplier_url !=' => $image_url
						);
					}
				}
				if (!empty($del_images_conditions)) {
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
				
				// VARIANTY PRODUKTU
				// pokud se mi podari vyparsovat nove varianty produktu
				$subproducts = $this->Parser->zbozi_subproducts($feed_product, $product_id);
				// smazu vsechny dosavadni
				$delete_subproducts_conditions = array('Subproduct.product_id' => $product_id);
				$this->Parser->Product->Subproduct->deleteAll($delete_subproducts_conditions);
				// a nahraju nove
				foreach ($subproducts as $subproduct) {
					$this->Parser->Product->Subproduct->saveAll($subproduct);
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