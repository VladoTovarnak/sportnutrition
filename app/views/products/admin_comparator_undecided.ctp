<h1>Nastavení nerozhodnutých produktů pro <?php echo $comparator['Comparator']['name']?></h1>
<?php echo $this->Form->create('ComparatorProductClickPrice', array('url' => array('controller' => 'products', 'action' => 'comparator_undecided', $comparator['Comparator']['id'])))?>
<table class="tabulka">
	<?php foreach ($this->data['ComparatorProductClickPrice'] as $index => $product) {?>
	<tr>
		<th><?php
			echo $this->Html->link($product['Product']['name'], '/' . $product['Product']['url'], array('target' => 'blank'));
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.Product.id');
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.Product.name');
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.Product.url');
		?></th>
		<td><?php
			if (isset($product['ComparatorProductClickPrice']['id'])) {
				echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.ComparatorProductClickPrice.id');
			}
			echo $this->Form->input('ComparatorProductClickPrice.' . $index . '.ComparatorProductClickPrice.feed', array('label' => false, 'type' => 'radio', 'options' => array(-1 => 'Zakázáno', 0 => 'Nerozhodnuto', 1 => 'Povoleno'), 'legend' => false, 'value' => 0));
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.ComparatorProductClickPrice.product_id', array('value' => $product['Product']['id']));
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $index . '.ComparatorProductClickPrice.comparator_id', array('value' => $comparator['Comparator']['id']));
		?></td>
	<?php } ?>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>