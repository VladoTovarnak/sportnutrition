<div class="mainContentWrapper">
	<h2><span><?php echo $page_heading?></span></h2>
	<br/>
	<?php echo $this->Html->link('<< Krok 2/4: Výběr dopravy a platby', array('controller' => 'orders', 'action' => 'set_payment_and_shipping'), array('escape' => false))?>
	<br/><br/>
	<table id="recapWrapper" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td class="leftBorder" valign="top">
				<h3>Objednané zboží - <?php echo $this->Html->link('upravit', array('controller' => 'carts_products', 'action' => 'index'), array('class' => 'smallLinkEdit'))?></h3>
				<table id="recapProducts" cellpadding="5" cellspacing="0">
					<tr>
						<th style="width:50%">Název produktu</th>
						<th style="width:30%">Množství</th>
						<th>Cena za kus</th>
						<th>Cena celkem</th>
					</tr>
					<?
						$final_price = 0;
						$final_price_wout = 0;
						$first = true;
						$border = '';
						foreach ( $cart_products as $cart_product ){
							$tax_class_coef = 1 + $cart_product['Product']['TaxClass']['value'] / 100;
							$final_price_wout = $final_price_wout + (($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) / $tax_class_coef );
							$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'];
							if ( $first ){
								$border = ' style="border-top:1px solid #EDF9FF"';
								$first = false;
							}
					?>
					<tr<?php echo $border ?>>
						<td>
							<strong><?php echo $cart_product['Product']['name'] ?></strong>
					<?php 	if ( !empty($cart_product['CartsProduct']['product_attributes']) ){ ?>
							<br />
							<div style="font-size:11px;padding-left:20px;">
					<?php 		foreach ( $cart_product['CartsProduct']['product_attributes'] as $option => $value ){ ?>
								<strong><?php echo $option ?></strong>: <?php echo $value ?><br />
					<?php 		} ?>
							</div>
					<?php 	} ?>
						</td>
						<td><?php echo $cart_product['CartsProduct']['quantity'] ?>&nbsp;ks</td>
						<td align="right"><?php echo front_end_display_price($cart_product['CartsProduct']['price_with_dph']) ?>&nbsp;Kč</td>
						<td align="right"><?php echo front_end_display_price($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) ?>&nbsp;Kč</td>
					</tr>
					<?php } ?>
					
					<tr>
						<th colspan="2" align="right">cena za zboží celkem:</td>
						<td colspan="2" align="right">
							<strong><?php echo front_end_display_price($final_price) ?> Kč</strong><br />
							<span style="font-size:10px">(<?php echo front_end_display_price($final_price_wout, 2) ?> Kč bez DPH)</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							způsob doručení: <strong><?=$shipping['Shipping']['name']?></strong><br />
							<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'set_payment_and_shipping'), array('class' => 'smallLinkEdit')) ?>
						</td>
						<td align="right"><?=front_end_display_price($order['shipping_cost'])?>&nbsp;Kč</td>
						<td align="right"><?=front_end_display_price($order['shipping_cost'])?>&nbsp;Kč</td>
					</tr>
					<tr>
						<td colspan="2">
							způsob platby: <strong><?=$payment['Payment']['name']?></strong><br />
							<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'set_payment_and_shipping'), array('class' => 'smallLinkEdit')) ?>
						</td>
						<td align="right">0&nbsp;Kč</td>
						<td align="right">0&nbsp;Kč</td>
					</tr>
					<tr>
						<th colspan="2" class="totalPrice">celková cena objednávky:</td>
						<td colspan="2" class="totalPrice"><?php echo front_end_display_price($final_price + $order['shipping_cost'])?> Kč</td>
					</tr>
					<?php if (isset($order['comments']) && !empty($order['comments'])) { ?>
					<tr>
						<th>Váš komentář</th>
						<td colspan="3"><?php echo $order['comments']?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="4" align="right">
							<?php echo $this->Html->link('>> Krok 4/4: Dokončit objednávku', array('controller' => 'orders', 'action' => 'finalize'), array('id' => 'finalLink'))?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td valign="top">
				<h3>Adresa doručení</h3>
				<? echo $address['name']?><br />
				<? echo $address['street'] . ' ' . $address['street_no']?><br />
				<? echo $address['zip'] . ' ' . $address['city'];?><br />
				<? echo $address['state']?><br />
				<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'address_edit', 'type' => 'd'), array('class' => 'smallLinkEdit')) ?>
			</td>
			<td>
				<h3>Fakturační adresa</h3>
				<? echo $address_payment['name']?><br />
				<? echo $address_payment['street'] . ' ' . $address_payment['street_no']?><br />
				<? echo $address_payment['zip'] . ' ' . $address_payment['city']?><br />
				<? echo $address_payment['state']?><br />
				<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'address_edit', 'type' => 'f'), array('class' => 'smallLinkEdit')) ?>
			</td>
	</table>
</div>