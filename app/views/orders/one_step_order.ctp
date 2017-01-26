<h2 id="ShoppingCart">Nákupní košík</h2>
<?php 
// flash, pokud je chyba ve formu pro prihlaseni
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'shopping_cart') {
		echo $this->Session->flash();
	}
}
?>
<? if (empty($cart_products)) { ?>
	<p class="empty_cart">Nákupní košík zatím zeje prázdnotou. Vložte produkty, které chcete objednat, do košíku.</p>
	<?php echo $this->Html->link('Zpět do obchodu', $back_shop_url, array('class' => 'button_like_link red'))?>
<? } else { ?>
	<table class="cart-contents" cellpadding="0" cellspacing="0">
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
<?php 	if (!empty($cart_product['CartsProduct']['product_attributes'])) { ?>
					<br />
					<div style="font-size:11px;padding-left:20px;">
<?php 		foreach ($cart_product['CartsProduct']['product_attributes'] as $option => $value) {
				/* u produktu chci mit moznost v urcitych kampanich nevybirat prichute ani nic jineho
				 * a vlozit produkt rovnou do kosiku, napr. z vypisu kategorii
				 * pokud mezi atributy najdu 'nezvoleno', tak atribut do frontu zakazikovi
				 * nebudu ukazovat - pozn. 'nezvoleno' ma ID: 1934 */
				if ( $value == 'nezvoleno' ){
					continue;
				}
?>
						<strong><?php echo $option ?></strong>: <?php echo $value ?><br />
<?php 		} ?>
					</div>
<?php 	} ?>
				</div>
			</td>
			<td align="right"><?php 
				echo $this->Form->Create('CartsProduct', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#ShoppingCart'), 'encoding' => false));
				echo $this->Form->hidden('Order.action', array('value' => 'cart_edit'));
				echo $this->Form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id']));
				echo $this->Form->input('CartsProduct.quantity', array('label' => false, 'size' => 1, 'value' => $cart_product['CartsProduct']['quantity'], 'div' => false, 'class' => 'small')) . '&nbsp;ks&nbsp;&nbsp;';
				echo $this->Form->submit('Upravit', array('class' => 'changeAmount small', 'div' => false));
				echo $this->Form->end();
			?></td>
			<td align="right"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph']) ?></span>&nbsp;Kč</td>
			<td align="right"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) ?></span>&nbsp;Kč</td>
			<td align="right"><?php 
				echo $this->Html->link('odebrat z košíku', array('controller' => 'carts_products', 'action' => 'delete', $cart_product['CartsProduct']['id'], 'back' => base64_encode($_SERVER['REQUEST_URI'] . '#ShoppingCart')), array('title' => 'odstranit z košíku', 'class' => 'red_alert'), 'Opravdu chcete produkt odstranit z košíku?');
			?></td>
		</tr>
<?php	} ?>

<?php 
		if ($this->Session->check('Discount')) {
			$discount = $this->Session->read('Discount');
			if ( $discount == "dpr999" ){
?>
				<tr>
					<th colspan="5">Uplatněné slevové kupóny</th>
				</tr>
				<tr>
					<td><strong>dpr999</strong> - Sleva na dopravu pro objednávku nad 999 Kč.</td>
					<td colspan="4" style="font-size: 14px;">(sleva bude uplatněna je-li v košíku zboží za 1000 Kč a více)</td>
				</tr>
<?php
			}
		}
?>

		<?php echo $this->element(REDESIGN_PATH . 'one_step_order_table_footer', array('final_price' => $final_price, 'shipping_price' => $shipping_price, 'type' => 'cart_summary'))?>
	</table>

<!-- 

<div class="discount_coupon">
	<?php
		echo $form->Create('Customer', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#ShoppingCart'), 'encoding' => false));
		echo $this->Form->hidden('Order.action', array('value' => 'apply_discount'));
	?>
	 	<span>Uplatnit slevový kupon:</span>
		<input name="data[Discount][validator]" type="text" class="discount_code_field" maxlength="80" id="DiscountValidate">
		<input class="verify_discount_code" type="submit" value="OVĚŘIT PLATNOST">
	<?php 
		echo $this->Form->end();
	?>
</div>

 -->

<?php echo $this->Html->link('Přejít k objednání', '#OrderDetails', array('class' => 'button_like_link red'))?>&nbsp;
<?php echo $this->Html->link('Zpět do obchodu', $back_shop_url, array('class' => 'button_like_link silver'))?>

<div class="clearer"></div>
<h2 id="OrderDetails">Objednávka</h2>
<?php 
// flash, pokud je chyba ve formu pro prihlaseni
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'customer_login') {
		echo $this->Session->flash();
	}
}
?>
<?php if (!$is_logged_in) { ?>
<ul style="list-style-type:none">
	<li>
		<?php
			$value = 0;
			$checked = '';
			if (isset($this->data['Customer']['is_registered']) && $this->data['Customer']['is_registered'] == $value) {
				$checked = 'checked="checked"';
			}
		?>
		<input type="radio" class="customer-is-registered" value="<?php echo $value?>" id="CustomerIsRegistered0" name="data[Customer][is_registered]" <?php echo $checked?>/>
			<label for="CustomerIsRegistered0" class="customer_status_label">Toto je moje první objednávka</label>
	</li>
	<li>
		<?php
			$value = 1;
			$checked = '';
			if (isset($this->data['Customer']['is_registered']) && $this->data['Customer']['is_registered'] == $value) {
				$checked = 'checked="checked"';
			}
		?>
		<input type="radio" class="customer-is-registered" value="1" id="CustomerIsRegistered1" name="data[Customer][is_registered]" <?php echo $checked?>/>
			<label for="CustomerIsRegistered1" class="customer_status_label">Přihlásit se, jsem již zaregistrován</label>
			<div id="CustomerOneStepOrderDiv" class="neukazovat">
				<?=$form->Create('Customer', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#OrderDetails'), 'encoding' => false));?>
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
</ul>
<?php } ?>

<h3 id="ShippingInfo"><span>&rarr;</span>&nbsp;Vyberte: způsob dopravy</h3>
<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#CustomerInfo'), 'encoding' => false))?>
<?php 
// flash, pokud je chyba ve formu pro udaje o zakaznikovi
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'shipping_info') {
		echo $this->Session->flash();
	}
}
?>
<?php if (!empty($shippings)) { ?>
<table id="ShippingChoiceTable">
<?php
	$first = true;
	foreach ($shippings as $shipping) { 
		$checked = '';
		if (isset($this->data['Order']['shipping_id']) && $this->data['Order']['shipping_id'] == $shipping['Shipping']['id']) {
			$checked = ' checked="checked"';
		}
?>
	<tr>
		<td><input name="data[Order][shipping_id]" type="radio" value="<?php echo $shipping['Shipping']['id']?>" id="OrderShippingId<?php echo $shipping['Shipping']['id']?>"<?php echo $checked?>/></td>
		<td><?php
		$shipping_info = $shipping['Shipping']['name'];
		// pokud mam dopravu na postu, chci vykreslit link pro vyber pobocky
		if ($shipping['Shipping']['id'] == ON_POST_SHIPPING_ID) {
			$shipping_info .= ' - ' . $this->Html->link('vyberte pobočku', '#', array('id' => 'PostOfficeChoiceLink'));
		} elseif ($shipping['Shipping']['id'] == BALIKOMAT_SHIPPING_ID) {
			$shipping_info .= ' - ' . $this->Html->link('vyberte pobočku', '#', array('id' => 'BalikomatChoiceLink'));
		}
		echo $shipping_info
		?></td>
		<td><small><?php echo $shipping['Shipping']['description']?></small></td>
		<td><?php echo round($shipping['Shipping']['price'])?>&nbsp;Kč</td> 
	</tr>
<?php	$first = false; 
	} ?>
</table>
<?php } ?>

<h3 id="PaymentInfo"><span>&rarr;</span>&nbsp;Vyberte: způsob platby</h3>
<?php if (!empty($payments)) { ?>
<table id="PaymentChoiceTable">
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

<h3><span>&rarr;</span>&nbsp;vzkaz nebo přání k objednávce</h3>
<?php echo $this->Form->input('Order.comments', array('label' => false, 'class' => 'content'))?>
<!-- INFORMACE O ZAKAZNIKOVI -->
<h3 id="CustomerInfo"><span>&rarr;</span>&nbsp;vyplňte: Informace o vás</h3>
<?php 
// flash, pokud je chyba ve formu pro udaje o zakaznikovi
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'customer_info') {
		echo $this->Session->flash();
	}
}
?>

<table class="customer_info_form">
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

<div id="InvoiceAddressBox">
	<h3 id="InvoiceAddressInfo"><span>&rarr;</span>&nbsp;Fakturační adresa</h3>
	<table id="InvoiceAddressTable"  class="customer_info_form">
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
</div>

<div id="DeliveryAddressBox">
	<h3 id="DeliveryAddressInfo"><span>&rarr;</span>&nbsp;Doručovací adresa</h3>
	<?php echo $this->Form->input('Customer.is_delivery_address_different', array('label' => 'Chci vyplnit jinou adresu doručení než je fakturační', 'type' => 'checkbox', 'id' => 'isDifferentAddressCheckbox'))?>
	
	<?php 
		$class = ' class="neukazovat customer_info_form"';
		if (isset($this->data['Customer']) && array_key_exists('is_delivery_address_different', $this->data['Customer']) && $this->data['Customer']['is_delivery_address_different']) {
			$class = ' class="customer_info_form"';
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
</div>
<br/>
<h3><span>&rarr;</span>&nbsp;Rekapitulace ceny objednávky</h3>
<table class="cart-contents" cellpadding="0" cellspacing="0">
<?php echo $this->element(REDESIGN_PATH . 'one_step_order_table_footer', array('final_price' => $final_price, 'shipping_price' => $shipping_price, 'type' => 'bottom_summary'))?>
</table>

<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'f')));
	echo $this->Form->hidden('Address.1.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'd')));
	
	echo $this->Form->hidden('Order.action', array('value' => 'order_finish'));

	echo $this->Form->submit('ODESLAT OBJEDNÁVKU', array('class' => 'finish_order_button', 'onclick' => "fbq('track', 'AddPaymentInfo');"));
	echo $this->Form->end();

} // konec nakupni kosik neni prazdny
?>
<div id="PostOfficeChoice" class="neukazovat">
<h2>Vybrat pobočku</h2>
<p>Zadejte název nebo PSČ obce, ve které si přejete zásilku vyzvednout na pobočce ČP.</p>
<p class="no-input neukazovat red_alert">Zadejte PSČ nebo město</p>
<p class="empty-output neukazovat red_alert">Zadanému PSČ nebo městu neodpovídá žádná pošta</p>
<?php echo $this->Form->create('PostOffice', array('url' => '#', 'id' => 'PostOfficeChoiceForm'));?>
<table class="content-table">
	<tr>
		<th>PSČ</th>
		<td><?php echo $this->Form->input('PostOffice.PSC', array('label' => false, 'div' => false)); ?></td>
		<th>Město</th>
		<td><?php echo $this->Form->input('PostOffice.NAZ_PROV', array('label' => false, 'div' => false, 'class' => 'content')); ?></td>
		<td><?php echo $this->Form->submit('Hledej', array('div' => false));?>
	</tr>
</table>
<?php echo $this->Form->end();?>
<div class="post-offices-list"></div>
</div>

<script type="text/javascript">
	fbq('track', 'InitiateCheckout');
</script>

<div class="modal"><span style="position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);text-align:center;font-size:20px;font-weight:bold">Mějte prosím moment strpení, Vaše objednávka se ukládá...<br/><br/><img src="/images/ajax-loader.gif" /></span></div>