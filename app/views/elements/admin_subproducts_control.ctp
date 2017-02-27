<?php if (!empty($subproducts)) { ?>
<h2>Varianty produktu <?php echo $product['Product']['name'] ?></h2>
<?php
echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'add_subproducts', $product['Product']['id']))); ?>
<table>
	<tr>
		<th>Atributy</th>
		<th>Přírůstková cena s&nbsp;DPH</th>
		<th>Povoleno</th>
	</tr>
	<?php
foreach ($subproducts as $subproduct) { ?>
	<tr>
		<td>
			<?php
foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) { ?>
					-&nbsp;<?php echo $attributes_subproduct['Attribute']['Option']['name'] ?>: <?php echo $attributes_subproduct['Attribute']['value'] ?><br/>
			<?php
} ?>
		</td>
		<td><?php echo $form->input('Product.' . $subproduct['Subproduct']['id'] . '.price_with_dph', array('label' => false, 'size' => 10, 'value' => $data['Product'][$subproduct['Subproduct']['id']]['price_with_dph'], 'after' => '&nbsp;Kč')) ?></td>
		<td>
			<?php echo $form->checkbox('Product.' . $subproduct['Subproduct']['id'] . '.active', array('label' => false, 'checked' => ($data['Product'][$subproduct['Subproduct']['id']]['active'] == 1))); ?>
			<?php echo $form->hidden('Product.' . $subproduct['Subproduct']['id'] . '.product_id', array('value' => $product['Product']['id'])); ?>
		</td>
	</tr>
	<?php
} ?>
</table>
<?php
	echo $form->submit('Odeslat');
	echo $form->end();
}
?>