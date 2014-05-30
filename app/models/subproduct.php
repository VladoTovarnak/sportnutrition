<?php
class Subproduct extends AppModel {
	var $name = 'Subproduct';

	var $actsAs = array('Containable');

	
	var $belongsTo = array(
		'Product',
		'Availability'
	);

	var $hasMany = array(
		'AttributesSubproduct' => array(
			'dependent' => true
		),
		'CartsProduct'
	);
	
	function countSortOrder($attribute_id, $product_id){
		$attribute = $this->Attribute->read(null, $attribute_id);
		$bottom_level_left = $attribute['Attribute']['option_id'] * 10;
		$bottom_level_right = ($attribute['Attribute']['option_id'] + 1) * 10;
		$conditions = array(
			'product_id' => $product_id,
			'sort_order' => 'BETWEEN ' . $bottom_level_left . ' AND ' . $bottom_level_right
		);

		$bottom_level = $this->find($conditions, 'MAX(sort_order) as bottom_level');

		// jestlize atribut neni jeste k produktu prirazeny,
		// vraci mi to prazdny zaznam
		if ( empty($bottom_level[0]['bottom_level']) ){
			return $bottom_level_left;
		}
		return $bottom_level[0]['bottom_level'];
	}
	
	function optionFilter($params){
		$filter = '';
		if ( isset($params['option_id']) ){
			$filter = 'option_id:' . $params['option_id'];
		}
		return $filter;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		$this->truncate();
		$this->AttributesSubproduct->truncate();
		// vzestupne serazene sn id produktu s atributy
		$snProducts = $this->Product->findWithAttributesSn();

		// pro kazdy produkt s atributy
		foreach ($snProducts as $snProduct) {
			// zjistim produkt v nasem systemu s odpovidajicim sn id
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.sportnutrition_id' => $snProduct['SnProduct']['sportnutrition_id']),
				'contain' => array(),
				'fields' => array('Product.id', 'Product.sportnutrition_id')
			));

			if (empty($product)) {
				// pokud narazim v tabulce vztahu mezi atributy a produkty na produkt, ktery neni v aktualni tabulce produktu (pohrobek), preskocim a pokracuji dal
				continue;
			}

			// najdu zaznam s informaci o atributech podle daneho product sn id
			$snProductAttributes = $this->findProductAttributesSn($product['Product']['sportnutrition_id']);

			// vygeneruju subprodukty
			$subproductAttributes = $this->generateSubproductAttributes($snProductAttributes);
		
			// ulozim subprodukty (se vztahy k atributum)
			foreach ($subproductAttributes as $subproductAttribute) {
				$subproduct = $this->transformSn($subproductAttribute, $product);
				if (!$this->saveAll($subproduct)) {
					debug($subproduct);
				}
			}
			
		}
		return true;
	}
	
	function generateSubproductAttributes($snProductAttributes) {
		$combineInput = array();
		foreach ($snProductAttributes as $snProductAttribute) {
			$option = $this->AttributesSubproduct->Attribute->Option->findByName($snProductAttribute['SnProductAttribute']['nazev_cz']);
			if (!empty($option)) {
				$optionId = $option['Option']['id'];
				$snAttributes = explode('|=|', $snProductAttribute['SnProductAttribute']['hodnoty_cz']);
				$attributeIds = array();
				foreach ($snAttributes as $snAttribute) {
					$attribute = $this->AttributesSubproduct->Attribute->findByValue($optionId, $snAttribute);
					if (!empty($attribute)) {
						$attributeIds[] = $attribute['Attribute']['id'];
					}
				}
				$combineInput[$optionId] = $attributeIds;
			}
		}

		$subproductAttributes = $this->Product->combine($combineInput);
		
		return $subproductAttributes;
	}
	
	function findProductAttributesSn($sportnutrition_id) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM products_povinne_select AS SnProductAttribute
			WHERE SnProductAttribute.product_id = ' . $sportnutrition_id;

		$snProductAttributes = $this->query($query);
		$this->setDataSource('default');
		return $snProductAttributes;
	}
	
	function transformSn($subproductAttribute, $product) {
		$attributes = array();
		foreach ($subproductAttribute as $attributeId) {
			$attributes[] = array('attribute_id' => $attributeId);
		}
		
		$subproduct = array(
			'Subproduct' => array(
				'product_id' => $product['Product']['id'],
				'active' => true,
			),
			'AttributesSubproduct' => $attributes
		);

		return $subproduct;
	}
}
?>