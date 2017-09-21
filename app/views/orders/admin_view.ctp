<table id="orderDisplay">
	<tr>
		<td valign="top">
		
		<?php
if ( !empty( $order['Order']['comments'] ) ){ ?>
			<h3>Komentář od zákazníka</h3>
				<p style="font-size:11px;color:blue;"><?php echo $order['Order']['comments']?></p>
		<?php
} ?>
	
		<h3>Objednávka č. <?php echo $order['Order']['id']?> (<?php echo strftime("%d.%m.%Y %H:%M", strtotime($order['Order']['created']))?>)</h3>
		<ul>
			<li><span class="smallText"><?php echo $html->link('editovat objednávku', array('controller' => 'ordered_products', 'action' => 'edit', $order['Order']['id'])) ?></span></li>
		</ul>

		<table id="productList"  class="tabulka">
			<tr>
				<th>Název produktu</th>
				<th>Množství</th>
				<th>Cena<br />za kus</th>
				<th>Cena<br />celkem</th>
			</tr>
			<?php
foreach ( $order['OrderedProduct'] as $product ){
				// celkova cena za pocet kusu krat jednotkova cena
				$total_products_price = $product['product_quantity'] * $product['product_price_with_dph']; ?>
			<tr>
				<td><?php 
					if (!empty($product['Product'])) { 
						echo $html->link($product['Product']['name'] . '&nbsp;(' . $product['Product']['Manufacturer']['name'] . ')', '/' . $product['Product']['url'], array('escape' => false), false);
					} else {
						echo $product['product_name'];
					}
					// musim vyhodit atributy, pokud nejake produkt ma
					if ( !empty( $product['OrderedProductsAttribute'] ) ){
						echo '<div class="orderedProductsAttributes">';
						foreach( $product['OrderedProductsAttribute'] as $attribute ){
							echo '<span>- <strong>' . $attribute['Attribute']['Option']['name'] . '</strong>: ' . $attribute['Attribute']['value'] . '</span><br />';
						}
						echo '</div>';
				} ?></td>
				<td><?php echo $product['product_quantity'] ?>&nbsp;ks</td>
				<td><?php echo $product['product_price_with_dph'] ?>&nbsp;Kč</td>
				<td><?php echo $total_products_price ?>&nbsp;Kč</td>
			</tr>
			<?php } ?>		
			<tr>
				<th colspan="2" align="right">cena za zboží celkem:</th>
				<td colspan="2" align="right"><?php echo $order['Order']['subtotal_with_dph']?>&nbsp;Kč</td>
			</tr>
			<tr>
				<td colspan="2" align="right">způsob doručení: <?php echo $order['Shipping']['name']?></td>
				<td colspan="2" align="right"><?php echo $order['Order']['shipping_cost']?>&nbsp;Kč</td>
			</tr>
			<tr>
				<th colspan="2" align="right">celková cena objednávky:</th>
				<td colspan="2" align="right"><?php echo ( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?>&nbsp;Kč</td>
			</tr>
		</table>
<?php
			$color = '';
			if ( !empty($order['Status']['color']) ){
				$color = ' style="color:#' . $order['Status']['color'] . '"';
			}
?>
		<h3>Stav objednávky - <?php echo '<span' . $color . '>' . $order['Status']['name'] . '</span>'?></h3>
<?php
	if ( !empty ( $order['Ordernote'] ) ){
?>
		<h3>Poznámky</h3>
		<table class="topHeading" style="width:80%">
			<tr>
				<th>datum</th>
				<th>status</th>
				<th>kdo</th>
				<th>poznámka</th>
			</tr>
<?php
		foreach ( $order['Ordernote'] as $note ){
			echo '
				<tr>
					<td>' . $note['created'] . '</td>
					<td>' . $note['Status']['name'] . '</td>
					<td>' . $note['Administrator']['first_name'] . ' ' . $note['Administrator']['last_name'] . '</td>
					<td>' . $note['note'] . '</td>
				</tr>
			';
		} ?>
		</table>
<?php 	} ?>
		<h3>Změna stavu / poznámka</h3>
		<?php echo $form->Create('Order', array('url' => array('action' => 'edit')))?>
		<fieldset  style="width:70%">
		<table class="leftHeading">
			<tr>
				<th>status:</th>
				<td>
					<?php echo $form->select('Order.status_id', $statuses, $order['Order']['status_id'], array('empty' => false))?>
				</td>
			</tr>
			<tr>
				<th>poznámka:</th>
				<td><?php echo $form->textarea('Ordernote.note', array('cols' => 70, 'rows' => 3))?></td>
			</tr>
			<tr>
				<th>číslo balíku:</th>
				<td><?php echo $form->text('Order.shipping_number')?></td>
			</tr>
			<tr>
				<th>variabilní symbol:</th>
				<td><?php echo $form->text('Order.variable_symbol')?></td>
			</tr>
		</table>
		<?php echo $form->hidden('Order.id', array('value' => $order['Order']['id']))?>
		</fieldset>
		<?php echo $form->end('změnit')?>
	</td>
	<td valign="top">
		<h3>Kontaktní údaje</h3>
		<span class="smallText"><?php echo $html->link('zobrazit profil', array('controller' => 'customers', 'action' => 'view', $order['Order']['customer_id'])) ?></span><br />
		<?php
			//print_r ($order);
			echo 'jméno:&nbsp;' . $order['Customer']['first_name'] . '<br />';
			echo 'příjmení:&nbsp;' . $order['Customer']['last_name'] . '<br />';
			echo 'telefon:&nbsp;' . $order['Order']['customer_phone'] . '<br />';
			echo 'email:&nbsp;' . ife($order['Order']['customer_email'], $order['Order']['customer_email'], 'neuveden');
		?>
		<h3>Fakturační adresa</h3>
		<?php
			echo $order['Order']['customer_name'] . '<br />'
			. ife( $order['Order']['customer_ico'], 'IČO: ' . $order['Order']['customer_ico'] . '<br />', '' )
			. ife( $order['Order']['customer_dic'], 'DIČ: ' . $order['Order']['customer_dic'] . '<br />', '' )
			. $order['Order']['customer_street'] . '<br />'
			. $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . '<br />'
			. $order['Order']['customer_state'] . '<br />
			platba: <strong>' . $order['Payment']['name'] . '</strong>';
		?>
		<h3>Doručovací adresa</h3>
		<?php
			echo $order['Order']['delivery_name'] . '<br />'
			. $order['Order']['delivery_street'] . '<br />'
			. $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . '<br />'
			. $order['Order']['delivery_state'] . '<br />
			doručení: <strong>' . $order['Shipping']['name'] . '</strong>';
			
			if ( $order['Shipping']['id'] == BALIKOVNA_POST_SHIPPING_ID || $order['Shipping']['id'] == ON_POST_SHIPPING_ID ){
				echo '<strong style="color:red">(' . $order['Order']['shipping_delivery_psc'] . ')</strong>';
			}
			if ( $order['Shipping']['id'] == HOMEDELIVERY_POST_SHIPPING_ID ){
				echo '<strong style="color:red">(' . $order['Order']['shipping_delivery_info'] . ')</strong>';
				echo '<br><span style="color:red" id="deliveryInfo"></span>';
			}

			echo '<br />číslo balíku: ' . $html->link($order['Order']['shipping_number'], $order['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $order['Shipping']['tracker_postfix']);
			echo '<br />variabilní symbol: ' . $order['Order']['variable_symbol'];
		?>
	</td>
</tr>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
			type: 'GET',
			url: '/post_offices/delivery_search/<?php echo $order['Order']['shipping_delivery_psc']?>',
			dataType: 'json',
			data: {
			},
			success: function(data) {
				choice = "<?php echo $order['Order']['shipping_delivery_info']?>";
				data = data[0];
				delivery_text = "běžný režim doručení";					
				if ( data.casovaPasma == 'ANO' ){
					// mam pasma vetvim na A a B
					if ( choice == 'A' ){
						delivery_text = "dopolední doručení: " + data.casDopoledniPochuzky;
					} else if ( choice == 'B' ){
						delivery_text = "odpolední doručení: " + data.casOdpoledniPochuzky;
					}
				}
				$("#deliveryInfo").text(delivery_text);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Nefunguje spojeni s postou. /views/orders/admin_view.ctp TS:" + textStatus + " ET:" + errorThrown);
			},
			complete: function(jqXHR, textStatus) {
			}
		});
	});
</script>