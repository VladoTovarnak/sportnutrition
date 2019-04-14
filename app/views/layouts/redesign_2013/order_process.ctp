<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
	</head>
<body id="order_process">

<div id="body">
	<div id="header">
		<a id="logo" href="/"><img src="/images/redesign_2013/logo_snv.png" width="240px" height="125px" alt="SNV - sportovní výživa pro Vás" /></a>
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
	<div id="main">
		<?php echo $this->element(REDESIGN_PATH . 'breadcrumbs'); ?>
<?php	if ($session->check('Message.flash')) {
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
</body>
</html>