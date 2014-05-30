<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>products_pagination.js"></script>
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
				<li><a href="#" class="filter_manufacturer" rel="<?php echo $filter_manufacturer['Manufacturer']['id']?>"><?php echo $filter_manufacturer['Manufacturer']['name']?>  (<?php echo $filter_manufacturer[0]['Manufacturer__products_count']?>)</a></li>
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
				<li><a href="#" class="filter_attribute" rel="<?php echo $filter_attribute['Attribute']['id']?>"><?php echo $filter_attribute['Attribute']['value']?>  (<?php echo $filter_attribute[0]['Attribute__products_count']?>)</a></li>
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
		</div>
		<hr class="cleaner" />
	</div>
	<hr class="cleaner" />
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
</div>

</body>
</html>
<?php echo $this->element('sql_dump')?>