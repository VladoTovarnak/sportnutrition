<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>products_pagination.js"></script>
		<script type="text/javascript" src="/loadmask/jquery.loadmask.min.js"></script>
		<link href="/loadmask/jquery.loadmask.css" rel="stylesheet" type="text/css" />
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
		<div class="right filter">
			<?php if (
					(isset($filter_manufacturers) && !empty($filter_manufacturers)) ||
					(isset($filter_attributes) && !empty($filter_attributes))
				) { ?>
			<h2><span>Filtrovat podle:</span></h2>
			<?php if (isset($filter_manufacturers) && !empty($filter_manufacturers)) { ?>
			<h3>Podle značky</h3>
			<ul>
				<?php foreach ($filter_manufacturers as $filter_manufacturer) { ?>
				<li><label><input type="checkbox" class="filter_manufacturer" rel="<?php echo $filter_manufacturer['Manufacturer']['id']?>" id="Manufacturer<?php echo $filter_manufacturer['Manufacturer']['id']?>"/><?php echo $filter_manufacturer['Manufacturer']['name']?>  (<?php echo $filter_manufacturer[0]['Manufacturer__products_count']?>)</label></li>
				<?php } ?>
			</ul>
			<?php } ?>
<!-- DODELAT ROZDELENI PRODUKTU PODLE FORMY
			<h3>Podle formy</h3>
			<ul>
				<li><a href="#">Aenean commod  (28)</a></li>
				<li><a href="#">SULDERES  (5)</a></li>
				<li><a href="#">JUMBOO  (45)</a></li>
				<li><a href="#">HYPER SVAL  (12)</a></li>
				<li><a href="#">UDER  (15)</a></li>
				<li><a href="#">DETHORT  (18)</a></li>
				<li><a href="#">JUBENDERRSS  (2)</a></li>
			</ul>
-->
			<?php if (isset($filter_attributes) && !empty($filter_attributes)) { ?>
			<h3>Podle příchuti</h3>
			<ul>
				<?php foreach ($filter_attributes as $filter_attribute) { ?>
				<li><label><input type="checkbox" class="filter_attribute" rel="<?php echo $filter_attribute['Attribute']['id']?>" id="Attribute<?php echo $filter_attribute['Attribute']['id']?>"/><?php echo $filter_attribute['Attribute']['value']?> (<?php echo $filter_attribute[0]['Attribute__products_count']?>)</label></li>
				<?php } ?>
			</ul>
			<?php }
			}
			if (isset($products_stack) && !empty($products_stack)) { ?>
			<h2><span>Naposledy zobrazené</span></h2>
			<ul>
			<?php foreach ($products_stack as $product) { ?>
				<li><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></li>
			<?php } ?>
			</ul>
			<?php } ?>
			<?php if (!empty($action_products)) { ?>
			<div>
				<h2><span>Akční výrobky:</span></h2>
				<?php foreach ($action_products as $action_product) {?>
				<div class="right_sidebar_product">
					<h3><a href="/<?php echo $action_product['Product']['url']?>"><?php echo $action_product['Product']['name']?></a></h3>
					<div class="image_holder">
						<a href="/<?php echo $action_product['Product']['url']?>">
							<img src="/product-images/small/<?php echo $action_product['Image']['name']?>" alt="Obrázek <?php $action_product['Product']['name']?>" width="45px" />
						</a>
					</div>
					<div class="prices_holder">
						běžná cena: <span class="old_price"><?php echo $action_product['Product']['retail_price_with_dph']?> Kč</span><br />
						<span class="regular_price">cena: <?php echo $action_product['Product']['price']?> Kč</span>
						<input class="right_sidebar_cart_add" type="submit" value="Vložit do košíku">
					</div>
					
					
					
				</div>
				<?php } ?>
				<div style="clear:both"></div>
			</div>
			<?php } ?>
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
</body>
</html>
<?php echo $this->element('sql_dump')?>