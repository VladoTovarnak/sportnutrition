<div>
	<h2>Smazané produkty</h2>
	<?php
		if ( empty($products) ){
	?>
		<p>Žádné proukty, které nejsou přiřazeny do žádné kategorie, nebyly nalezeny.
	<?php
		} else{
		// vypisu produkty, ktere byly vymazane
	?>
		<table class="topHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Id</th>
				<th>Název</th>
				<th>Cena</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			foreach ( $products as $product ){
		?>
			<tr>
				<td><?php echo $product['Product']['id']?></td>
				<td><?php echo $product['Product']['name']?></td>
				<td><?php echo $product['Product']['retail_price_with_dph']?></td>
				<td style="font-size:12px;">
					<?php echo $html->link('Zařadit do prodeje', array('controller' => 'categories_products', 'action' => 'add', $product['Product']['id'])) ?> |
					<?php echo $html->link('Smazat úplně', array('controller' => 'products', 'action' => 'delete_total', $product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?')?>
				</td>
			</tr>
		<?php

			}
		?>
		</table>
	<?php
		} 
	?>
</div>