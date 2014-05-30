<? foreach ( $products as $product ){ ?>
	<SHOPITEM>
		<PRODUCT><?php echo $product['Product']['name'] ?></PRODUCT>
		<DESCRIPTION><?php echo $product['Product']['short_description']?></DESCRIPTION>
		<URL>http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?></URL>
		<IMGURL>http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?></IMGURL>
		<PRICE><?php echo ceil($product['Product']['price'] * 100 / ($product['TaxClass']['value'] + 100)) ?></PRICE>
		<PRICE_VAT><?php echo $product['Product']['price']?></PRICE_VAT>
		<VAT><?php echo str_replace('.', ',', $product['TaxClass']['value'] / 100) ?></VAT>
		<MANUFACTURER><?php echo $product['Manufacturer']['name']?></MANUFACTURER>
		<ITEM_TYPE>new</ITEM_TYPE>
		<CATEGORYTEXT><?php echo $product['CATEGORYTEXT'] ?></CATEGORYTEXT>
		<DELIVERY_DATE>0</DELIVERY_DATE>
<?php foreach ($shippings as $shipping) { ?>
		<DELIVERY>
			<DELIVERY_ID><?php echo $shipping['Shipping']['heureka_id']?></DELIVERY_ID>
			<?php // pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
			$shipping_price = 0;
			if ($shipping['Shipping']['free'] && $product['Product']['price'] < $shipping['Shipping']['free']) {
				$shipping_price = ceil($shipping['Shipping']['price']);
			}
			?>
			<DELIVERY_PRICE><?php echo $shipping_price?></DELIVERY_PRICE>	
		</DELIVERY>
<?php } ?>
	</SHOPITEM>
<?
	}
?>