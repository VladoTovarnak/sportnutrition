<h2><span>Doprava a způsob platby</span></h2>

<a id="orderAndPay" href="/customers/order_personal_info"><< Krok 1/4: Vložení osobních údajů</a>

<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'set_payment_and_shipping')))?>
<h3>Doprava</h3>
<?php if (!empty($shippings)) { ?>
<table>
<?php
	$first = true;
	foreach ($shippings as $shipping) { 
		$checked = '';
		if (isset($this->data['Order']['shipping_id']) && $this->data['Order']['shipping_id'] == $shipping['Shipping']['id']) {
			$checked = ' checked="checked"';
		}
		if (!isset($this->data['Order']['shipping_id']) && $first) {
			$checked = ' checked="checked"';
		}
?>
	<tr>
		<td><input name="data[Order][shipping_id]" type="radio" value="<?php echo $shipping['Shipping']['id']?>" id="OrderShippingId<?php echo $shipping['Shipping']['id']?>"<?php echo $checked?>/></td>
		<td><?php echo $shipping['Shipping']['name']?></td>
		<td><small><?php echo $shipping['Shipping']['description']?></small></td>
		<td><?php echo round($shipping['Shipping']['price'])?>&nbsp;Kč</td> 
	</tr>
<?php	$first = false; 
	} ?>
</table>
<?php } ?>

<h3>Platba</h3>
<?php if (!empty($payments)) { ?>
<table>
<?php
	$first = true;
	foreach ($payments as $payment) {
		$checked = '';
		if (isset($this->data['Order']['payment_id']) && $this->data['Order']['payment_id'] == $payment['Payment']['id']) {
			$checked = ' checked="checked"';
		}
		if (!isset($this->data['Order']['payment_id']) && $first) {
			$checked = ' checked="checked"';
		}
?>
	<tr>
		<td><input name="data[Order][payment_id]" type="radio" value="<?php echo $payment['Payment']['id']?>" id="OrderPaymentId<?php echo $payment['Payment']['id']?>"<?php echo $checked ?>/></td>
		<td><?php echo $payment['Payment']['name']?></td>
		<td><small><?php echo $payment['Payment']['description']?></small></td>
		<td>0&nbsp;Kč</td> 
	</tr>
<?php	$first = false; 
	} ?>
</table>
<?php } ?>

<h3>Poznámka k objednávce</h3>
<?php echo $this->Form->input('Order.comments', array('label' => false, 'cols' => 40, 'rows' => 5))?>

<?php echo $this->Form->submit('>> Krok 3/4: rekapitulace objednávky')?>
<?php echo $this->Form->end()?>