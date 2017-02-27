<table id="productsList" cellspacing="0" cellpadding="0">
	<tr>
		<th>název produktu</th>
		<th style="width:50%">krátký popis</th>
		<th>cena</th>
	</tr>
<?php
	$i = 0;
	foreach ( $products as $product ){
		$bg = ' style="background-color:#F5F5F5"';
		$i++;
		if ( $i % 2 == 0 ){
			$bg = '';
		}
?>
	<tr<?php echo $bg?>>
		<th valign="top">
			<h2><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h2>
		</th>
		<td>
			<p class="productsShortDescription"><?php echo $product['Product']['short_description']?></p>
		</td>
		<td align="right">
			<?php
				if ( $product['Product']['discount_price'] < $product['Product']['retail_price_with_dph'] ){
			?>
					<span class="old_price"><?php echo $product['Product']['retail_price_with_dph']?> Kč</span><br />
           			<strong><?php echo $product['Product']['discount_price']?> Kč</strong>
           	<?php
				} else {
			?>
					<strong><?php echo $product['Product']['retail_price_with_dph']?> Kč</strong>
			<?php
				}
			?>
		</td>
	</tr>
<?php
	}
?>
</table>