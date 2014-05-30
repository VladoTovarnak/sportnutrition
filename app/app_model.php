<?php mb_internal_encoding('UTF-8')?>
<?php
class AppModel extends Model {
	var $curl = null;
	
	function sn_connect($url = 'http://www.sportnutrition.cz/admin/objednavky:17/vyrizena/') {
		$username = SN_USERNAME;
		$password = SN_PASSWORD;
		
		$login = $username . ':' . $password;
		
		// prihlasuju se
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.100 Safari/534.30' );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 0 );
		curl_setopt( $curl, CURLOPT_USERPWD, $login);
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
		curl_setopt( $curl, CURLOPT_COOKIEFILE, 'cookies.txt' );
		curl_setopt( $curl, CURLOPT_COOKIEJAR,  'cookies.txt' );
		
		$this->curl = $curl;
		
		return true;
	}
	
	function sn_download($url) {
		if (!$this->curl) {
			return false;
		}
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_URL, $url);
		
		return curl_exec($this->curl);
	}
	
	function sn_disconnect() {
		curl_close($this->curl);
		$this->curl = null;
	}
	
	function sn_save_page($url = null, $file_name = null) {
		if ($url && $file_name) {
			if ($result = $this->sn_download($url)) {
				return file_put_contents($file_name, $result);
			}
		}
		return false;
	}
	
	function update_setting($name, $value) {
		return $this->query('
			UPDATE parser_settings
			SET value="' . $value . '"
			WHERE name="' . $name . '"
		');
	}
	
	function truncate() {
		if ($this->useTable) {
			return $this->query('TRUNCATE TABLE ' . $this->useTable);
		}
		return false;
	}

}