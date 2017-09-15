<?php
class PostOfficesController extends AppController {
	var $name = 'PostOffices';
	
	function load() {
		$xml = simplexml_load_file($this->PostOffice->xmlFile);
		if ($xml != FALSE) {
			$counter = 0;
			foreach ($xml->children() AS $child) {
				$name = $child->getName();
				// v XMLku si prolezeme jen atributy ROW
				if ($name == 'row') {
					$save = array(); // inicializace stacku pro ulozeni
					foreach ($child as $a => $b) {
						// prolezu vsechny atributy
						if ($a != 'OTV_DOBA') { // pokud se nejedna o otviraci dobu
							$save[$a] = $this->PostOffice->setBool($b);
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
					$db_post_office = $this->PostOffice->find('first', array(
						'conditions' => array('PostOffice.PSC' => $save['PSC']),
						'contain' => array(),
						'fields' => array('PostOffice.id')
					));
					
					// pokud jsem zaznam nasel, nastavim jeho ID - tim zaznam updatujeme
					if (!empty($db_post_office)) {
						$save['id'] = $db_post_office['PostOffice']['id'];
					}
					$save['PostOffice'] = $save;
					
					$this->PostOffice->create();
					
					if (!$this->PostOffice->save($save)) {
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
			$result['message'] = 'Neznám všechny atributy pro vyhledání pošty';
		} else {
			$zip = $_POST['zip'];
			$city = $_POST['city'];
			$type = $_POST['type'];

			$conditions = array();
			if (!empty($zip)) {
				$conditions[] = 'PostOffice.PSC LIKE "%%' . $zip . '%%"';
			}
			if (!empty($city)) {
				$conditions[] = 'PostOffice.NAZ_PROV LIKE "%%' . $city . '%%"';
			}
			if ($type == 'balikomat') {
				$conditions[] = 'PostOffice.NAZ_PROV LIKE "%%Balíkomat%%"';
			}
			
			// pokud nemam zadano mesto ani psc, koncim s chybou
			if (empty($conditions)) {
				$result['message'] = 'Zadejte PSČ nebo město';
			} else {
				// vytahnu si pobocky podle podminek
				$post_offices = $this->PostOffice->find('all', array(
					'conditions' => $conditions,
					'contain' => array(),
				));
				
				$result['data'] = $post_offices;
				$result['success'] = true;
			}
		}
		
		echo json_encode($result);
		die();
	}
	
	function delivery_search($postCode = null){
		if ( !isset($postCode) ){
			echo '[{"response": "Empty PSC"}]';
		} else {
			$result = @file_get_contents("https://b2c.cpost.cz/services/PostCode/getDataAsJson?postCode=" . $postCode);
			if ( $result === false ){
				echo '[{"response": "Bad PSC"}]';
			} else{
				echo $result;
			}
		}
		die();
	}
	
}