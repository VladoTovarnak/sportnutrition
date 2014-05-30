<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>comment_form_management.js"></script>
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
		<?php echo $this->element(REDESIGN_PATH . 'category_banner')?>
		<div class="left">
			<?php 
				if ($session->check('Message.flash')){
					echo $session->flash();
				}
				echo $content_for_layout;
			?>
		</div>
		<div class="right">
			<?php 
				if (isset($similar_products) && !empty($similar_products)) { ?>
				<div>
				<h2><span>Lidé co koupili tento <br />produkt koupili také:</span></h2>
			<?php	foreach ($similar_products as $similar_product) {?>
				<div class="product card small">
					<h3><a href="/<?php echo $similar_product['Product']['url']?>"><?php echo $similar_product['Product']['name']?></a></h3>
					<a href="#"><img src="/product-images/small/<?php echo $similar_product['Image']['name']?>" alt="Obrázek <?php echo $similar_product['Product']['name']?>" /></a>
					<div class="rating"></div>
					<b class="price"><?php echo $similar_product[0]['price']?> Kč</b>
				</div>
			<?php	} ?>
				<div style="clear:both"></div>
				</div>
				
			<?php } ?>
		</div>
		<hr class="cleaner" />
	</div>
	<hr class="cleaner" />
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
</div>

</body>
</html>
<?php echo $this->element('sql_dump')?>