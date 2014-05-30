<?php
class ContentsController extends AppController{
	var $name = 'Contents';

	var $helpers = array('Html', 'Javascript', 'Form');
	
	var $scaffold = 'admin';
	
	function admin_index() {
		$contents = $this->Content->find('all', array(
			'contain' => array()
		));
		
		$this->set('contents', $contents);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add(){
		if (isset($this->data)){
			if ( $this->Content->save($this->data) ){
				$this->Session->setFlash('Stránka byla uložena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Stránku se nepodařilo uložit! Opravte chyby ve formuláři a uložte ji znovu.', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$this->set('tiny_mce_elements', 'ContentContent');
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null){
		if (!$id) {
			$this->Session->setFlash('Neznámá webstránka.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$content = $this->Content->find('first', array(
			'conditions' => array('Content.id' => $id),
			'contain' => array(),
		));
		
		if (empty($content)) {
			$this->Session->setFlash('Neexistující webstránka.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (isset($this->data)){
			if ($this->Content->save($this->data)) {
				$this->Session->setFlash('Stránka byla uložena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Stránku se nepodařilo uložit!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $content;
		}
	
		$this->set('tiny_mce_elements', 'ContentContent');
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá webstránka.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->Content->delete($id)) {
			$this->Session->setFlash('Webstránka byla odstraněna.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Webstránku se nepodařilo odstranit.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}

	function view() {
		if ( ereg('cPath=', $_SERVER['REQUEST_URI']) ){
			$this->requestAction('/categories/redirect_to_new');
			die('end');
		}
		
		if ( ereg('manufacturers_id=', $_SERVER['REQUEST_URI']) ){
			$this->requestAction('/manufacturers/redirect_to_new');
			die('end');
		}
		
		// navolim si layout stranky
		$this->layout = REDESIGN_PATH . 'content';

		$path = func_get_args();

		$page = $this->Content->find(array('path' => $path[0]));
		if ( $this->Content->getNumRows() > 0 ){
			$this->set('page_heading', $page['Content']['heading']);
			$this->set('page_content', $page['Content']['content']);
			$this->set('title_for_content', $page['Content']['title']);
			$this->set('description_for_content', $page['Content']['description']);
		} else {
			die('404 nenalezeno');
		}
		
		$breadcrumbs = array(array('anchor' => $page['Content']['title'], 'href' => $page['Content']['path']));
		$this->set('breadcrumbs', $breadcrumbs);
	}

}
?>