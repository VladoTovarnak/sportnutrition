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
			<div>
				<h2><span>Akční výrobky:</span></h2>
				<div class="product card small">
					<h3><a href="/super-bcaa-85-000mg-liquid-1l-super-bcaa-85-000mg-liquid-1l-p3252">Super BCAA 85 000mg liquid 1l + Super BCAA 85 000mg liquid 1l</a></h3>
					<a href="#"><img src="/product-images/small/super-bcaa-85-000-liquid-1l-1l.jpg" alt="Obrázek Super BCAA 85 000mg liquid 1l + Super BCAA 85 000mg liquid 1l" /></a>
					<div class="rating"></div>
					<b class="price">478 Kč</b>
				</div>
				<div class="product card small">
					<h3><a href="/xtreme-bcaa-2000-15tbl-blistr-p3632">Xtreme BCAA 2000 15tbl blistr</a></h3>
					<a href="#"><img src="/product-images/small/xtreme-bcaa-2000-fitness-authority.jpg" alt="Obrázek Xtreme BCAA 2000 15tbl blistr" /></a>
					<div class="rating"></div>
					<b class="price">49 Kč</b>
				</div>
				<div class="product card small">
					<h3><a href="/excelent-protein-bar-85g-p1442">Excelent protein bar 85g</a></h3>
					<a href="#"><img src="/product-images/small/excelent-protein-bar-85g.jpg" alt="Obrázek Excelent protein bar 85g" /></a>
					<div class="rating"></div>
					<b class="price">41 Kč</b>
				</div>
				<div class="product card small">
					<h3><a href="/gela-pure-900-250cps-p1770">GELA Pure 900 250cps</a></h3>
					<a href="#"><img src="/product-images/small/gela-pure-900-250cps_1.jpg" alt="Obrázek GELA Pure 900 250cps" /></a>
					<div class="rating"></div>
					<b class="price">308 Kč</b>
				</div>
				<div style="clear:both"></div>
			</div>
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