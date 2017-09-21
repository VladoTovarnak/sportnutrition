<?php
class PostOffice extends AppModel {
	var $name = 'PostOffice';
	
	var $actsAs = array('Containable');
	
	var $xmlFile = 'http://napostu.cpost.cz/vystupy/napostu.xml';
	
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
	
	function get_post_info($postCode){
		$arrContextOptions=array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		
		if ( !isset($postCode) ){
			echo '[{"response": "Empty PSC"}]';
		} else {
			$result = @file_get_contents("https://b2c.cpost.cz/services/PostCode/getDataAsJson?postCode=" . $postCode, false, stream_context_create($arrContextOptions));
			if ( $result === false ){
				echo '[{"response": "Bad PSC"}]';
			} else{
				return $result;
			}
		}
	}
	
	function delivery_address($postCode){
		$postOfficeInfo = $this->find('first', array(
			'conditions' => array(
				'PSC' => $postCode
			)
		));
		
		return 'pošta - ' . $postOfficeInfo['PostOffice']['NAZ_PROV'] . ', ' .  $postOfficeInfo['PostOffice']['ADRESA'];
	}

	function delivery_time($postCode, $delivery_info){
		// stahnu si JSON s datama o moznostech doruceni
		$delivery_data = json_decode($this->get_post_info($postCode), true);
		$delivery_data = $delivery_data[0];
		
		if ( !isset($delivery_data['response']) || $delivery_data['response'] != 'Bad PSC' ){
			$delivery_text = "doručení v běžném režimu";
			if ( $delivery_data['casovaPasma'] == 'ANO' ){
				// defaultne beru delivery_info jako A
				switch ( $delivery_info ){
					case "A":
						$delivery_text = "zvolený čas doručení - dopolední doručení (" . $delivery_data['casDopoledniPochuzky'] . ")";
						break;
						
					case "B":
						$delivery_text = "zvolený čas doručení - odpolední doručení (" . $delivery_data['casOdpoledniPochuzky'] . ")";
						break;
				}
			}
		}
		
		return $delivery_text;
	}
}