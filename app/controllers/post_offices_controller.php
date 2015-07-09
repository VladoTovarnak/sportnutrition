<?php
class PostOfficesController extends AppController {
	var $name = 'PostOffices';
	
	function load() {
		$xml = simplexml_load_file($this->PostOffice->xmlFile);
		if ($xml != FALSE) {
			foreach ($xml->children() AS $child) {
				$name = $child->getName();
				if ($name == 'row') {
					$save = array();
					foreach ($child as $a => $b) {
						if ($a == 'OTV_DOBA') {
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
						} else {
							$save[$a] = $this->PostOffice->setBool($b);
						}
					}
					$db_post_office = $this->PostOffice->find('first', array(
						'conditions' => array('PostOffice.PSC' => $save['PSC']),
						'contain' => array(),
						'fields' => array('PostOffice.id')
					));
					
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
			}
		}
		else {
			echo "DB se nepodařilo naimportovat! Chybí vstupní XML formát.";
		}
		die();
	}
}