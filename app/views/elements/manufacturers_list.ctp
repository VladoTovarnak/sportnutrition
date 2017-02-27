<?php echo $form->create('Manufacturer', array('action' => 'show')) ?>
<?php
	$selected = null;
	if ( isset($this->params['manufacturer_id']) ){
		$selected = $this->params['manufacturer_id'];
	}

	if ( isset($product['Manufacturer']['id']) ){
		$selected = $product['Manufacturer']['id'];
	}
	
	echo '<div>' . $form->select('Manufacturer.id', $manufacturers, $selected, array('empty' => false)) . '</div>';
?>
<?php echo $form->submit('zobrazit produkty') ?>
<?php echo $form->end() ?>