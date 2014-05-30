<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
		
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
	        map = new google.maps.Map(document.getElementById('map'), mapOptions);
	
			var marker = new google.maps.Marker({
				position: point,
				map: map,
				title: 'SportNutrition Vávra'
	        });
		}
	
	    google.maps.event.addDomListener(window, 'load', initialize);
	    </script>
	</head>
<body>

<div id="body">
	<div id="header">
		<h1><a href="/">SNV - Sport Nutrition Vávra<span></span></a></h1>
		<?php
			echo $this->element(REDESIGN_PATH . 'login_box');
			echo $this->element(REDESIGN_PATH . 'horizontal_menu');
		?>
	</div>
	<?php echo $this->element(REDESIGN_PATH . 'sidebox')?>
	<?php echo $this->element(REDESIGN_PATH . 'search_box')?>

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

</body>
</html>