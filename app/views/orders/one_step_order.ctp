<h2><span>Nákupní košík</span></h2>
<? if (empty($cart_products)) { ?>
	<p>Nákupní košík je prázdný.</p>
<? } else { ?>
	<table id="cartContents" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:60%">Název produktu</th>
			<th style="width:16%">Množství</th>
			<th style="width:9%" nowrap>Cena za kus</th>
			<th style="width:9%" nowrap>Cena celkem</th>
			<th style="width:6%">&nbsp;</th>
		</tr>
<?
		$final_price = 0;
		foreach ( $cart_products as $cart_product ){
			$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'];
			
			$image = '/img/na_small.jpg';
			if (isset($cart_product['Product']['Image']) && !empty($cart_product['Product']['Image'])) {
				$path = 'product-images/small/' . $cart_product['Product']['Image'][0]['name'];
				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
				}
			}
?>
		<tr>
			<td style="position:relative">
				<div class="image_holder">
					<a href="/<?php echo $cart_product['Product']['url']?>">
						<img src="<?php echo $image?>" alt="Obrázek <?php $cart_product['Product']['name']?>" width="45px" />
					</a>
				</div>
				<div class="cart-product-info">
					<a href="/<?php echo $cart_product['Product']['url'] ?>"><?php echo $cart_product['Product']['name'] ?></a>
<?php 	if ( !empty($cart_product['CartsProduct']['product_attributes']) ){ ?>
					<br />
					<div style="font-size:11px;padding-left:20px;">
<?php 		foreach ($cart_product['CartsProduct']['product_attributes'] as $option => $value) { ?>
						<strong><?php echo $option ?></strong>: <?php echo $value ?><br />
<?php 		} ?>
					</div>
<?php 	} ?>
				</div>
			</td>
			<td align="right"><?php 
				echo $this->Form->Create('CartsProduct', array('url' => array('controller' => 'orders', 'action' => 'one_step_order')));
				echo $this->Form->hidden('Order.action', array('value' => 'cart_edit'));
				echo $this->Form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id']));
				echo $this->Form->input('CartsProduct.quantity', array('label' => false, 'size' => 1, 'value' => $cart_product['CartsProduct']['quantity'], 'div' => false, 'class' => 'small')) . '&nbsp;ks&nbsp;&nbsp;';
				echo $this->Form->submit('Upravit', array('class' => 'changeAmount small', 'div' => false));
				echo $this->Form->end();
			?></td>
			<td align="right"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph']) ?></span>&nbsp;Kč</td>
			<td align="right"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) ?></span>&nbsp;Kč</td>
			<td align="right"><?php 
				echo $this->Html->link('smazat', array('controller' => 'carts_products', 'action' => 'delete', $cart_product['CartsProduct']['id'], 'back' => base64_encode($_SERVER['REQUEST_URI'])), array('title' => 'odstranit z košíku'), 'Opravdu chcete produkt odstranit z košíku?');
			?></td>
		</tr>
<?php	} ?>
		<tr>
			<th colspan="2" align="right">cena za zboží celkem:</th>
			<td colspan="2" align="right"><strong><span class="final-price"><?php echo intval($final_price) ?></span> Kč</strong></td>
			<td>&nbsp;</td>
		</tr>
	</table>
<? } ?>

<?php echo $this->Html->link('Zpět na nabídku', '/', array('style' => 'float:left'))?>
<?php echo $this->Html->link('Přejít k objednání', '#OrderDetails', array('style' => 'float:right'))?>
<div class="clearer"></div>
<h2 id="OrderDetails"><span>Objednávka</span></h2>
<?php if (!$is_logged_in) { ?>
<ul style="list-style-type:none">
	<li>
		<input type="radio" name="customer-is-registered" value="1" id="CustomerIsRegistered1" /> Přihlásit se, jsem již zaregistrován
		<div id="CustomerOneStepOrderDiv" class="neukazovat">
			<?=$form->Create('Customer', array('url' => array('controller' => 'orders', 'action' => 'one_step_order')));?>
			<table id="orderForm">
				<tr>
					<th>Login:</th>
					<td><?=$form->text('Customer.login', array('class' => 'content'))?></td>
				</tr>
				<tr>
					<th>Heslo:</th>
					<td><?=$form->password('Customer.password', array('class' => 'content'))?></td>
				</tr>
			</table>
			<?php echo $this->Form->hidden('Order.action', array('value' => 'customer_login'))?>
			<?php echo $this->Form->submit('Přihlásit')?>
			<?php echo $this->Form->end()?>
			<?=$html->link('zapomněl(a) jsem heslo', array('controller' => 'customers', 'action' => 'password')) ?>
		</div>
	</li>
	<li><input type="radio" name="customer-is-registered" value="0" id="CustomerIsRegistered0" /> Toto je moje první objednávka</li>
</ul>
<?php } ?>

<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'one_step_order')))?>
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
<?php echo $this->Form->input('Order.comments', array('label' => false, 'class' => 'content'))?>
<!-- INFORMACE O ZAKAZNIKOVI -->
<?php echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'order_personal_info'), 'id' => 'OrderDetailsCustomer'))?>
<h3>Informace o zákazníkovi</h3>
<table>
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.first_name', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.last_name', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.phone', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Email<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.email', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Firma</th>
		<td><?php echo $this->Form->input('Customer.company_name', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>IČ</th>
		<td><?php echo $this->Form->input('Customer.ico', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $this->Form->input('Customer.dic', array('label' => false, 'class' => 'content'))?></td>
	</tr>
</table>
<h3>Fakturační adresa</h3>
<table id="InvoiceAddressTable">
	<tr>
		<th>Ulice<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.0.street', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Číslo popisné</th>
		<td><?php echo $this->Form->input('Address.0.street_no', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.0.city', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.0.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Stát</th>
		<td><?php echo $this->Form->input('Address.0.state', array('label' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika', 'Slovensko' => 'Slovensko'))); ?></td>
	</tr>
</table>

<h3>Doručovací adresa</h3>
<?php echo $this->Form->input('Customer.is_delivery_address_different', array('label' => 'Dodací adresa není stejná jako fakturační', 'type' => 'checkbox', 'id' => 'isDifferentAddressCheckbox'))?>

<?php 
	$class = ' class="neukazovat"';
	if (isset($this->data['Customer']) && array_key_exists('is_delivery_address_different', $this->data['Customer']) && $this->data['Customer']['is_delivery_address_different']) {
		$class = '';
	}
?>
<table id="DeliveryAddressTable"<?php echo $class?>>
	<tr>
		<th>Ulice<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.1.street', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>Číslo popisné</th>
		<td><?php echo $this->Form->input('Address.1.street_no', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.1.city', array('label' => false, 'class' => 'content'))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('Address.1.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Stát</th>
		<td><?php echo $this->Form->input('Address.1.state', array('label' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika', 'Slovensko' => 'Slovensko'))); ?></td>
	</tr>
</table>

<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'f')));
	echo $this->Form->hidden('Address.1.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'd')));
	
	echo $this->Form->hidden('Order.action', array('value' => 'order_finish'));

	echo $this->Form->submit('Objednat');
	echo $this->Form->end();
?>
