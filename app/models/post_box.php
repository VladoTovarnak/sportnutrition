<?php
class PostBox extends AppModel {
	var $name = 'PostBox';
	
	var $actsAs = array('Containable');
	
	var $xmlFile = 'http://napostu.ceskaposta.cz/vystupy/balikovny.xml';
	
	/**
	 * Nastavení bool hodnot pokud je třeba
	 * @param <type> $value
	 * @return <type>
	 */
	function setBool($value) {
		if ($value == 'A')
			return 1;
		else if ($value == 'N')
			return 0;
		else
			return mysql_real_escape_string($value);
	}
	
	function delivery_address($psc){
		$postBoxInfo = $this->find('first', array(
			'conditions' => array(
				'PSC' => $psc
			)
		));
		$log = $this->getDataSource()->getLog(false, false);
		
		return $postBoxInfo['PostBox']['NAZEV'] . ', ' .  $postBoxInfo['PostBox']['ADRESA'];
	}
	
}