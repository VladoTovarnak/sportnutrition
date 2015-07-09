<?php
class Parser extends AppModel {
	var $useTable = false;
	
	function nutrend_product($feed_product) {
		$manufacturer_id = null;
		if ($this->manufacturer_id) {
			$manufacturer_id = $this->manufacturer_id;
		}

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
			// 		- danova trida
			$tax_class_id = $this->nutrend_product_tax_class_id($feed_product);
			//		- dalsi nastaveni
			$active = false;
			$feed = false;
			$supplier_id = $manufacturer_id;
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
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_heading($feed_product) {
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_breadcrumb($feed_product) {
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_related_name($feed_product) {
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_zbozi_name($feed_product) {
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_title($feed_product) {
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_short_description($feed_product) {
		return 'MadMax ' . $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_description($feed_product) {
		$description = $feed_product->DESCRIPTION->__toString();
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
		return $feed_product->PRODUCTNAME->__toString();
	}
	
	function nutrend_product_tax_class_id($feed_product) {
		$price = $feed_product->PRICE->__toString();
		$price_vat = $feed_product->PRICE_VAT->__toString();
		$tax_class = false;
		if ($price && $price_vat) {
			$tax_class = round((100 * $price_vat / $price) - 100);
		}
		if (!$tax_class) {
			$tax_class = 21;
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
	
	function images_urls($feed_product) {
		$urls = $feed_product->IMGURL;
		$res = array();
		foreach ($urls as $url) {
			$res[] = $url->__toString();
		}
		return $res;
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
				$is_main = $this->Product->Image->isMain($product_id);

				$save_image = array(
					'Image' => array(
						'name' => $image_name,
						'product_id' => $product_id,
						'is_main' => $is_main,
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
	
	function zbozi_subproducts($feed_product, $product_id) {
		$variants = $feed_product->VARIANT;
		$res = array();
		
		App::import('Model', 'Attribute');
		$this->Attribute = &new Attribute;

		$variant_info_separator = ';';
		$attribute_info_separator = ':';
		
		$subproducts = array();
		
		foreach ($variants as $variant) {
			$variant_info = $variant->PRODUCTNAMEEXT->__toString();
			$variant_price = $variant->PRICE_VAT->__toString();
			// predpokladam, ze info a variante produktu je ulozeno ve tvaru Option1:Attribute1;Option2=Attribute2'..., takze napr: Velikost: S;Barva: bila
			$variant_info = explode($variant_info_separator, $variant_info);
			$attribute_ids = array();
			foreach ($variant_info as $attribute_info) {
				$attribute_info = explode($attribute_info_separator, $attribute_info);
				if (count($attribute_info) != 2) {
					debug($attribute_info);
					trigger_error('Nepodarilo se vyparsovat varianty produktu ' . $product_id, E_USER_NOTICE);
					return false;
				} else {
					$option_name = trim($attribute_info[0]);
					$attribute_value = trim($attribute_info[1]);
					
					$db_option = $this->Attribute->Option->findByName($option_name);
					if (empty($db_option)) {
						$option_save = array(
							'Option' => array(
								'name' => $option_name
							)
						);
						$this->Attribute->Option->create();
						if ($this->Attribute->Option->save($option_save)) {
							$option_id = $this->Attribute->Option->id;
						} else {
							debug($option_save);
							trigger_error('Nepodarilo se ulozit nazev tridy atributu ' . $option_name, E_USER_NOTICE);
							return false;
						}
					} else {
						$option_id = $db_option['Option']['id'];
					}
					
					$db_attribute = $this->Attribute->findByValue($option_id, $attribute_value);
					if (empty($db_attribute)) {
						$attribute_save = array(
							'Attribute' => array(
								'option_id' => $option_id,
								'value' => $attribute_value
							)
						);
						$this->Attribute->create();
						if ($this->Attribute->save($attribute_save)) {
							$attribute_id = $this->Attribute->id;
						} else {
							debug($attribute_save);
							trigger_error('Nepodarilo se ulozit hodnotu atributu ' . $attribute_value, E_USER_NOTICE);
							return false;
						}
					} else {
						$attribute_id = $db_attribute['Attribute']['id'];
					}
					
					$attribute_ids[] = $attribute_id;
				}
			}
			if (empty($attribute_ids)) {
				trigger_error('Nepodarilo se vyparsovat atributy?', E_USER_NOTICE);
				return false;
			} else {
				$product_price = $this->nutrend_product_retail_price_with_dph($feed_product, 'PRICE_VAT');
				// vygeneruju subproduct
				$subproduct = array(
					'Subproduct' => array(
						'price_with_dph' => $product_price - $variant_price,
						'active' => true,
						'product_id' => $product_id
					),
					'AttributesSubproduct' => array()
				);
				foreach ($attribute_ids as $attribute_id) {
					$subproduct['AttributesSubproduct'][] = array('attribute_id' => $attribute_id);
				}
				$subproducts[] = $subproduct;
			}
		}
		return $subproducts;
	}
}
?>