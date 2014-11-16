<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
	</head>
<body onload="lazyLoad();">

<div id="body">
	<div id="header">
		<a id="logo" href="/"><img src="/images/redesign_2013/logo_snv.png" width="240px" height="125px" alt="SNV - sportovní výživa pro Vás" /></a>
		<?php
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
?>

<script type="text/javascript">
	function initialize() {
		var point = new google.maps.LatLng(49.580042, 17.289001)
		var mapOptions = {
				zoom: 14,
				center: new google.maps.LatLng(49.580042, 17.289001),
				mapTypeId: google.maps.MapTypeId.ROADMAP
		};
	
		var map = new google.maps.Map(document.getElementById('map'),
		mapOptions);
	
		var marker = new google.maps.Marker({
				position: point,
				map: map,
				title: 'SportNutrition Vávra'
		});
	}
	
	function loadScript() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
				'callback=initialize';
		document.body.appendChild(script);
	}
	
	window.onload = loadScript;

	function lazyLoad(){
		/*
		var scriptTag = document.createElement('script'); 
	    scriptTag.src = "//js/<?php echo REDESIGN_PATH?>jquery.js"; // set the src attribute
	    scriptTag.type = 'text/javascript'; // if you have an HTML5 website you may want to comment this line out
	    scriptTag.async = true; // the HTML5 async attribute
	    var headTag = document.getElementsByTagName('head')[0];
	    headTag.appendChild(scriptTag);
	    */
	    return true;
	}
</script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>jquery.js"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.js"></script>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.easing.js" type="text/javascript"></script>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.slidorion.js" type="text/javascript"></script>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>main.js" type="text/javascript"></script>
<?php 
	if ($this->params['controller'] == 'searches' && $this->params['action'] == 'do_search') {
?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>search_filter.js"></script>
<?php
	}
?>
	<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="/jRating-master/jquery/jRating.jquery.js"></script>
	<script type="text/javascript">
		var ratingStarType = 'small';
		<?php if (isset($this->params['controller']) && isset($this->params['action']) && $this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
			ratingStarType = 'big';
		<?php } ?> 
	</script>
	<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>/product_rating_management.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		var url = document.URL;
		var a = $('<a>', { href:url } )[0];
		if (a.hash == '#nutrishop_redirect') {
			$('#banner').fancybox({
				width: 600,
				height: 470,
				autoSize: false,
			}).trigger('click');
		}
	});
	</script>
</body>
</html>