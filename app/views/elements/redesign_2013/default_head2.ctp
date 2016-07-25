<?php
	if (!isset($_title) || $_title == '') {
		$_title = 'Sportovní výživa, doplňky stravy, kloubní výživa, vitamíny';
	}
	if (!isset($_description) || $_description == '') {
		$_description = 'Sportovní výživa od Sport Nutrition. Proteinové a sacharidové přípravky (proteiny, sacharidy, gainery), vitamíny a aminokyseliny.';
	}
?>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?><?php echo ($_SERVER['REMOTE_ADDR'] == IMAGE_IP ? 'style000-new.css' : 'style000.css?v=14')?>" type="text/css" media="screen" />
		<title><?php echo $_title?> : Sportnutrition</title>
		<meta name="description" content="<?php echo $_description?>" />
<?php
	if (isset($_keywords) && !empty($_keywords) && is_string($_keywords)) {
?>
		<meta name="keywords" content="<?php echo $_keywords?>" />
<?php
	}
	
		$sess = $this->Session->read('Customer');
		echo '<!--';
		print_r ($sess);
		echo '-->';
?>

		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		
		fbq('init', '792712067534673');
		fbq('track', "PageView");</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=792712067534673&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->