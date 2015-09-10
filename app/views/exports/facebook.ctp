<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
		<title>SportNutrition Vávra</title>
		<link>http://www.sportnutrition.cz</link>
		
	<? foreach ( $products as $product ){ ?>
		<item>
			<g:id>CZ_<?php echo $product['Product']['id']?></g:id>
			<g:title><![CDATA[<?php echo $product['Product']['name']?>]]></g:title>
			<g:description><![CDATA[<?php echo $product['Product']['short_description']?>]]></g:description>
			<g:link><![CDATA[http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?>]]></g:link>
			<g:image_link><![CDATA[http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?>]]></g:image_link>
			<g:brand><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></g:brand>
			<g:condition>new</g:condition>
			<g:availability>in stock</g:availability>
			<g:price><?php echo $product['Product']['price']?> CZK</g:price>
			
			<g:google_product_category><![CDATA[<?php echo $product['Product']['category_text']?>]]></g:google_product_category>
			
<?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) { ?>
			<g:gtin><![CDATA[<?php echo $product['Product']['ean']?>]]></g:gtin>
<?php } ?>

<?php foreach ($product['shippings'] as $shipping) { ?>
			<g:shipping>
				<g:country><![CDATA[CZ]]></g:country>
				<g:service><![CDATA[<?php echo $shipping['name']?>]]></g:service>
				<g:price><![CDATA[<?php echo $shipping['price']?> CZK]]></g:price>	
			</g:shipping>
<?php } ?>
		</item>
	<? } ?>
	</channel>
</rss>