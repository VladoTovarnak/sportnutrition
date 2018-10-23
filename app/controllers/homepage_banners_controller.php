<?php
class HomepageBannersController extends AppController {
	var $name = 'HomepageBanners';
	
	function admin_index() {
		$banners = $this->HomepageBanner->find('all', array(
			'conditions' => array(),
			'contain' => array(),
			'fields' => array('*')
		));
		
		if (isset($this->data)) {
			switch($this->data['HomepageBanner']['action']) {
				case 'change_image':
					// zmenit obrazek u daneho banneru
					// pokud nahraju obrazek na disk
					if (is_uploaded_file($this->data['HomepageBanner']['image']['tmp_name'])) {
						// obrazek musi mit rozmery 668x358
						$imagesize = getimagesize($this->data['HomepageBanner']['image']['tmp_name']);
						$image_width = $imagesize[0];
						$image_height = $imagesize[1];
						if ($image_width != 668 || $image_height != 358) {
							$this->Session->setFlash('Obrázek se nepodařilo změnit, musí mít rozměry 668 x 358 px!', REDESIGN_PATH . 'flash_failure');
							$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
						}
						$this->data['HomepageBanner']['image']['name'] = strip_diacritic($this->data['HomepageBanner']['image']['name'], false);
						App::import('Model', 'Image');
						$this->HomepageBanner->Image = &new Image;
						// zkontroluju, jestli nemam obrazek s danym jmenem a pripadne ocisluju
						$this->data['HomepageBanner']['image']['name'] = $this->HomepageBanner->Image->checkName($this->HomepageBanner->folder . '/' . $this->data['HomepageBanner']['image']['name']);
						// zlopiruju obrazek z tmp do pozadovaneho souboru
						if (move_uploaded_file($this->data['HomepageBanner']['image']['tmp_name'], $this->data['HomepageBanner']['image']['name'])) {
							// potrebuju zmenit prava u obrazku
							chmod($this->data['HomepageBanner']['image']['name'], 0644);
							// upravim udaj db
							// odstranim info o adresari, ve kterem je obrazek ulozeny
							$this->data['HomepageBanner']['image']['name'] = explode("/", $this->data['HomepageBanner']['image']['name']);
							$this->data['HomepageBanner']['image']['name'] = $this->data['HomepageBanner']['image']['name'][count($this->data['HomepageBanner']['image']['name']) -1];
							
							$banner_save = array(
								'HomepageBanner' => array(
									'id' => $this->data['HomepageBanner']['id'],
									'image' => $this->data['HomepageBanner']['image']['name']
								)
							);
							
							$old_banner = $this->HomepageBanner->find('first', array(
								'conditions' => array('HomepageBanner.id' => $this->data['HomepageBanner']['id']),
								'contain' => array(),
								'fields' => array('HomepageBanner.image')
							));
							
							if (!($this->HomepageBanner->save($banner_save))) {
								$this->Session->setFlash('Obrázek se nepodařilo nahrát do systému, opakujte prosím akci!', REDESIGN_PATH . 'flash_failure');
								$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
							}
							
							if (!empty($old_banner) && isset($old_banner['HomepageBanner']['image'])) {
								$old_banner_image = $this->HomepageBanner->folder . '/' . $old_banner['HomepageBanner']['image'];
								if (file_exists($old_banner_image)) {
									unlink($old_banner_image);
								}
							}
							$this->Session->setFlash('Obrázek byl upraven', REDESIGN_PATH . 'flash_success');
							$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
							// smazu z disku puvodni obrazek
						} else {
							$this->Session->setFlash('Nepodařilo se nahrát obrázek, opakujte prosím akci.', REDESIGN_PATH . 'flash_failure');
							$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
						}
					} else {
						$this->Session->setFlash('Nepodařilo se nahrát obrázek, opakujte prosím akci.', REDESIGN_PATH . 'flash_failure');
						$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
					}
					break;
				case 'change_description':
					if ($this->HomepageBanner->save($this->data)) {
						$this->Session->setFlash('Popis byl upraven', REDESIGN_PATH . 'flash_success');
					} else {
						$this->Session->setFlash('Popis se nepodařilo upravit, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
					}
					$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
					break;
				case 'change_url':
					if (!preg_match('/^https:\/\/www\.sportnutrition\.cz/', $this->data['HomepageBanner']['url'])) {
						$this->data['HomepageBanner']['url'] = 'https://www.sportnutrition.cz' . $this->data['HomepageBanner']['url'];
					}
					if ($this->HomepageBanner->save($this->data)) {
						$this->Session->setFlash('URL byla upravena', REDESIGN_PATH . 'flash_success');
					} else {
						$this->Session->setFlash('URL se nepodařilo upravit, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
					}
					$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
					break;
			}
		}
		
		$this->set('banners', $banners);
		$this->set('folder', $this->HomepageBanner->folder);
		
		$this->set('tiny_mce_elements', 'HomepageBanner0Description,HomepageBanner1Description,HomepageBanner2Description,HomepageBanner3Description');
		$this->set('tiny_mce_easy', true);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	/**
	 * Posun banneru o jedno nahoru
	 * @param string $id
	 */
	function admin_move_up($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID banneru.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
		}
		if ($this->HomepageBanner->moveUp($id)) {
			$this->Session->setFlash('Banner byl posunut nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Banner se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
	}
	

	/**
	 * Posun banneru o jedno dolu
	 * @param string $id
	 */
	function admin_move_down($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID banneru.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
		}
		if ($this->HomepageBanner->moveDown($id)) {
			$this->Session->setFlash('Banner byl posunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Banner se nepodařilo přesunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
	}
}