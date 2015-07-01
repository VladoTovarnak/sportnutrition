<h1>Objednávka č. <?php echo $order['Order']['id']?></h1>

<table class='tabulka'>
	<tr>
		<th>Dodavatel</th>
		<th>Odběratel</th>
	</tr>
	<tr valign="top">
		<td width="50%" valign="top">
			<br />
			<strong>SPORT NUTRITION, Vávra Stanislav</strong><br /> <br />
			IČ: 42956391<br />
			DIČ: <br />
			Adresa: U solných mlýnů 2 - Lékárna, 783 71 Olomouc<br />
			Telefon: 608 962 685<br />
			E-mail: <a href="mailto:info@sportnutrition.cz">info@sportnutrition.cz</a><br />
			Web: <a href="http://www.sportnutrition.cz/">http://www.sportnutrition.cz/</a></strong><br />
		</td>
		<td width="50%">
			<br />
			<strong></strong><br />
			<strong><?php echo $order['Order']['customer_first_name']?> <?php echo $order['Order']['customer_last_name']?></strong><br />
<?php 
			$invoice_address = '';
			if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) {
				$invoice_address = $order['Order']['customer_street'] . ', ' . $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . ', ' . $order['Order']['customer_state'];
			}
			echo $invoice_address;
?><br />
			IČ: <?php echo $order['Order']['customer_ico']?><br />
			DIČ: <?php echo $order['Order']['customer_dic']?><br /> <br />
			Telefon: <?php echo $order['Order']['customer_phone']?><br />
			E-mail: <a href="mailto:<?php echo $order['Order']['customer_email']?>"><?php echo $order['Order']['customer_email']?></a><br /> <br />
<?php 
			$delivery_address = '';
			if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) {
				$delivery_address = 'Dodací adresa: ' . $order['Order']['delivery_street'] . ', ' . $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . ', ' . $order['Order']['delivery_state'];
			}
			echo $delivery_address;
?><br />
		</td>
	</tr>
</table>
<br />

<table class="tabulka">
	<tr valign="top">
		<th width="40">počet</th>
		<th>název, kód, poznámka</th>
		<th width="100">cena/ks</th>
		<th width="100">cena celkem</th>
	</tr>
	<?php foreach ($order['OrderedProduct'] as $ordered_product) { ?>
	<tr valign='top'>
		<td align='right'><?php echo $ordered_product['product_quantity']?></td>
		<td>
			<?php echo $ordered_product['product_name']?><br />
			<?php foreach ($ordered_product['OrderedProductsAttribute'] as $attribute) { 
				echo $attribute['Attribute']['Option']['name'] . ': ' . $attribute['Attribute']['value'];
			}?>
		</td>
		<td align='right'><?php echo round($ordered_product['product_price_with_dph'])?>&nbsp;Kč</td>
		<td align='right'><?php echo round($ordered_product['product_price_with_dph'] * $ordered_product['product_quantity'])?>&nbsp;Kč</td>
	</tr>
	<?php } ?>
	<tr valign='top'>
		<td align='right'>1</td>
		<td><?php echo $order['Shipping']['name']?></td>
		<td align='right'><?php echo round($order['Order']['shipping_cost'])?>&nbsp;Kč</td>
		<td align='right'><?php echo round($order['Order']['shipping_cost'])?>&nbsp;Kč</td>
	</tr>
	<tr valign='top'>
		<td align='right'>1</td>
		<td><?php echo $order['Payment']['name']?></td>
		<td align='right'>0 Kč</td>
		<td align='right'>0 Kč</td>
	</tr>
</table>
<br />

<h2>Celkem k úhradě: <?php echo $order['Order']['orderfinaltotal']?> Kč</h2>
<?php if (!empty($order['Order']['comments'])) { ?>
<h3><?php echo $order['Order']['comments']?></h3>
<?php } ?>