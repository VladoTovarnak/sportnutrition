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
}