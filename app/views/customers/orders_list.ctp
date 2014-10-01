<h2><span><?php echo $page_heading?></span></h2>
<table class="topHeading" width="100%">
	<tr>
		<th>číslo</th>
		<th>vytvořena</th>
		<th>cena</th>
		<th>stav</th>
		<th>&nbsp;</th>
	</tr>
<? foreach ( $orders as $order ){ ?>
	<tr>
		<td><?=$order['Order']['id']?></td>
		<td><?=cz_date_time($order['Order']['created'])?></td>
		<td><?=front_end_display_price($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost']) . '&nbsp;Kč' ?></td>
		<td><?
				$color = '';
				if ( !empty($order['Status']['color']) ){
					$color = ' style="color:#' . $order['Status']['color'] . '"';
				}
				echo '<span' . $color . '>' . $order['Status']['name'] . '</span>';
			?>
		</td>
		<td>
			<?=$html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $order['Order']['id']));?>
		</td>
	</tr>
<? } ?>
</table>