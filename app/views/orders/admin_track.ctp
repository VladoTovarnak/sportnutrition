<h2>Kontrola doručených objednávek</h2>
<p>Nedoručené objednávky byly překontrolovány.</p>
<?php
	if ( !empty($bad_orders) ){
?>
		<ul>
		<?php
			foreach ( $bad_orders as $bad_order_id ){
		?>
			<li><?php echo $html->link($bad_order_id, array('controller' => 'orders', 'action' => 'view', $bad_order_id) ) ?></li>
		<?php
			}
		?>
		</ul>
<?php
	}
?>