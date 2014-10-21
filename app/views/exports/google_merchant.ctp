<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
	<? foreach ( $products as $product ){ ?>
		<item>
			<g:id>CZ_<?php echo $product['Product']['id']?></g:id>
			<title><![CDATA[<?php echo $product['Product']['name']?>]]></title>
			<description><![CDATA[<?php echo $product['Product']['short_description']?>]]></description>
			<g:google_product_category><![CDATA[<?php echo $product['Product']['category_text']?>]]></g:google_product_category>
			<link><![CDATA[http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?>]]></link>
			<g:image_link><![CDATA[http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?>]]></g:image_link>
			<g:condition>new</g:condition>
			<g:identifier_exists>false</g:identifier_exists>
			<g:availability>in stock</g:availability>
			<g:price><![CDATA[<?php echo $product['Product']['price']?>]]> Kč</g:price>
			<g:brand><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></g:brand>
			<g:product_type><![CDATA[<?php echo $product['Product']['type_text']?>]]></g:product_type>
		</item>
	<? } ?>
	</channel>
</rss>