<h2>Duplikace produktu</h2>
	<?php echo $form->create('Product', array('url' => array('action' => 'copy', $this->data['Product']['id'])));?>
	<fieldset>
		<legend>Kopírovat produkt</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>kopírovat do</th>
				<td><?php echo $form->select('Product.category_id', $categories, null, array('empty' => false))?></td>
			</tr>
		</table>
	</fieldset>
<?php
echo $form->submit('Duplikovat');?>