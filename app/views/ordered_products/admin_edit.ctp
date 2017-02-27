<h2>Úprava objednávky č. <?php echo $id ?></h2>
<p><?php echo $html->link('zpět na objednávku', array('controller' => 'orders', 'action' => 'view', $order['Order']['id'])) ?></p>
<table id="productList" class="tabulka">
	<tr>
		<th>Objednaný produkt</th>
		<th>Změna atributů</th>
		<th>Množství</th>
		<th>Cena<br />
			za kus</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	foreach ( $products as $product ){
		// celkova cena za pocet kusu krat jednotkova cena
		$total_products_price = $product['OrderedProduct']['product_quantity'] * $product['OrderedProduct']['product_price_with_dph'];
	?>
				<tr>
					<td>
						<?php echo $product['Product']['name'] ?>
						<?php

						// musim vyhodit atributy, pokud nejake produkt ma
						if ( !empty( $product['OrderedProductsAttribute'] ) ){
						?>
							<div class="orderedProductAttributes">
							<?php
foreach( $product['OrderedProductsAttribute'] as $attribute ){ ?>
									<span>- <strong> <?php echo $attribute['Attribute']['Option']['name'] ?></strong>: <?php echo $attribute['Attribute']['value'] ?></span><br /> 
							<?php
} ?>
							</div>
						<?php

						}

						echo '<br /><span style="font-size:11px">cena za kus: <strong>' . $product['OrderedProduct']['product_price_with_dph'] . ' Kč</strong></span>';
						?>
					</td>
					<td>
						<?php
							if ( !empty($product['Subs']) ){
								echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
						?>
							<table style="font-size:10px">
								<?php
								foreach ( $product['Subs'] as $sub ){
									if ( !empty($sub['Value']) ){
										echo '<tr><th align="right">' . $sub['Option']['name'] . '</th>';
										echo '<td><select name="data[OrderedProduct][Option][' . $sub['Option']['id'] . ']" style="font-size:10px;">';
										foreach ( $sub['Value'] as $value ){
											$selected = '';
											foreach ( $product['OrderedProductsAttribute'] as $attr ){
												if ( $attr['attribute_id'] == $value['id']){
													$selected = ' selected="selected"';
												}
											}
											echo '<option value="' . $value['id'] . '"' . $selected . '>' . $value['value'] . '</option>';
										}
										echo '</select></td></tr>';
									}
								}
								?>
								<tr>
									<th>&nbsp;</th>
									<td><?php echo $form->submit('změnit atributy') ?></td>
								</tr>
							</table>
						<?php
								echo $form->hidden('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
								echo $form->hidden('OrderedProduct.change_switch', array('value' => 'attributes_change'));
								echo $form->end();
							} else {
								echo '&nbsp;';
							}
						?>
					</td>
					<td>
						<?php
							echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
							echo $form->input('OrderedProduct.product_quantity', array('value' => $product['OrderedProduct']['product_quantity'], 'label' => false, 'div' => false, 'size' => 3)) . ' ks';
							echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
							echo $form->hidden('OrderedProduct.change_switch', array('value' => 'quantity_change'));
						?>
							<br />
						<?php
							echo $form->submit('změnit počet');
							echo $form->end();
						?>
							
					</td>
					<td>
					<?php
						echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
					?>
						<select name="data[OrderedProduct][product_price_with_dph]">
							<option value="<?php echo $product['Product']['retail_price_with_dph'] ?>"<?php echo ( $product['Product']['retail_price_with_dph'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "" ) ?>>
								základní cena: <?php echo $product['Product']['retail_price_with_dph'] ?> Kč
							</option>
						<?php if ($product['Product']['discount_common'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_common'] ?>"<?php echo ($product['Product']['discount_common'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
								běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
							</option>
						<?php } ?>
						<?php if (isset($product['Product']['discount_member']) && $product['Product']['discount_member'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_member'] ?>"<?php echo ($product['Product']['discount_member'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
								členská sleva: <?php echo $product['Product']['discount_member']?> Kč
							</option>
						<?php } ?>
						</select>
						<br />
						<span style="font-size:10px">ručně:</span> <?php echo $form->input('OrderedProduct.custom_price', array('label' => false, 'size' => 5)); ?>
						<?php
							echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
							echo $form->hidden('OrderedProduct.change_switch', array('value' => 'price_change'));
							echo $form->submit('změnit cenu');
							echo $form->end();
						?>
					</td>
					<td>
						<?php echo $html->link('smazat produkt', array('controller' => 'ordered_products', 'action' => 'delete', $product['OrderedProduct']['id'])) ?>
					</td>
				</tr>
	<?php

	}
	?>
	<tr>
		<th colspan="2" align="right">
			cena za zboží celkem:
		</th>
		<td colspan="2" align="right">
			<?php echo $order['Order']['subtotal_with_dph']?> Kč
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			způsob doručení:
		</td>
		<td colspan="2" align="right">
			<?php echo $form->create('Order', array('url' => array('action' => 'edit_shipping', $order['Order']['id'])));?>
			<?php echo $form->select('Order.shipping_id', $shipping_choices, $order['Order']['shipping_id'], array('empty' => false));?>
			<?php echo $form->submit('změnit');?>
			<?php echo $form->end();?>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			způsob platby:
		</td>
		<td colspan="2" align="right">
			<?php echo $form->create('Order', array('url' => array('action' => 'edit_payment', $order['Order']['id'])));?>
			<?php echo $form->select('Order.payment_id', $payment_choices, $order['Order']['payment_id'], array('empty' => false));?>
			<?php echo $form->submit('změnit');?>
			<?php echo $form->end();?>
		</td>
	</tr>
	<tr>
		<th colspan="2" align="right">
			celková cena objednávky:
		</th>
		<td colspan="2" align="right">
			<?php echo ( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?> Kč
		</td>
	</tr>
</table>

<h3>Přidat nový produkt</h3>
<?php echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id']))); ?>
<table>
	<tr>
		<td colspan="2">
			<p style="font-size:12px">Napište jakoukoliv část názvu produktu. ("metr" vyhledá např. gluko<strong>metr</strong> i tono<strong>metr</strong> ).</p>
		</td>
	</tr>
	<tr>
		<th>vyhledat produkt</th>
		<th><?php echo $form->input('OrderedProduct.query', array('label' => false)) ?></th>
	</tr>
	<tr>
		<td><?php echo $form->submit('vyhledat') ?></td>
	</tr>
</table>
<?php echo $form->hidden('OrderedProduct.change_switch', array('value' => 'product_query')); ?>
<?php echo $form->end(); ?>
<br/>
<?php
if ( isset($query_products) ){
	echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
?>
<table class="tabulka">
<?php
	foreach ( $query_products as $product ){
?>
				<tr>
					<td>
						<?php echo $this->Html->link($product['Product']['name'], '/' . $product['Product']['url'], array('target' => 'blank')) ?>
					</td>
					<td>
						<?php
							if ( !empty($product['Subs']) ){
						?>
							<table style="font-size:10px">
								<?php
								foreach ( $product['Subs'] as $sub ){
									if ( !empty($sub['Value']) ){
								?>
								<tr>
									<th align="right"><?php echo $sub['Option']['name']?></th>
									<td>
										<select name="data[OrderedProduct][<?php echo $product['Product']['id']?>][Option][<?php echo $sub['Option']['id']?>]" style="font-size:10px;">';
										<?php
foreach ( $sub['Value'] as $value ){ ?>
											<option value="<?php echo $value['id']?>"><?php echo $value['value']?></option>;
										<?php
} ?>
										</select>
									</td>
								</tr>
								<?php
}
								}
								?>
							</table>
						<?php
							} else {
								echo '&nbsp;';
							}
						?>
					</td>
					<td>
						<?php
							echo $form->input('OrderedProduct.' . $product['Product']['id'] . '.product_quantity', array('value' => '1', 'label' => false, 'div' => false, 'size' => 3)) . ' ks';
						?>
					</td>
					<td>
						<select name="data[OrderedProduct][<?php echo $product['Product']['id'] ?>][product_price_with_dph]">
							<option value="<?php echo $product['Product']['retail_price_with_dph'] ?>">
								základní cena: <?php echo $product['Product']['retail_price_with_dph'] ?> Kč
							</option>
						<?php if ($product['Product']['discount_common'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_common'] ?>">
								běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
							</option>
						<?php } ?>
						<?php if (isset($product['Product']['discount_member']) && $product['Product']['discount_member'] > 0) { ?>
							<option value="<?php echo $product['Product']['discount_member'] ?>">
								členská sleva: <?php echo $product['Product']['discount_member']?> Kč
							</option>
						<?php } ?>
						</select>
						<br />
						<span style="font-size:10px">ručně:</span> <?php echo $form->input('OrderedProduct.' . $product['Product']['id'] . '.custom_price', array('label' => false, 'size' => 5)); ?>
					</td>
					<td>
						<?php echo $form->hidden('OrderedProduct.' . $product['Product']['id'] . '.product_id', array('value' => $product['Product']['id'])) ?>
						<?php echo $form->submit('přidat', array('name' => 'data[OrderedProduct][' . $product['Product']['id'] . '][add_it]', 'value' => $product['Product']['id'])) ?>
					</td>
				</tr>
	<?php

	}
?>
</table>
<?php
	echo $form->hidden('OrderedProduct.change_switch', array('value' => 'add_product'));
	echo $form->end();
}
?>