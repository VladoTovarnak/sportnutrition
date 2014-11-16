<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
	</head>
<body>

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
		<div class="left">
			<?php echo $this->element(REDESIGN_PATH . 'breadcrumbs'); ?>
			<?php 
				if ($session->check('Message.flash')){
					echo $session->flash();
				}
				echo $content_for_layout;
			?>
		</div>
		<div class="right filter">
			<?php echo $this->element(REDESIGN_PATH . '/action_products')?>
		</div>
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
$(document).ready(function() {
	$('#ManufacturerSelect').change(function() {
		 $("#ManufacturerSelect option:selected").each(function() {
			 manufacturerId = $(this).attr('value');
			 if (manufacturerId) {
				// natahnu vyrobce a presmeruju
				$.ajax({
					type: 'POST',
					url: '/manufacturers/ajax_get_url',
					dataType: 'json',
					data: {
						id: manufacturerId
					},
					success: function(data) {
						if (data.success) {
							window.location.href = data.message;
						}
					}
				});
			 }
		});
	});
});
</script>
</body>
</html>