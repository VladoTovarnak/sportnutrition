<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
        // To by moved to general styles after all layouts are responsive
        <style>
            @media screen and (max-width: 640px) {

                #sidebar {
                    display: none;
                }
            }
        </style>
	</head>
<body>

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
				<li>
					<label>
						<input type="checkbox" class="filter_manufacturer" rel="<?php echo $filter_manufacturer['Manufacturer']['id']?>" id="Manufacturer<?php echo $filter_manufacturer['Manufacturer']['id']?>"/>
						<?php 
							$manufacturer = $filter_manufacturer['Manufacturer']['name'] . '(' . $filter_manufacturer[0]['Manufacturer__products_count'] . ')';
							$url = '/' . $category['Category']['url'] . '?m=' . $filter_manufacturer['Manufacturer']['id'];
							echo $this->Html->link($manufacturer, $url);
						?>
					</label>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>

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
	echo $this->element(REDESIGN_PATH . 'default_foot2');
?>
<script type="text/javascript">
	fbq('track', 'ViewCategory', {
	  content_name: '<?php echo $_heading?>',
	  content_category: '<?php echo $fb_content_category ?>',
	  content_ids: [<?php echo $fb_content_ids ?>],
	  content_type: 'product'
	});
</script>
</body>
</html>
<?php echo $this->element('sql_dump')?>