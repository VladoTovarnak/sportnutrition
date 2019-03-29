<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
		<?php if (!$product['Product']['active']) { ?>
			<meta name="robots" content="noindex, follow" />
		<?php } ?>
        <?php if (front_end_display_price($product['Product']['retail_price_with_dph'])) { ?>
        <meta property="product:price:amount" content="<?php echo front_end_display_price($product['Product']['retail_price_with_dph']) ?>">
        <meta property="product:price:currency" content="CZK">
        <?php } ?>
        <?php if (front_end_display_price($product['Product']['price'])) { ?>
        <meta property="product:sales_price:amount" content="<?php echo front_end_display_price($product['Product']['price']) ?>">
        <meta property="product:sales_price:currency" content="CZK">
        <?php } ?>
        <?php if ($product['Availability']['name'] === 'skladem') { ?>
            <meta property="product:availability" content="in stock">
        <?php } ?>
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
		<h1 class="product_name"><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['heading']?></a></h1>
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
				<h2><span>S tímto produktem <br />ostatní kupují také:</span></h2>
			<?php
				foreach ($similar_products as $similar_product) {
					$image = '/img/na_small.jpg';
					if (isset($similar_product['Image']) && !empty($similar_product['Image'])) {
						$path = 'product-images/small/' . $similar_product['Image']['name'];
						if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
							$path = 'product-images-new/small/' . $similar_product['Image']['name'];
						}
						if (file_exists($path) && is_file($path) && getimagesize($path)) {
							$image = '/' . $path;
						}
					} ?>
				<div class="right_sidebar_product">
					<h3><a href="/<?php echo $similar_product['Product']['url']?>"><?php echo $similar_product['Product']['name']?></a></h3>
					<div class="image_holder">
						<a href="/<?php echo $similar_product['Product']['url']?>">
							<img src="<?php echo $image?>" alt="Obrázek <?php echo $similar_product['Product']['name']?>" width="45px"/>
						</a>
					</div>
					<div class="prices_holder">
						běžná cena: <span class="old_price"><?php echo $similar_product['Product']['retail_price_with_dph']?> Kč</span><br />
						<span class="regular_price">cena: <?php echo $similar_product[0]['price']?> Kč</span>
						<?php
							if ( count($similar_product['Subproduct']) < 1 ){
									echo $this->Form->create('Product', array('url' => '/' . $similar_product['Product']['url'], 'encoding' => false));
									echo $this->Form->hidden('Product.id', array('value' => $similar_product['Product']['id']));
									echo $this->Form->hidden('Product.quantity', array('value' => 1));
									echo $this->Form->submit('Vložit do košíku', array('class' => 'right_sidebar_cart_add', 'onclick' => 'fireAddToCart(' . $similar_product['Product']['id'] . ', "' . $similar_product['Product']['name'] . '", "' . $similar_product['CategoriesProduct'][0]['Category']['name']. '", ' . $similar_product['Product']['price'] . ');'));
									echo $this->Form->end();
							} else {
						?>
							<a style="float:left" href="/<?php echo $similar_product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
						<?php
							}
						?>
					</div>
				</div>
			<?php	} ?>
				<div style="clear:both"></div>
				</div>
			<?php } ?>
			
			<?php
				if (isset($right_sidebar_products) && !empty($right_sidebar_products)) { ?>
				<div>
				<h2><span>Podobné produkty</span></h2>
			<?php	foreach ($right_sidebar_products as $right_sidebar_product) {
				$image = '/img/na_small.jpg';
				if (isset($right_sidebar_product['Image']) && !empty($right_sidebar_product['Image'])) {
					$path = 'product-images/small/' . $right_sidebar_product['Image']['name'];
					if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
						$path = 'product-images-new/small/' . $right_sidebar_product['Image']['name'];
					}
					if (file_exists($path) && is_file($path) && getimagesize($path)) {
						$image = '/' . $path;
					}
				}
			?>
				<div class="right_sidebar_product">
					<h3><a href="/<?php echo $right_sidebar_product['Product']['url']?>"><?php echo $right_sidebar_product['Product']['name']?></a></h3>
					<div class="image_holder">
						<a href="/<?php echo $right_sidebar_product['Product']['url']?>">
							<img src="<?php echo $image?>" alt="Obrázek <?php echo $right_sidebar_product['Product']['name']?>" width="45px" />
						</a>
					</div>
					<div class="prices_holder">
						běžná cena: <span class="old_price"><?php echo $right_sidebar_product['Product']['retail_price_with_dph']?> Kč</span><br />
						<span class="regular_price">cena: <?php echo $right_sidebar_product['Product']['price']?> Kč</span>
						<?php 
							if ( count($right_sidebar_product['Subproduct']) < 1 ){
								echo $this->Form->create('Product', array('url' => '/' . $right_sidebar_product['Product']['url'], 'encoding' => false));
								echo $this->Form->hidden('Product.id', array('value' => $right_sidebar_product['Product']['id']));
								echo $this->Form->hidden('Product.quantity', array('value' => 1));
								echo $this->Form->submit('Vložit do košíku', array('class' => 'right_sidebar_cart_add', 'onclick' => 'fireAddToCart(' . $right_sidebar_product['Product']['id'] . ', "' . $right_sidebar_product['Product']['name'] . '", "' . $product['CategoriesProduct'][0]['Category']['name']. '", ' . $right_sidebar_product['Product']['price'] . ');'));
								echo $this->Form->end();
							} else{
						?>
								<a style="float:left" href="/<?php echo $right_sidebar_product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
						<?php
							}
						?>
					</div>
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
<?php
	echo $this->element(REDESIGN_PATH . 'heureka_overeno');
	echo $this->element(REDESIGN_PATH . 'facebook_prava');
	echo $this->element(REDESIGN_PATH . 'default_foot2');
?>
<script type="text/javascript">
	fbq('track', 'ViewContent', { 
	    content_type: 'product',
	    content_ids: ['CZ_<?php echo $product['Product']['id'] ?>'],
	    content_name: '<?php echo $product['Product']['name'] ?>',
	    content_category: '<?php echo $product['CategoriesProduct'][0]['Category']['name'] ?>',
	    value: <?php echo $product['Product']['price']?>,
	    currency: 'CZK'
	});

	function fireAddToCart(id, name, cname, price){
		fbq('track', 'AddToCart', { 
		    content_type: 'product',
		    content_ids: '["CZ_' + id + '"]',
		    content_name: "'" + name + "'",
		    content_category: "'" + cname + "'",
		    value: price,
		    currency: 'CZK'
		});
	}
</script>
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "Product",
    "name": "<?php echo ltrim(rtrim($product['Product']['name'])) ?>",
    "image": "https://<?php echo $_SERVER['SERVER_NAME']; ?>/product-images/<?= $product['Image'][0]['name'] ?>",
    "description": "<?php echo ltrim(rtrim($product['Product']['short_description'])) ?>",
    "sku": "<?php echo $product['Product']['id'] ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?php echo $product['Manufacturer']['name'] ?>"
    },
    <?php if ($product['Product']['rate']) { ?>
    "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?php echo $product['Product']['rate'] ?>",
    "reviewCount": "<?php echo $product['Product']['voted_count'] ?>"
    },
    <?php } ?>

    "offers": {
        "@type": "Offer",
    <?php if ($product['Availability']['name'] === 'skladem') { ?>
        "availability": "http://schema.org/InStock",
    <?php } ?>
        "priceCurrency": "CZK",
        "price": "<?php echo str_replace(" ", "", front_end_display_price($product['Product']['price'])) ?>",
        "url": "https://<?php echo $_SERVER['SERVER_NAME']; ?>/<?= $product['Product']['url'] ?>",
        "itemCondition": "new"
    }
}
</script>
</body>
</html>
<?php echo $this->element('sql_dump')?>