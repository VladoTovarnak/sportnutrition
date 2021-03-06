<!DOCTYPE html>
<html>
	<head lang="cs">
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
	</head>
<body>	

<div id="body">
	<div id="header">
		<a id="logo" href="/"><img src="/images/redesign_2013/logo_snv.png" width="240" height="125" alt="SNV - sportovní výživa pro Vás" /></a>
		<?php
		    echo $this->element(REDESIGN_PATH . 'mobile_menu');
		    echo $this->element(REDESIGN_PATH . 'login_box');
			echo $this->element(REDESIGN_PATH . 'horizontal_menu');
		?>
	</div>
	<?php
		echo $this->element(REDESIGN_PATH . 'sidebox');
		echo $this->element(REDESIGN_PATH . 'submenu');
		echo $this->element(REDESIGN_PATH . 'search_box');
	?>
	<hr class="cleaner" />
	<div id="sidebar"><?php
		echo $this->element(REDESIGN_PATH . 'categories_menu');
		echo $this->element(REDESIGN_PATH . 'manufacturer_select');
		echo $this->element(REDESIGN_PATH . 'awards');
		echo $this->element(REDESIGN_PATH . 'facebook');
	?></div>

	<div id="main">
		<?php echo $this->element(REDESIGN_PATH . 'breadcrumbs'); ?>
		<?php 
			if ($session->check('Message.flash')){
				echo $session->flash();
			}
			echo $content_for_layout;
		?>
		<hr class="cleaner" />
	</div>
	<hr class="cleaner" />
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
</div>
<?php
	echo $this->element(REDESIGN_PATH . 'heureka_overeno');
	echo $this->element(REDESIGN_PATH . 'facebook_prava');
	echo $this->element(REDESIGN_PATH . 'default_foot2');
?>
<!--<script type="text/javascript">
	function initialize() {
		var point = new google.maps.LatLng(49.569130, 17.302750);
		var mapOptions = {
				zoom: 15,
				center: new google.maps.LatLng(49.569130, 17.302750),
				mapTypeId: google.maps.MapTypeId.ROADMAP
		};
	
		var map = new google.maps.Map(document.getElementById('map'),
		mapOptions);

		// Define content of infowindow
        var contentInfoWindow =
            '<strong>SNV Vávra s.r.o.</strong><br/>' +
            'Týnecká 826/55, Holice,<br/>' +
            '779 00 Olomouc<br/>' +
            '(Budova Husqvarny 1. patro, vchod zezadu)';

        // Define infowindow and assign the content
        var infowindow = new google.maps.InfoWindow({
            content: contentInfoWindow
        });

        // Define marker on the map
		var marker = new google.maps.Marker({
				position: point,
				map: map,
				title: 'SNV Vávra s.r.o.'
		});

		// toggle infowindow by clicking on marker
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
        });

        // open infowindow by default
        infowindow.open(map,marker);
	}
	
	function loadScript() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBWrprssVtJkxVzAoaJJZgMRJmWjOJSlGc&' +
				'callback=initialize';
		document.body.appendChild(script);
	}
	
	window.onload = loadScript;
</script>
<script type="text/javascript">
	function fireAddToCart(id, name, cname, price){
		fbq('track', 'AddToCart', { 
		    content_type: 'product',
		    content_ids: '["CZ_' + id + '"]',
		    content_name: "'" + name + "'",
		    content_category: "'" + cname + "'",
		    value: price,
		    currency: 'CZK'
		});
	}
</script>-->
</body>
</html>