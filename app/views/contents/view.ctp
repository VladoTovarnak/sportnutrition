<div id="editorContent">
<?
	if ( preg_match('/##LINKER##/', $page_content) ){
		require_once('__linker/uploader.php');
		$linker_output = upload_links('nutrishop_linkovaci_stranka', base64_encode($_SERVER['REQUEST_URI']));
		$page_content = str_replace('##LINKER##', $linker_output, $page_content);
	}
	
	// na zvolene stranky pridam mapu
	$map_pages = array('osobni-odber.htm', 'firma.htm');
	if (in_array($this->params['url']['url'], $map_pages)) {
		$page_content .= '<div id="map" style="width:585px;height:300px"></div>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBWrprssVtJkxVzAoaJJZgMRJmWjOJSlGc&amp;sensor=false"></script>
	    <script type="text/javascript">
		var map;
	    function initialize() {
			var point = new google.maps.LatLng(49.580042, 17.289001)
	          
	        var mapOptions = {
				zoom: 14,
				center: point,
				mapTypeId: google.maps.MapTypeId.ROADMAP
	        };
	        map = new google.maps.Map(document.getElementById(\'map\'), mapOptions);
	
			var marker = new google.maps.Marker({
				position: point,
				map: map,
				title: \'SportNutrition Vávra\'
	        });
		}
	
	    google.maps.event.addDomListener(window, \'load\', initialize);
	    </script>';
	}

	echo $page_content;
?>
</div>