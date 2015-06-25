<tr>
	<th colspan="2" align="right">cena za zboží:</th>
	<td colspan="2" align="right"><strong><span class="final-price goods-price-span"<?php echo ($type == 'cart_summary' ? ' id="GoodsPriceSpan"' : '') ?>><?php echo intval($final_price) ?></span> Kč</strong></td>
<?php if (isset($type) && $type == 'cart_summary') { ?>
	<td>&nbsp;</td>
<?php } ?>
</tr>
<tr>
	<th colspan="2" align="right">cena za dopravu:</th>
	<td colspan="2" align="right" class="shipping-price-cell"<?php echo ($type == 'cart_summary' ? ' id="ShippingPriceCell"' : '') ?>><strong>
	<?php if ($shipping_price == 0) { ?>
	<span class="final-price shipping-price-span"<?php echo ($type == 'cart_summary' ? ' id="ShippingPriceSpan"' : '') ?>>ZDARMA</span>
	<?php } else {
		if (isset($this->data['Order']['shipping_id'])) { ?>
	<span class="final-price shipping-price-span"<?php echo ($type == 'cart_summary' ? ' id="ShippingPriceSpan"' : '') ?>><?php echo intval($shipping_price)?></span> Kč
		<?php } else { ?>
	od <span class="final-price shipping-price-span"<?php echo ($type == 'cart_summary' ? ' id="ShippingPriceSpan"' : '') ?>><?php echo intval($shipping_price)?></span> Kč
		<?php } ?>
	 <?php } ?>
	 </strong></td>
<?php if (isset($type) && $type == 'cart_summary') { ?>
	<td>&nbsp;</td>
<?php } ?>
</tr>
<tr>
	<th colspan="2" align="right">cena celkem:</th>
	<td colspan="2" align="right" class="total-price-cell"<?php echo ($type == 'cart_summary' ? ' id="TotalPriceCell"' : '') ?>><strong>
	<?php
	$total_price = intval($shipping_price + $final_price);
	if (isset($this->data['Order']['shipping_id'])) { ?>
	<span class="final-price total-price-span"<?php echo ($type == 'cart_summary' ? ' id="TotalPriceSpan"' : '') ?>><?php echo $total_price?></span> Kč
	<?php } else { ?>
	od <span class="final-price total-price-span"<?php echo ($type == 'cart_summary' ? ' id="TotalPriceSpan"' : '') ?>><?php echo $total_price?></span> Kč
	 <?php } ?>
	</strong></td>
<?php if (isset($type) && $type == 'cart_summary') { ?>
	<td>&nbsp;</td>
<?php } ?>
</tr>