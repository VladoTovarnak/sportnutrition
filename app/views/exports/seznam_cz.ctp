<?php
foreach ( $products as $product ){ ?>
	<SHOPITEM>
<?php 
$zbozi_name = $product['Product']['zbozi_name'];
if (empty($zbozi_name)) {
	$zbozi_name = $product['Product']['name'];
}
?>
		<PRODUCT><![CDATA[<?php echo $zbozi_name?>]]></PRODUCT>
		<DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description']?>]]></DESCRIPTION>
		<URL>http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?></URL>
<?php // vychozi dostupnost produktu je ihned
	$availability = 0;
	// dostupnost do tydne
	if ($product['Availability']['id'] == 4) {
		$availability = 5;
	}
?>
		<DELIVERY_DATE><![CDATA[<?php echo $availability?>]]></DELIVERY_DATE>
<?php if (file_exists('product-images/' . $product['Image']['name'])) { ?>
		<IMGURL>http://www.<?php echo CUST_ROOT ?>/product-images/<?php echo (empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?></IMGURL>
<?php } ?>
		<PRICE_VAT><?php echo $product['Product']['price']?></PRICE_VAT>
<?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) { ?>
		<EAN><![CDATA[<?php echo $product['Product']['ean']?>]]></EAN>
<?php } ?>
<?php if (isset($product['ComparatorProductClickPrice']['click_price']) && !empty($product['ComparatorProductClickPrice']['click_price']) && $product['ComparatorProductClickPrice']['click_price'] != 0) { ?>
		<MAX_CPC><?php echo number_format($product['ComparatorProductClickPrice']['click_price'], 2, '.', '')?></MAX_CPC>
<?php } ?>
<?php if (!empty($product['Subproduct'])) { 
		foreach ($product['Subproduct'] as $subproduct) { 
			$subproduct_name = array();
			foreach ($subproduct['AttributesSubproduct'] as $as) {
				$subproduct_name[] = $as['Attribute']['Option']['name'] . ': ' . $as['Attribute']['value'];
			}
			$subproduct_name = implode(', ', $subproduct_name);
			$subproduct_price = $product['Product']['price'] + $subproduct['price_with_dph'];
?>
		<VARIANT>
			<PRODUCTNAMEEXT><![CDATA[<?php echo $subproduct_name ?>]]></PRODUCTNAMEEXT>
			<PRICE_VAT><?php echo $subproduct_price?></PRICE_VAT>
		</VARIANT>
<?php 	}
	} ?>
	</SHOPITEM>
<?php
} ?>