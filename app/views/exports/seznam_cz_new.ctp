<?php
foreach ( $products as $product ) { ?>
	<?php
	// priprava pole pro nazev produktu pro zbozi.cz
	// pokud nema produkt specialne nastavene jmeno
	// pro zbozi.cz, pouzije se defaultni jmeno produktu
	$zbozi_name = $product['Product']['zbozi_name'];
	if ( empty( $zbozi_name ) ) {
		$zbozi_name = $product['Product']['name'];
	}

	?>
	<?php if ( ! empty( $product['Subproduct'] ) ) {
		foreach ( $product['Subproduct'] as $subproduct ) {
			$subproduct_name = array();
			foreach ( $subproduct['AttributesSubproduct'] as $as ) {
				$zbozi_name = $product['Product']['name'] . " " . strtolower( $as['Attribute']['value'] );
				?>
                <SHOPITEM>
                    <ITEM_ID><?php echo $product['Product']['id'] . "-" . $as['Attribute']['id'] ?></ITEM_ID>
                    <PRODUCTNAME><![CDATA[<?php echo $zbozi_name ?>]]></PRODUCTNAME>
                    <PRODUCT><![CDATA[<?php echo $zbozi_name ?>]]></PRODUCT>
                    <DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description'] ?>]]></DESCRIPTION>
                    <CATEGORYTEXT><?php // @TODO - doplnit naparovani na kategorie zbozi.cz
						?></CATEGORYTEXT>

					<?php if ( isset( $product['Product']['ean'] ) && ! empty( $product['Product']['ean'] ) ) { ?>
                        <EAN><![CDATA[<?php echo $product['Product']['ean'] ?>]]></EAN>
					<?php } ?>

                    <MANUFACTURER><?php echo $product['Manufacturer']['name'] ?></MANUFACTURER>
                    <URL>https://www.<?php echo CUST_ROOT ?>/<?php echo $product['Product']['url'] ?></URL>
					<?php
					// vychozi dostupnost produktu je ihned
					$availability = 0;

					// dostupnost do tydne
					if ( $product['Availability']['id'] == 4 ) {
						$availability = 5;
					}
					?>
                    <DELIVERY_DATE><![CDATA[<?php echo $availability ?>]]></DELIVERY_DATE>
					<?php
					// pokud ma produkt cenu 2000 a vic ma dopravu
					// zdarma, tak to hodim do extra_message
					if ( $product['Product']['price'] > 1999 ) {
						?>
                        <EXTRA_MESSAGE>free_delivery</EXTRA_MESSAGE>
						<?php
					}
					?>
                    <PARAM>
                    <PARAM_NAME><![CDATA[<?php echo strtolower( $as['Attribute']['Option']['name'] ) ?>]]></PARAM_NAME>
                    <VAL><![CDATA[<?php echo strtolower( $as['Attribute']['value'] ) ?>]]></VAL>
                    </PARAM>

					<?php if ( file_exists( 'product-images/' . $product['Image']['name'] ) ) { ?>
                        <IMGURL>https://www.<?php echo CUST_ROOT ?>
                            /product-images/<?php echo( empty( $product['Image']['name'] ) ? '' : str_replace( " ", "%20", $product['Image']['name'] ) ) ?></IMGURL>
					<?php } ?>

                    <PRICE_VAT><?php echo $product['Product']['price'] ?></PRICE_VAT>

					<?php if ( isset( $product['ComparatorProductClickPrice']['click_price'] ) && ! empty( $product['ComparatorProductClickPrice']['click_price'] ) && $product['ComparatorProductClickPrice']['click_price'] != 0 ) { ?>
                        <MAX_CPC><?php echo number_format( $product['ComparatorProductClickPrice']['click_price'], 2, '.', '' ) ?></MAX_CPC>
                        <MAX_CPC_SEARCH><?php echo number_format( $product['ComparatorProductClickPrice']['click_price'], 2, '.', '' ) ?></MAX_CPC_SEARCH>
					<?php } ?>
                </SHOPITEM>
				<?php
			}
		}
	} else { ?>
        <SHOPITEM>
            <ITEM_ID><?php echo $product['Product']['id'] ?></ITEM_ID>
            <PRODUCTNAME><![CDATA[<?php echo $zbozi_name ?>]]></PRODUCTNAME>
            <PRODUCT><![CDATA[<?php echo $zbozi_name ?>]]></PRODUCT>
            <DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description'] ?>]]></DESCRIPTION>
            <CATEGORYTEXT><?php // @TODO - doplnit naparovani na kategorie zbozi.cz
				?></CATEGORYTEXT>

			<?php if ( isset( $product['Product']['ean'] ) && ! empty( $product['Product']['ean'] ) ) { ?>
                <EAN><![CDATA[<?php echo $product['Product']['ean'] ?>]]></EAN>
			<?php } ?>

            <MANUFACTURER><?php echo $product['Manufacturer']['name'] ?></MANUFACTURER>
            <URL>https://www.<?php echo CUST_ROOT ?>/<?php echo $product['Product']['url'] ?></URL>
			<?php
			// vychozi dostupnost produktu je ihned
			$availability = 0;

			// dostupnost do tydne
			if ( $product['Availability']['id'] == 4 ) {
				$availability = 5;
			}
			?>
            <DELIVERY_DATE><![CDATA[<?php echo $availability ?>]]></DELIVERY_DATE>
			<?php
			// pokud ma produkt cenu 2000 a vic ma dopravu
			// zdarma, tak to hodim do extra_message
			if ( $product['Product']['price'] > 1999 ) {
				?>
                <EXTRA_MESSAGE>free_delivery</EXTRA_MESSAGE>
				<?php
			}
			?>
			<?php if ( file_exists( 'product-images/' . $product['Image']['name'] ) ) { ?>
                <IMGURL>https://www.<?php echo CUST_ROOT ?>
                    /product-images/<?php echo( empty( $product['Image']['name'] ) ? '' : str_replace( " ", "%20", $product['Image']['name'] ) ) ?></IMGURL>
			<?php } ?>

            <PRICE_VAT><?php echo $product['Product']['price'] ?></PRICE_VAT>

			<?php if ( isset( $product['ComparatorProductClickPrice']['click_price'] ) && ! empty( $product['ComparatorProductClickPrice']['click_price'] ) && $product['ComparatorProductClickPrice']['click_price'] != 0 ) { ?>
                <MAX_CPC><?php echo number_format( $product['ComparatorProductClickPrice']['click_price'], 2, '.', '' ) ?></MAX_CPC>
                <MAX_CPC_SEARCH><?php echo number_format( $product['ComparatorProductClickPrice']['click_price'], 2, '.', '' ) ?></MAX_CPC_SEARCH>
			<?php } ?>
        </SHOPITEM>
	<?php }
}
?>