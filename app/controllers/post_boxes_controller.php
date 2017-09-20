<?php
class PostBoxesController extends AppController {
	var $name = 'PostBoxes';
	
	function load() {
		$xml = simplexml_load_file($this->PostBox->xmlFile);
		if ($xml != FALSE) {
			$counter = 0;
			foreach ($xml->children() AS $child) {
				$name = $child->getName();
				// v XMLku si prolezeme jen atributy ROW
				if ($name == 'row') {
					$save = array(); // inicializace stacku pro ulozeni
					foreach ($child as $a => $b) {
						// prolezu vsechny atributy
						if ($a != 'OTEV_DOBY') { // pokud se nejedna o otviraci dobu
							$save[$a] = $this->PostBox->setBool($b);
						} else { // jedna se o otviraci dobu, musim prolezt vsechno i "prestavky" v otviraci dobe
							foreach ($b->children() AS $x) {
								$day = $x->attributes()->name;
								$den = mb_strtolower($day, 'UTF-8');
								
								$den = str_replace(array('ě', 'í', 'č', 'ř', 'ú', 'ý', 'á'), array('e', 'i', 'c', 'r', 'u', 'y', 'a'), $den);
								// od_do
								$count = 1;
								foreach ($x->children() as $doba) {
									// <od> a <do>
									$start = (string) $doba->od;
									$end = (string) $doba->do;
									
									$save[$den . "_od" . $count] = $start;
									$save[$den . "_do" . $count] = $end;
									
									$count++;
								}
							}
						}
					}
					// zkusim najit, jestli uz zaznam s postou v databazi existuje
					$db_post_box = $this->PostBox->find('first', array(
						'conditions' => array('PostBox.PSC' => $save['PSC']),
						'contain' => array(),
						'fields' => array('PostBox.id')
					));
					
					// pokud jsem zaznam nasel, nastavim jeho ID - tim zaznam updatujeme
					if (!empty($db_post_box)) {
						$save['id'] = $db_post_box['PostBox']['id'];
					}
					$save['PostBox'] = $save;
					
					$this->PostBox->create();
					
					if (!$this->PostBox->save($save)) {
						debug($save);
						trigger_error('Nepodarilo se ulozit postu', E_USER_NOTICE);
					}
				}
				$counter = $counter + 1;
			}
			echo "Zpracoval jsem XML a jeho " . $counter . " radku.";
		}
		else {
			echo "DB se nepodařilo naimportovat! Chybí vstupní XML formát.";
		}
		die();
	}
	
	function ajax_search() {
		$result = array(
			'success' => false,
			'message' => '',
			'data' => array()
		);
		
		if (!isset($_POST['zip']) || !isset($_POST['city']) || !isset($_POST['type'])) {
			$result['message'] = 'Neznám všechny atributy pro vyhledání Balíkomatu.';
		} else {
			$zip = isset($_POST['zip']) ? $_POST['zip']: '';
			$city = isset($_POST['city']) ? $_POST['city'] : '';
			$type = isset($_POST['type']) ? $_POST['type'] : '';

			$conditions = array();
			if (!empty($zip)) {
				$conditions[] = 'PostBox.PSC LIKE "%%' . $zip . '%%"';
			}
			if (!empty($city)) {
				$conditions[] = 'PostBox.OBEC LIKE "%%' . $city . '%%" OR PostBox.C_OBCE LIKE "%%' . $city . '%%"  ';
			}
			
			// pokud nemam zadano mesto ani psc, koncim s chybou
			if (empty($conditions)) {
				$result['message'] = 'Zadejte PSČ nebo město';
			} else {
				// vytahnu si pobocky podle podminek
				$post_boxes = $this->PostBox->find('all', array(
					'conditions' => array(
						'OR' => $conditions
					),
					'contain' => array(),
				));
				
				$result['data'] = $post_boxes;
				$result['success'] = true;
			}
		}
		
		echo json_encode($result);
		die();
	}
	
	function test(){
		die($this->PostBox->delivery_address("10003"));
		
	}
	
}