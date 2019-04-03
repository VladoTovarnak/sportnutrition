<?php
	if (!isset($_title) || $_title == '') {
		$_title = 'Sportovní výživa, doplňky stravy, kloubní výživa, vitamíny';
	}
	if (!isset($_description) || $_description == '') {
		$_description = 'Sportovní výživa od Sport Nutrition. Proteinové a sacharidové přípravky (proteiny, sacharidy, gainery), vitamíny a aminokyseliny.';
	}
?>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?><?php echo ($_SERVER['REMOTE_ADDR'] == IMAGE_IP ? 'style000-new.css' : 'style000.min.css?v=1')?>" type="text/css" media="screen" />
		<title><?php echo $_title?> : Sportnutrition</title>
		<meta name="description" content="<?php echo $_description?>" />

<?php
    // Add mobile view properties
    if ($this->layout == "redesign_2013/product" || $this->layout == "redesign_2013/category" || $this->layout == "redesign_2013/content" || $this->layout == "redesign_2013/homepage") {
        // Add viewport
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

    }
	if (isset($_keywords) && !empty($_keywords) && is_string($_keywords)) {
?>
		<meta name="keywords" content="<?php echo $_keywords?>" />
<?php
	}
?>
<?php
if ( !defined('ISDEVELOPER') ){
?>

		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		
		<?php 
				if ( $this->Session->check('Customer') ){
		?>
				fbq('init', '792712067534673', {
					em: '{{<?php echo $this->Session->read('Customer.email');?>}}',
					ph: '{{<?php echo $this->Session->read('Customer.phone');?>}}',
					fn: '{{<?php echo $this->Session->read('Customer.first_name');?>}}',
				});
		<?php
				} else{
		?>
					fbq('init', '792712067534673');			
		<?php
				}
		?>
		fbq('track', "PageView");</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=792712067534673&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->
    <?php
}
?>