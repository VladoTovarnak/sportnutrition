<h2><span><?php echo $page_heading?></span></h2>
<table id="customerLayout">
	<tr>
		<th>Informace o Vás</th>
	</tr>
	<tr>
		<td valign="top">
			<table class="leftHeading">
				<tr>
					<th>Jméno a příjmení:</th>
					<td><?php echo $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']?></td>
				</tr>
				<tr>
					<th>telefon:</th>
					<td><?php echo $customer['Customer']['phone']?></td>
				</tr>
				<tr>
					<th>email:</th>
					<td><?php echo ife( $customer['Customer']['email'], $customer['Customer']['email'], 'neuveden' )?></td>
				</tr>
				<?php foreach ($customer['CustomerLogin'] as $customer_login) { ?>
				<tr>
					<th>login:</th>
					<td><?php echo $customer_login['login']?></td>
				</tr>
				<tr>
					<th>heslo:</th>
					<td>********</td>
				</tr>
				<?php } ?>
				<tr>
					<th>&nbsp;</th>
					<td><?php echo $html->link('editovat', array('controller' => 'customers', 'action' => 'edit'))?></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<th>Vaše objednávky</th>
	</tr>
	<tr>
		<td valign="top">
			<?php
			$count = count($customer['Order']);
			if ( $count == 0 ){
				echo 'Nevytvořil(a) jste zatím žádnou objednávku.';
			} else {
			?>
					<table class="topHeading">
						<tr>
							<th>číslo</th>
							<th>vytvořena</th>
							<th>cena</th>
							<th>stav</th>
							<th>&nbsp;</th>
						</tr>
						<?php
							$max_order_count = 3;
							if ( $count < $max_order_count ){
								$max_order_count = $count;
							}
							for ( $i = 0; $i < $max_order_count; $i++ ){
						?>

						<tr>
							<td><?php echo $customer['Order'][$i]['id']?></td>
							<td><?php echo cz_date_time($customer['Order'][$i]['created'])?></td>
							<td><?php echo front_end_display_price($customer['Order'][$i]['subtotal_with_dph'] + $customer['Order'][$i]['shipping_cost']) . '&nbsp;Kč' ?></td>
							<td><?php
									$color = '';
									if ( !empty($customer['Order'][$i]['Status']['color']) ){
										$color = ' style="color:#' . $customer['Order'][$i]['Status']['color'] . '"';
									}
									echo '<span' . $color . '>' . $customer['Order'][$i]['Status']['name'] . '</span>';
								?>
							</td>
							<td>
								<?php echo $html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $customer['Order'][$i]['id']));?>
							</td>
						</tr>
						<?php
							}
						?>
						<tr>
							<td colspan="5">
						<?php
						if ( $count > 3 ){
							echo 'Zobrazeny jsou poslední tři objednávky z ' . $count . ' celkem.<br />';
						}
						echo $html->link('zobrazit seznam objednávek', array('controller' => 'customers', 'action' => 'orders_list'));
						?>
							</td>
						</tr>
					</table>
			<?php
			}
			?>
		</td>
	</tr>

	<tr>
		<th colspan="2">Vaše adresy</th>
	</tr>
	<tr>
		<td colspan="2">
			<table class="topHeading" width="100%">
				<tr>
					<th>Fakturační adresa</th>
					<th>Doručovací adresa</th>
				</tr>
				<tr>
					<td>
						<?php
						foreach ( $customer['Address'] as $address ){
							if ( $address['type'] == 'f' ){
								echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
							}
						}
						?>
					</td>
					<td>
						<?php	
						foreach ( $customer['Address'] as $address ){
							if ( $address['type'] == 'd' ){
								echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php echo $html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'f')) ?></td>
					<td><?php echo $html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'd')) ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
