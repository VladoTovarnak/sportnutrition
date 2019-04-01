<?php

foreach ($products as $product) { 
	$name = $product['Product']['heureka_name'];
	if (empty($name)) {
		$name = $product['Product']['name'];
	}
?>
	<SHOPITEM>
		<ITEM_ID><![CDATA[<?php echo $product['Product']['id'] ?>]]></ITEM_ID>
		<PRODUCT><![CDATA[<?php echo $name ?>]]></PRODUCT>
        <PRODUCTNAME><![CDATA[<?php echo $name ?>]]></PRODUCTNAME>
		<DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description']?>]]></DESCRIPTION>
		<URL><![CDATA[http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>]]></URL>
		<IMGURL><![CDATA[http://www.<?php echo CUST_ROOT ?>/product-images/<?php echo (empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?>]]></IMGURL>
		<PRICE><![CDATA[<?php echo ceil($product['Product']['price'] * 100 / ($product['TaxClass']['value'] + 100)) ?>]]></PRICE>
		<PRICE_VAT><![CDATA[<?php echo $product['Product']['price']?>]]></PRICE_VAT>
		<VAT><![CDATA[<?php echo str_replace('.', ',', $product['TaxClass']['value']) ?>%]]></VAT>
		<MANUFACTURER><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></MANUFACTURER>
		<ITEM_TYPE><![CDATA[new]]></ITEM_TYPE>
		<CATEGORYTEXT><![CDATA[<?php echo $product['CATEGORYTEXT'] ?>]]></CATEGORYTEXT>
		<DELIVERY_DATE><![CDATA[0]]></DELIVERY_DATE>
<?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) { ?>
		<EAN><![CDATA[<?php echo $product['Product']['ean']?>]]></EAN>
<?php } ?>
<?php if (isset($product['ComparatorProductClickPrice']['click_price']) && !empty($product['ComparatorProductClickPrice']['click_price']) && $product['ComparatorProductClickPrice']['click_price'] != 0) { ?>
		<HEUREKA_CPC><?php echo number_format($product['ComparatorProductClickPrice']['click_price'], 2, ',', '')?></HEUREKA_CPC>
<?php } ?>
<?php foreach ($product['shippings'] as $shipping) { ?>
		<DELIVERY>
			<DELIVERY_ID><![CDATA[<?php echo $shipping['name']?>]]></DELIVERY_ID>
			<DELIVERY_PRICE><![CDATA[<?php echo $shipping['price']?>]]></DELIVERY_PRICE>	
		</DELIVERY>
<?php } ?>
	</SHOPITEM>
<?php
} ?>