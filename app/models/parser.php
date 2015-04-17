<?php
class Parser extends AppModel {
	var $useTable = false;
	
	function nutrend_product($feed_product) {
		$product = array();
		// atributy produktu
		try {
			//		- nazev
			$name = $this->nutrend_product_name($feed_product);
			//		- heading
			$heading = $this->nutrend_product_heading($feed_product);
			//		- breadcrumb
			$breadcrumb = $this->nutrend_product_breadcrumb($feed_product);
			//		- related_name
			$related_name = $this->nutrend_product_related_name($feed_product);
			//		- zbozi_name
			$zbozi_name = $this->nutrend_product_zbozi_name($feed_product);
			//		- title
			$title = $this->nutrend_product_title($feed_product);
			//		- short_description
			$short_description = $this->nutrend_product_short_description($feed_product);
			//		- description
			$description = $this->nutrend_product_description($feed_product);
			//		- retail_price_with_dph
			$retail_price_with_dph = $this->nutrend_product_retail_price_with_dph($feed_product, 'PRICE_VAT');
			//		- discount_common
			$discount_common = null;
			//		- ean
			$ean = $this->nutrend_product_ean($feed_product);
			//		- supplier product id - id produktu ve feedu dodavatele
			$supplier_product_id = $this->nutrend_product_supplier_product_id($feed_product);
			// 		- dostupnost
			$availability_id = 1;
			// 		- vyrobce
			$manufacturer_id = 14;
			// 		- danova trida
			$tax_class_id = $this->nutrend_product_tax_class_id($feed_product);
			//		- dalsi nastaveni
			$active = false;
			$feed = false;
			$supplier_id = 14;
		} catch (Exception $e) {
			debug($feed_product);
			debug($e->getMessage());
			return false;
		}
		
		$product = array(
			'Product' => array(
				'name' => $name,
				'heading' => $heading,
				'breadcrumb' => $breadcrumb,
				'related_name' => $related_name,
				'zbozi_name' => $zbozi_name,
				'heureka_name' => $zbozi_name,
				'title' => $title,
				'short_description' => $short_description,
				'description' => $description,
				'ean' => $ean,
				'retail_price_with_dph' => $retail_price_with_dph,
				'discount_common' => $discount_common,
				'supplier_product_id' => $supplier_product_id,
				'supplier_id' => $supplier_id,
				'availability_id' => $availability_id,
				'manufacturer_id' => $manufacturer_id,
				'tax_class_id' => $tax_class_id,
				'active' => $active,
				'feed' => $feed
			)
		);
		return $product;
	}
		
	function nutrend_product_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_heading($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_breadcrumb($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_related_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_zbozi_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_title($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function nutrend_product_short_description($feed_product) {
		return $feed_product->ANOTATION->__toString();;
	}
	
	function nutrend_product_description($feed_product) {
		$description = '';
		foreach ($feed_product->DESCRIPTIONS as $item) {
			$description .= $item->ITEM->__toString();
		}
		$description = str_replace('<![CDATA[', '', $description);
		$description = str_replace(']]>', '', $description);
		$description = trim($description);
		return $description;
	}
	
	function nutrend_product_retail_price_with_dph($feed_product, $price_field ) {
		return $feed_product->$price_field->__toString();
	}
	
	function nutrend_product_ean($feed_product) {
		return $feed_product->EAN->__toString();
	}
	
	function nutrend_product_supplier_product_id($feed_product) {
		return $feed_product->CODE->__toString();
	}
	
	function nutrend_product_tax_class_id($feed_product) {
		$tax_class = $feed_product->VAT->__toString();
		if (!$tax_class) {
			throw new Exception('CHYBA PARSOVANI DANOVE TRIDY - Nepodařilo se vyparsovat daňovou třídu');
			return false;
		}
		$tax_class_id = false;
		App::import('Model', 'Product');
		$this->Product = &new Product;
		$db_tax_class = $this->Product->TaxClass->find('first', array(
				'conditions' => array('TaxClass.value' => $tax_class),
				'contain' => array(),
				'fields' => array('TaxClass.id')
		));
		if (empty($db_tax_class)) {
			throw new Exception('CHYBA PARSOVANI DANOVE TRIDY - Nepodařilo se nalézt daňovou třídu s hodnotou ' . $tax_class);
			return false;
		} else {
			return $db_tax_class['TaxClass']['id'];
		}
		return false;
	}
	
	function nutrend_image_url($feed_product) {
		return $feed_product->IMGURL->__toString();
	}
	
	function nutrend_image_save($product_id, $image_url) {
		if ($image_url) {
			$db_image = $this->Product->Image->find('first', array(
				'conditions' => array(
					'Image.product_id' => $product_id,
					'Image.supplier_url' => $image_url
				)
			));
	
			// v systemu obrazek z dane url pro dany produkt nemam
			if (empty($db_image)) {
				// nahraju obrazek
				$image_name = $this->Product->image_name($product_id);

				$save_image = array(
					'Image' => array(
						'name' => $image_name,
						'product_id' => $product_id,
						'is_main' => true,
						'supplier_url' => $image_url
					)
				);

				$this->Product->Image->create();
				if (!$this->Product->Image->save($save_image)) {
					trigger_error('Nepodarilo se ulozit obrazek', E_USER_NOTICE);
					return false;
				} else {
					// stahnu obrazek
					if ($image_content = download_url($image_url)) {
						// nahraju obrazek na disk
						if (file_put_contents('product-images/' . $image_name, $image_content)) {
							// pokud je obrazek typu PNG, musim mu udelat bile pozadi
							$file_ext = explode('.', $image_url);
							$file_ext = $file_ext[count($file_ext)-1];
							if ($file_ext == 'png' || $file_ext == 'PNG') {
								$file_path = 'product-images/' . $image_name;
								$save_path = 'product-images/' . $image_name;
								$color_rgb = array('red' => 255, 'green' => 255, 'blue' => 255);
	
								$img = @imagecreatefrompng($file_path);
								$width  = imagesx($img);
								$height = imagesy($img);
								//create new image and fill with background color
								$background_img = @imagecreatetruecolor($width, $height);
								$color = imagecolorallocate($background_img, $color_rgb['red'], $color_rgb['green'], $color_rgb['blue']);
								imagefill($background_img, 0, 0, $color);
	
								//copy original image to background
								imagecopy($background_img, $img, 0, 0, 0, 0, $width, $height);
	
								//save as png
								imagepng($background_img, $save_path, 0);
							}

							$this->Product->Image->makeThumbnails('product-images/' . $image_name);
							return true;
						} else {
							trigger_error('Nepodarilo se ulozit obrazek ' . $image_url . ' do ' . $image_name, E_USER_NOTICE);
						}
					} else {
						trigger_error('Nepodarilo se stahnout obrazek ' . $image_url, E_USER_NOTICE);
					}
				}
			}
		}
		return false;
	}
}
?>