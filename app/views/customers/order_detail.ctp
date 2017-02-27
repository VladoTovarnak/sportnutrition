<h2><span><?php echo $page_heading?></span></h2>
<table class="topHeading" width="100%">
	<tr>
		<th>název</th>
		<th>jedn. cena</th>
		<th>cena celkem</th>
	</tr>
	<?php
foreach ($order['OrderedProduct'] as $product) { ?>
	<tr>
		<td><?php 
			echo $product['product_quantity'] . ' &times; ' . (isset($product['Product']['name']) ? $this->Html->link($product['Product']['name'], $product['Product']['url'], array('target' => 'blank')) : '<em>produkt není v nabídce</em>');
			// musim vyhodit atributy, pokud nejake produkt ma
			if ( !empty( $product['OrderedProductsAttribute'] ) ){
				echo '<br /><div style="font-size:11px;padding-left:20px;">';
				foreach( $product['OrderedProductsAttribute'] as $attribute ){
					echo '<span>- <strong>' . $attribute['Attribute']['Option']['name'] . '</strong>: ' . $attribute['Attribute']['value'] . '</span><br />';
				}
				echo '</div>';
			}
		?></td>
		<td><?php echo front_end_display_price($product['product_price_with_dph']) ?>&nbsp;Kč</td>
		<td><?php echo front_end_display_price($product['product_price_with_dph'] * $product['product_quantity']) ?>&nbsp;Kč</td>
	</tr>
	<?php } ?>
	<tr>
		<th colspan="2">objednané zboží celkem:</th>
		<td><?php echo front_end_display_price($order['Order']['subtotal_with_dph'])?> Kč</td>
	</tr>
	<tr>
		<th colspan="2">způsob dopravy:</th>
		<td><?php echo $order['Shipping']['name']?> (<?php echo front_end_display_price($order['Order']['shipping_cost'])?>&nbsp;Kč)</td>
	</tr>
	<tr>
		<th colspan="2">způsob platby:</th>
		<td><?php echo $order['Payment']['name']?></td>
	</tr>
	<tr>
		<th colspan="2">celková cena objednávky:</th>
		<td><?php echo front_end_display_price($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?>&nbsp;Kč</td>
	</tr>
	<?php if (isset($order['Order']['comments']) && !empty($order['Order']['comments'])) { ?>
	<tr>
		<th>Váš komentář</th>
		<td colspan="2"><?php echo $order['Order']['comments']?></td>
	</tr>
	<?php } ?>
</table>
<?php if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) { ?>
<table>
	<tr>
		<th>Fakturační adresa</th>
		<th>Doručovací adresa</th>
	</tr>
	<tr>
		<td><?php
			echo $order['Order']['customer_name'] . '<br />';
			echo $order['Order']['customer_street']  . '<br />';
			echo $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . '<br />';
			echo $order['Order']['customer_state'];
		?></td>
		<td><?php 
			echo $order['Order']['delivery_name'] . '<br />';
			echo $order['Order']['delivery_street'] . '<br />';
			echo $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . '<br />';
			echo $order['Order']['delivery_state'];
		?></td>
	</tr>
</table>
<?php } ?>