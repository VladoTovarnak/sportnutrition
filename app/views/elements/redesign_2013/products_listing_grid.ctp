<div class="products">
<?
foreach ( $products as $product ){
	$image = '/images/na.jpg';
	if (isset($product['Image']) && !empty($product['Image'])) {
		$image = '/product-images/small/' . $product['Image']['name'];
	}
?>
	<div class="product card">
		<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
		<a href="/<?php echo $product['Product']['url']?>"><img src="<?php echo $image?>" alt="Obrázek <?php echo $product['Product']['name'] ?>" /></a>
		<div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
		<p><?php echo $product['Product']['short_description']?></p>
		<b class="price"><?php echo $product['Product']['price']?> Kč</b>
	</div>
<? } ?>
	<hr class="cleaner" />
</div>