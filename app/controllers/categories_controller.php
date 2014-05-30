<?php
class CategoriesController extends AppController {

	var $name = 'Categories';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function admin_index() {
		$categories = $this->Category->find('threaded', array(
			'contain' => array(),
			'order' => array('Category.lft' => 'asc')
		));

		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_view($id = null) {
		$this->Category->id = $id;
		$this->set('category', $this->Category->read()); // nacitani dat o kategorii
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('path_categories', $this->Category->getPath($id, $fields, -1));
		// $path_categories obsahuje cestu do kategorie, kterou prave prohlizim
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('childs', $this->Category->children($id, true, $fields, null, null, 1, -1));
		$this->set('opened_category_id', $id);
	}

	function getCategoriesMenuList($opened_category_id = ROOT_CATEGORY_ID){
		$this->Category->unbindModel( array('hasAndBelongsToMany' => array('Product')), false);
		$this->Category->id = $opened_category_id;
		$fields = array('id'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$path = $this->Category->getPath($this->Category->id, $fields, -1);
		
		$ids_to_find = Set::extract('/Category/id', $path);
		$ids_to_find[] = ROOT_CATEGORY_ID;

		$categories = $this->Category->find('all', array(
			'conditions' => array("parent_id IN ('" . implode("', '", $ids_to_find) . "')"),
			'order' => array("lft" => 'asc')
		));

		// ke kazde kategorii si zjistim kolik ma
		// v sobe produktu
		foreach( $categories as $key => $value ){
			$categories[$key]['Category']['productCount'] = $this->Category->countProducts($categories[$key]['Category']['id']);
			$categories[$key]['Category']['activeProductCount'] = $this->Category->countActiveProducts($categories[$key]['Category']['id']);
		}

		return array(
			'categories' => $categories, 'ids_to_find' => $ids_to_find, 'opened_category_id' => $opened_category_id
		);
	}
	
	function getSubcategoriesMenuList($opened_category_id = null, $order_by_opened = false, $show_all = false){
		return $this->Category->getSubcategoriesMenuList($opened_category_id, false, $order_by_opened, $show_all);
	}

	function admin_add($id = null) {
		if ( !empty($this->data) ){
			$this->Category->create();

			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if ( empty($this->data['Category']['title']) ){
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if ( empty($this->data['Category']['description']) ){
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online obchodu se sportovní výživou a fitness přípravky - www." . CUST_ROOT;
			}
			// pripadne doplnim nadpis a breadcrumb podle nazvu
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['heading'];
			}

			if ( $this->Category->save($this->data['Category']) ){

				// url musim kontrolovat az po ulozeni, protoze
				// neznam ID kategorie, nejdriv ji ulozim a pak checknu
				if ( empty($this->data['Category']['url']) ){
					$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $this->Category->id);
					// zmenim url a znovu ulozim ( UPDATE )
					$this->Category->save($this->data['Category']);
				}

				$this->Session->setFlash('Kategorie byla vložena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Kategorie nebyla vložena, opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
			$this->set('parent_id', $this->data['Category']['parent_id']);
			$this->set('opened_category_id', $this->data['Category']['parent_id']);
		}
		else{
			$this->set('opened_category_id', $id);
			$this->set('parent_id', $id);
			$this->data['Category']['public'] = true;
			$this->data['Category']['active'] = true;
		}
		
		$this->set('tiny_mce_elements', 'CategoryContent');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$category = $this->Category->find('first', array(
			'conditions' => array('Category.id' => $id),
			'contain' => array(),
		));
		
		if (empty($category)) {
			$this->Session->setFlash('Neexistující kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

		$this->set('opened_category_id', $id);
		
		if (isset($this->data)) {
			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if ( empty($this->data['Category']['title']) ){
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if ( empty($this->data['Category']['description']) ){
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online obchodu se sportovní výživou a fitness přípravky - www." . CUST_ROOT;
			}
			if ( empty($this->data['Category']['url']) ){
				$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $id);
			}
			// pripadne doplnim nadpis a breadcrumb
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadrumb'] = $this->data['Category']['name'];
			}

			if ($this->Category->save($this->data)) {
				$this->Session->setFlash('Kategorie byla upravena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Ukládání kategorie se nezdařilo!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $category;
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['name'];
			}
		}
		
		$this->set('tiny_mce_elements', 'CategoryContent');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	// soft delete kategorie
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'));
		}
		
		if (!$this->Category->hasAny(array('Category.id' => $id))) {
			$this->Session->setFlash('Neexistující kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'));
		}
		
		$category = array(
			'Category' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->Category->save($category)) {
			$this->Session->setFlash('Kategorie byla deaktivována.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Kategorii se nepodařilo deaktivovat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'categories', 'action' => 'index'));
	}

	// natvrdo smaze kategorii ze systemu
	function admin_delete_from_db($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}

		// zjistim si pocet deti a jestli jsou v kategorii nejake produkty
		$children = $this->Category->childcount($id);
		$productCount = $this->Category->countAllProducts($id);

		if ( $children != 0 ){
			// jestlize obsahuje podkategorie, nedovolim mazat a vypisu hlasku
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje jiné podkategorie!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		} elseif ( $productCount != 0 ){
			// obsahuje produkty, nedovolim mazat
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje produkty!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}

		$this->Category->id = $id;
		$category = $this->Category->read('parent_id');
		if ($this->Category->delete($id)) {
			$this->Session->setFlash('Kategorie byla vymazána.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Kategorii se nepodařilo vymazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}

	function getCategoriesList($active_id = ROOT_CATEGORY_ID){
		$this->set('list', $this->find('all'));
	}

	function admin_list_products($category_id) {
		$this->paginate['CategoriesProduct'] = array(
			'contain' => 'Product',
			'order' => array('Product.active' => 'desc')
		);
		$data = $this->paginate('CategoriesProduct', array('category_id' => $category_id) );
		$this->set(
			'products', $data
		);
		$this->set('opened_category_id', $category_id);
	}

	function admin_moveup($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od id korenove kategorie
		if ( isset($id) && $id != ROOT_CATEGORY_ID ){
			if ( $this->Category->moveup($id) ){
				$this->Session->setFlash('Kategorie byla posunuta nahoru.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejvyssi možné pozici.', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladni kategorii..', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
	}

	function admin_movedown($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od id korenove kategorie
		if ( isset($id) && $id != ROOT_CATEGORY_ID ){
			if ( $this->Category->movedown($id) ){
				$this->Session->setFlash('Kategorie byla posunuta dolů.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejnižší možné pozici.', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladni kategorii..', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
	}

	function admin_movenode($id){
		if ( !isset($this->data) ){ // formular jeste nebyl odeslan
			// nactu si data o kategorii, s kterou chci pracovat
			$this->Category->recursive = -1;
			$this->data = $this->Category->read(null, $id);

			// nahraju si strukturovany seznam kategorii,
			// vynecham hlavni kategorii a kategorii, kterou chci presunout
			$categories = $this->Category->generatetreelist(array('not' => array('id' => array('5', $id))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			$this->Category->id = $id;
			$data = array(
				'parent_id' => $this->data['Category']['target_id']
			);
			$this->Category->save($data, false, array('parent_id'));

			$this->Session->setFlash('Kategorie byla přesunuta do nového uzlu.');
			$this->redirect(array('action' => 'view', $id), null, true);
		}
	}

	/**
	 * Natahne data ze struktury tabulek sportnutrition
	 */
	function import() {
		$this->Category->import();
		die('here');
	}
	
	function update() {
		$this->Category->update();
		die('here');
	}
	
} // konec definice tridy
?>