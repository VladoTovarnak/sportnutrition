<?php
	foreach ( $products as $product ){
?>

	<div class="productBox">
		<h2><?php echo $html->link($product['Product']['name'], '/' . $product['Product']['url'], array('escape' => false), false);?></h2>
		<div class="imageBox">
			<div class="topShadow"></div>
			<div class="shadow">
				<table>
					<tr>
						<td align="center" valign="middle" style="height:100px;width:114px;">
				<?php
if (!empty($product['Product']['Image'])) {
					// zjistim hlavni obrazek
					$main_image = $product['Product']['Image'][0];
					foreach ($product['Product']['Image'] as $image) {
						if ($image['is_main']) {
							$main_image = $image;
							break;
						}
					}
					?>

				<a href="/<?php echo $product['Product']['url']?>"><img src="/product-images/small/<?php echo $main_image['name']?>" alt="" /></a>

				<?php
					} else {
				?>
					<img src="/product-images/small/na.gif" alt="" />
				<?php
					}
				?>

						</td>
					</tr>
				</table>

			</div>
			<div class="bottomShadow"></div>
			<?php
				if ( $product['Product']['discount_price'] < $product['Product']['retail_price_with_dph'] ){
			?>
					<span class="old_price"><?php echo $product['Product']['retail_price_with_dph']?> Kč</span><br />
           			<span class="price"><?php echo $product['Product']['discount_price']?> Kč</span>
           			<?php
           				if ( !$session->check('Customer.id') ){
           			?>
							<br /><span style="font-size:9px;"><a href="/slevy-sportovni-vyziva.htm" style="font-size:9px;">chcete lepší cenu?</a></span>
           			<?php
           				}	
				} else {
			?>
					<span class="price"><?php echo $product['Product']['retail_price_with_dph']?> Kč</span>
			<?php
				}
			?>
		</div>

		<div class="detailBox">
			<div class="manufacturer">Výrobce: <?php echo $product['Product']['Manufacturer']['name']?></div>
		</div>
		<p><?php echo $product['Product']['short_description']?></p>
		<div class="buyBox">
				<?php

					if ( $product['Product']['Availability']['cart_allowed'] == '1' ){
				?>
						počet kusů
						<?php echo $form->Create(array('url' => array('controller' => 'products', 'action' => 'view', $product['Product']['id']), 'id' => 'addP' . $product['Product']['id']));?>
						<div style="display:inline">
							<input name="data[Product][quantity]" class="quantity" value="1" type="text" />
							<input style="display:inline" type="submit" class="buyNow" value="Koupit" />
							<input type="hidden" name="data[Product][id]" value="<?php echo $product['Product']['id']?>" />
						</div>
						<?php echo $form->end() ?>
				<?php
					} else {
						echo '<span>Produkt nyní nelze objednat kvůli nedostupnosti.</span>';
					}
				?>
			<a href="/<?php echo $product['Product']['url']?>">Zobrazit detaily</a>
		</div>						
		<div style="clear:both"></div>
	</div>
<?php
	}
?>