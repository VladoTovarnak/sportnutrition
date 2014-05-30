﻿<div class="mainContentWrapper">
<?=$form->Create('Order', array('url' => '/orders/shipping_edit'))?>
	<fieldset>
		<legend>Detaily objednávky</legend>
		<table id="orderForm">
			<tr>
				<th>Způsob doručení</th>
				<td>
					<?
						if ( !isset($this->data['Order']['shipping_id']) ){
							$this->data['Order']['shipping_id'] = null;
						}
						echo $form->select('Order.shipping_id', $shipping_choices, $this->data['Order']['shipping_id'], array('empty' => false));
					?><sup>*</sup>
				</td>
			</tr>
			<tr>
				<th>Způsob platby</th>
				<td>
					<?
						if ( !isset($this->data['Order']['payment_id']) ){
							$this->data['Order']['payment_id'] = null;
						}

						$delivery_choices = array(
							'1' => 'V hotovosti',
							'2' => 'Bankovním převodem'
						);
						echo $form->select('Order.payment_id', $delivery_choices, $this->data['Order']['payment_id'], array('empty' => false));
					?><sup>*</sup>
				</td>
			</tr>
			<tr>
				<th>Váš komentář k objednávce</th>
				<td>
					<?=$form->textarea('Order.comments', array('cols' => 40, 'rows' => 5))?>
				</td>
			</tr>
		</table>
	</fieldset>
		
		<table id="orderForm">
			<tr>
				<th>&nbsp;</th>
				<td><?=$form->Submit('Rekapitulace objednávky');?></td>
			</tr>
		</table>
<?$form->end()?>

</div>