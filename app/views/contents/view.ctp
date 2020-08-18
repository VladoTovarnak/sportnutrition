<div id="editorContent">
<?php
	if ( preg_match('/##LINKER##/', $page_content) ){
		require_once('__linker/uploader.php');
		$linker_output = upload_links('nutrishop_linkovaci_stranka', base64_encode($_SERVER['REQUEST_URI']));
		$page_content = str_replace('##LINKER##', $linker_output, $page_content);
	}

	/*
	// na zvolene stranky pridam mapu
	$map_pages = array('osobni-odber.htm', 'firma.htm');
	if (in_array($this->params['url']['url'], $map_pages)) {
		$page_content .= '<div id="map" style="width:585px;height:300px"></div>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBWrprssVtJkxVzAoaJJZgMRJmWjOJSlGc"></script>
	    <script type="text/javascript">
		var map;
	    function initialize() {
			var point = new google.maps.LatLng(49.569130, 17.302750);
            var mapOptions = {
                    zoom: 15,
                    center: new google.maps.LatLng(49.569130, 17.302750),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
            };
        
            var map = new google.maps.Map(document.getElementById(\'map\'),
            mapOptions);
    
            // Define content of infowindow
            var contentInfoWindow =
                \'<strong>SNV Vávra s.r.o.</strong><br/>\' +
                \'Týnecká 826/55, Holice,<br/>\' +
                \'779 00 Olomouc<br/>\' +
                \'(Budova Husqvarny 1. patro, vchod zezadu)\';
    
            // Define infowindow and assign the content
            var infowindow = new google.maps.InfoWindow({
                content: contentInfoWindow
            });
    
            // Define marker on the map
            var marker = new google.maps.Marker({
                    position: point,
                    map: map,
                    title: \'SNV Vávra s.r.o.\'
            });
    
            // toggle infowindow by clicking on marker
            google.maps.event.addListener(marker, \'click\', function() {
                infowindow.open(map,marker);
            });
    
            // open infowindow by default
            infowindow.open(map,marker);
		}
	
	    google.maps.event.addDomListener(window, \'load\', initialize);
	    </script>';
	}
*/
	echo $page_content;
?>
</div>