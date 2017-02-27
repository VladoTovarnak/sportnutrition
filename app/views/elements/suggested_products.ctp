<div id="suggestedProducts">
<?php
	foreach ( $products as $product ){
?>
	<div class="wrapper">
		<div class="imageWrapper"><img src="/product-images/small/<?php echo $product['Image'][0]['name']?>" alt="" /></div>
		<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
		<p><?php echo $product['Product']['short_description']?>&nbsp;<a href="/<?php echo $product['Product']['url']?>">více info</a></p>
		<br />
		<span>cena: od <?php echo $product['Product']['price']?> Kč</span>
		<div class="clearer"></div>
	</div>
<?php
	}
?>
</div>