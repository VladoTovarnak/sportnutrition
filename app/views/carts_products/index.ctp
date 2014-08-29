<h2><span><?php echo $page_heading?></span></h2>
<p>Na této stránce můžete zkontrolovat obsah Vašeho nákupního košíku.<br />
Chcete-li dokončit objednávku a <a href="/orders/add">zaplatit</a>, klikněte <a href="/orders/add">zde</a>.</p>

<h3><span>Seznam produktů v nákupním košíku</span></h3>
<? if ( empty($cart_products) ){ ?>
	<p>V košíku nemáte žádné zboží.</p>
<? } else { ?>
	<table id="cartContents" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:50%">Název produktu</th>
			<th style="width:30%">Množství</th>
			<th>Cena za kus</th>
			<th>Cena celkem</th>
			<th>&nbsp;</th>
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
				<div style="position:absolute; top:30%; left: 55px; height:60px;">
					<a href="/<?php echo $cart_product['Product']['url'] ?>"><?php echo $cart_product['Product']['name'] ?></a>
<?php 	if ( !empty($cart_product['CartsProduct']['product_attributes']) ){ ?>
					<br />
					<div style="font-size:11px;padding-left:20px;">
<?php 		foreach ( $cart_product['CartsProduct']['product_attributes'] as $option => $value ){ ?>
						<strong><?php echo $option ?></strong>: <?php echo $value ?><br />
<?php 		} ?>
					</div>
<?php 	} ?>
				</div>
			</td>
			<td class="quantity"><?php 
				echo $form->Create('CartsProduct', array('url' => array('action' => 'edit', $cart_product['CartsProduct']['id'])));
				echo $form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id']));
				echo $this->Form->input('CartsProduct.quantity', array('label' => false, 'size' => 1, 'value' => $cart_product['CartsProduct']['quantity'], 'div' => false, 'class' => 'small')) . '&nbsp;ks';
				echo $form->Submit('Upravit', array('class' => 'changeAmount small', 'div' => false));
				echo $form->end();
			?></td>
			<td><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph']) ?></span>&nbsp;Kč</td>
			<td><span class="price"><?php echo intval( $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'] ) ?></span>&nbsp;Kč</td>
			<td><?php 
				echo $this->Html->link('smazat', array('controller' => 'carts_products', 'action' => 'delete', $cart_product['CartsProduct']['id']), array('title' => 'odstranit z košíku'), 'Opravdu chcete produkt odstranit z košíku?');
			?></td>
		</tr>
<?php	} ?>
		<tr>
			<th colspan="2" align="right">cena za zboží celkem:</td>
			<td colspan="3" align="center"><strong><span class="price"><?php echo intval($final_price) ?></span> Kč</strong></td>
		</tr>
		<tr>
			<td colspan="5" align="right">
				<?php echo $this->Html->link('>> Krok 1/4: Vložení osobních údajů', array('controller' => 'customers', 'action' => 'order_personal_info'), array('id' => 'orderAndPay'))?>
			</td>
		</tr>
	</table>
<? } ?>