<h2>Statistiky</h2>
<form action="/admin/statistics" method="post">
	od:&nbsp;<?php echo $form->dateTime('Statistic.from', 'DMY', 24)?><br />
	do:&nbsp;<?php echo $form->dateTime('Statistic.to', 'DMY', 24)?><br />
	<?php echo $form->submit('Změnit')?>
</form>
<h3>Nejprodávanější produkty</h3>
<table class="topHeading">
	<tr>
		<th>Název produktu</th>
		<th>Prodané množství</th>
	</tr>
<?php
foreach ( $sold_products as $sold_product ){ ?>
	<tr>
		<td><?php echo $this->Html->link($sold_product['Product']['name'], '/' . $sold_product['Product']['url'])?></td>
		<td><?php echo $sold_product[0]['quantity']?></td>
	</tr>
<?php } ?>
</table>

<h3>Objednávky</h3>
<p>V daném období bylo uskutečněno <strong><?php echo $orders['Order']['count']?> objednávek</strong> v celkové hodnotě <strong><?php echo format_price($orders['Order']['income'])?></strong>.</p>