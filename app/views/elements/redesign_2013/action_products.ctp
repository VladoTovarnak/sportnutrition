<?php if (isset($action_products) && !empty($action_products)) { ?>
<div>
	<h2><span>Akční výrobky:</span></h2>
	<?php foreach ($action_products as $action_product) {
		$image = '/img/na_small.jpg';
		if (isset($action_product['Image']) && !empty($action_product['Image'])) {
			$path = 'product-images/small/' . $action_product['Image']['name'];
			if (file_exists($path) && is_file($path) && getimagesize($path)) {
				$image = '/' . $path;
			}
		}
	?>
	<div class="right_sidebar_product">
		<h3><a href="/<?php echo $action_product['Product']['url']?>"><?php echo $action_product['Product']['name']?></a></h3>
		<div class="image_holder">
			<a href="/<?php echo $action_product['Product']['url']?>">
				<img src="<?php echo $image?>" alt="Obrázek <?php $action_product['Product']['name']?>" width="45px" />
			</a>
		</div>
		<div class="prices_holder">
			běžná cena: <span class="old_price"><?php echo number_format($action_product['Product']['retail_price_with_dph'], 0, ',', ' ')?> Kč</span><br />
			<span class="regular_price">cena: <?php echo number_format($action_product['Product']['price'], 0, ',', ' ')?> Kč</span>
			<?php 
				echo $this->Form->create('Product', array('url' => '/' . $action_product['Product']['url'], 'encoding' => false));
				echo '<input class="right_sidebar_cart_add" type="submit" value="Vložit do košíku" />';
				echo $form->end();
			?>
		</div>
	</div>
	<?php } ?>
	<div style="clear:both"></div>
</div>
<?php } ?>