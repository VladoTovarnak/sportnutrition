<? foreach ( $products as $product ){ ?>
	<SHOPITEM>
		<PRODUCT><![CDATA[<?php echo $product['Product']['name'] ?>]]></PRODUCT>
		<DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description']?>]]></DESCRIPTION>
		<URL><![CDATA[http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?>]]></URL>
		<IMGURL><![CDATA[http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?>]]></IMGURL>
		<PRICE><![CDATA[<?php echo ceil($product['Product']['price'] * 100 / ($product['TaxClass']['value'] + 100)) ?>]]></PRICE>
		<PRICE_VAT><![CDATA[<?php echo $product['Product']['price']?>]]></PRICE_VAT>
		<VAT><![CDATA[<?php echo str_replace('.', ',', $product['TaxClass']['value'] / 100) ?>]]></VAT>
		<MANUFACTURER><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></MANUFACTURER>
		<ITEM_TYPE><![CDATA[new]]></ITEM_TYPE>
		<CATEGORYTEXT><![CDATA[<?php echo $product['CATEGORYTEXT'] ?>]]></CATEGORYTEXT>
		<DELIVERY_DATE><![CDATA[0]]></DELIVERY_DATE>
<?php foreach ($shippings as $shipping) { ?>
		<DELIVERY>
			<DELIVERY_ID><![CDATA[<?php echo $shipping['Shipping']['heureka_id']?>]]></DELIVERY_ID>
			<?php // pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
			$shipping_price = ceil($shipping['Shipping']['price']);
			if ($shipping['Shipping']['free'] != 0 && $product['Product']['price'] > $shipping['Shipping']['free']) {
				$shipping_price = 0;
			}
			?>
			<DELIVERY_PRICE><![CDATA[<?php echo $shipping_price?>]]></DELIVERY_PRICE>	
		</DELIVERY>
<?php } ?>
	</SHOPITEM>
<? } ?>