<?php
class CategoriesProduct extends AppModel {

	var $actsAs = array('Containable');
	
	var $name = 'CategoriesProduct';
	
	var $belongsTo = array('Category', 'Product');
	
	/*
	 * Natahne sportnutrition data
	*/
	function import($truncate = true) {
		// vyprazdnim tabulku
		$condition = null;
		if ($truncate) {
			$this->truncate();
		} else {
			$snCategoriesProducts = $this->find('all', array(
				'contain' => array(),
				'fields' => array('CategoriesProduct.sportnutrition_id')	
			));
			$snCategoriesProducts = Set::extract('/CategoriesProduct/sportnutrition_id', $snCategoriesProducts);
			
			$condition = 'SnCategoriesProduct.id NOT IN (' . implode(',', $snCategoriesProducts) . ')';
		}
		
		$snCategoriesProducts = $this->findAllSn($condition);

		$save = array('CategoriesProduct');
		foreach ($snCategoriesProducts as $snCategoriesProduct) {
			$save['CategoriesProduct'][] = $this->transformSn($snCategoriesProduct);
		}
		
		$this->saveAll($save['CategoriesProduct']);
		
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM product2categories AS SnCategoriesProduct
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
	
	function transformSn($snCategoriesProduct) {
		$category = $this->Category->find('first', array(
			'conditions' => array('Category.sportnutrition_id' => $snCategoriesProduct['SnCategoriesProduct']['category_id']),
			'contain' => array(),
			'fields' => array('Category.id')	
		));
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.sportnutrition_id' => $snCategoriesProduct['SnCategoriesProduct']['product_id']),
			'contain' => array(),
			'fields' => array('Product.id')
		));
		if (!$product['Product']['id']) {
			debug($snCategoriesProduct);
			debug($product); die();
		}
		$categoriesProduct = array(
			'CategoriesProduct' => array(
				'category_id' => $category['Category']['id'],
				'product_id' => $product['Product']['id'],
				'sportnutrition_id' => $snCategoriesProduct['SnCategoriesProduct']['id']
			)
		);

		return $categoriesProduct;
	}
}

?>