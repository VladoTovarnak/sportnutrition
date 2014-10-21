<?php 
class Tool extends AppModel {
	var $name = 'Tool';
	
	var $useTable = false;
	
	function redirect_old_sn() {
		$url = $_SERVER['REQUEST_URI'];

		$redirect_url = null;
		$type = null;
		if (preg_match('/^\/(.+)\//U', $url, $matches)) {
			$type = $matches[1];
		}

		switch ($type) {
			case 'product':
			case 'produkty-id':
				// presmeruju produkt
				App::import('Model', 'Product');
				$this->Product = &new Product;
				
				$redirect_url = $this->Product->redirect_url($url);
				break;
			case 'category':
			case 'rubriky':
				// presmeruju kategorii
				App::import('Model', 'Category');
				$this->Category = &new Category;
				
				$redirect_url = $this->Category->redirect_url($url);
				break;
			case 'website':
				// presmeruju obsah
				App::import('Model', 'Content');
				$this->Content = &new Content;
				
				$redirect_url = $this->Content->redirect_url($url);
				break;
			case 'manufacturer':
				// presmeruju vyrobce
				App::import('Model', 'Manufacturer');
				$this->Manufacturer = &new Manufacturer;
				
				$redirect_url = $this->Manufacturer->redirect_url($url);
				break;
		}

		return $redirect_url;
	}
}
?>