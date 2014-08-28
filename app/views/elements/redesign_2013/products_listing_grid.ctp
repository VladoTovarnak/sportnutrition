<div class="products">
<?
foreach ( $products as $product ){
	$image = '/img/na_small.jpg';
	if (isset($product['Image']) && !empty($product['Image'])) {
		$path = 'product-images/small/' . $product['Image']['name'];
		if (file_exists($path) && is_file($path) && getimagesize($path)) {
			$image = '/' . $path;
		}
	}
?>
	<div class="product card">
		<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
		<a class="image_holder" href="/<?php echo $product['Product']['url']?>">
			<img src="<?php echo $image?>" alt="Obrázek <?php echo $product['Product']['name'] ?>" width="90" height="170"/>
		</a>
		<div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
		<p class="comments"><a href="<?php echo $product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
		<?php 
			echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
			echo '<input class="cart_add" type="submit" value="Vložit do košíku" />';
			echo $form->end();
		?>
		<p class="prices">
			<span class="common">Běžná cena: <?php echo $product['Product']['retail_price_with_dph']?> Kč</span><br />
			<span class="price">Cena: <?php echo $product['Product']['price']?> Kč</span>
		</p>
		<p class="guarantee">
			<a href="/garance-nejnizsi-ceny.htm"><span class="first_line">Garance nejnižší ceny!</span></a><br />
			<span class="second_line">Pro více informací pokračujte <a href="/garance-nejnizsi-ceny.htm">zde</a>.</span>
		</p>
	</div>
<? } ?>
	<hr class="cleaner" />
</div>