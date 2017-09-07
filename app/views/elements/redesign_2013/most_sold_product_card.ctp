<?php 	
	$image = '/img/na_small.jpg';
	if (isset($most_sold_product['Image']) && !empty($most_sold_product['Image'])) {
		$path = 'product-images/small/' . $most_sold_product['Image']['name'];
		if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
			$path = 'product-images-new/small/' . $most_sold_product['Image']['name'];
		}
		if (file_exists($path) && is_file($path) && getimagesize($path)) {
			$image = '/' . $path;
		}
	}
?>
	
<div class="product card">
	<h3><a href="/<?php echo $most_sold_product['Product']['url']?>"><?php echo $most_sold_product['Product']['name']?></a></h3>
	<a class="image_holder" href="/<?php echo $most_sold_product['Product']['url']?>">
		<img src="<?php echo $image?>" alt="Obrázek <?php echo $most_sold_product['Product']['name'] ?>" width="90" height="170"/>
	</a>

	<p class="comments"><a href="/<?php echo $most_sold_product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="/<?php echo $most_sold_product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
	<?php
		if ( count($most_sold_product['Subproduct']) < 1 ){
			echo $this->Form->create('Product', array('url' => '/' . $most_sold_product['Product']['url'], 'encoding' => false));
			echo $this->Form->hidden('Product.id', array('value' => $most_sold_product['Product']['id']));
			echo $this->Form->hidden('Product.quantity', array('value' => 1));
			echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add', 'onclick' => 'fireAddToCart(' . $most_sold_product['Product']['id'] . ', "' . $most_sold_product['Product']['name'] . '", ' . $most_sold_product['Product']['price'] . ');'));
			echo $this->Form->end();
		} else {
			?>
				<a style="float:left" href="/<?php echo $most_sold_product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
			<?php
		}
	?>
	<p class="prices">
			<span class="common">Běžná cena: <?php echo front_end_display_price($most_sold_product['Product']['retail_price_with_dph'])?> Kč</span><br />
			<span class="price">Cena: <?php echo front_end_display_price($most_sold_product['Product']['price'])?> Kč</span>
	</p>
</div>