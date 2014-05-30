<? foreach ( $products as $product ){ ?>
	<SHOPITEM>
<?php 
$zbozi_name = $product['Product']['zbozi_name'];
if (empty($zbozi_name)) {
	$zbozi_name = $product['Product']['name'];
}
?>
		<PRODUCT><?=$zbozi_name?></PRODUCT>
		<DESCRIPTION><?=$product['Product']['short_description']?></DESCRIPTION>
		<URL>http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?></URL>
<?php // vychozi dostupnost produktu je ihned
	$availability = 0;
	// dostupnost do tydne
	if ($product['Availability']['id'] == 4) {
		$availability = 5;
	}
?>
		<DELIVERY_DATE><?php echo $availability?></DELIVERY_DATE>
<?php if (file_exists('product-images/' . $product['Image']['name'])) { ?>
		<IMGURL>http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?></IMGURL>
<?php } ?>
		<PRICE_VAT><?=$product['Product']['price']?></PRICE_VAT>
<?php // vyber polozek do detailu na firm.cz
	if (in_array($product['Product']['id'], $firmy_cz_products)) { ?>
		<FIRMY_CZ>1</FIRMY_CZ>
<?php } ?>
	</SHOPITEM>
<? } ?>