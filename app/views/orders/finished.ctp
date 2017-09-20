<div class="mainContentWrapper">
	<h2><span><?php echo $page_heading?></span></h2>
	<p class="finished_first">
		Vaše objednávka byla nyní <strong>přijata do našeho systému</strong>,<br />
		již brzy Vás budeme informovat o stavu Vaší objednávky.
	</p>

<?php
// jedna se o neprihlaseneho zakaznika, pro ktereho byl vytvoren ucet
	if (!empty($order['Customer']['login']) && !empty($order['Customer']['password'])) { ?>
		<p>Pro Vaše větší pohodlí jsme pro Vás vytvořili zákaznický účet. Pokud jste uvedl(a) v objednávce Vaši emailovou adresu,
		byly Vám odeslány tyto přihlašovací údaje:<br /><br />
		<strong>LOGIN: </strong><?php echo $order['Customer']['login']?><br />
		<strong>HESLO: </strong><?php echo $order['Customer']['password']?></p>
		<p>Doporučujeme Vám, <strong>poznamenat si tyto údaje</strong>, abyste mohl(a) plně využívat výhod Vašeho zákaznického účtu. Pro komunikaci
		emailem a telefonicky Vám stačí poznamenat si login.</p>
<?php
} else { ?>
		<p class="finished_second">
			Pomocí Vašeho <a href="/customers">uživatelského účtu</a><br />
		 	můžete kontrolovat stav Vaší objednávky.
		 </p>
<?php
} ?>
	<h3>Děkujeme za Vaši důvěru.</h3>
</div>

<?php 
	if (!defined('ISDEVELOPER')){
?>
	
		<!-- Měřicí kód Sklik.cz -->
		<iframe width="119" height="22" frameborder="0" scrolling="no" src="http://c.imedia.cz/checkConversion?c=100007593&color=ffffff&v=<?php echo $order['Order']['orderfinaltotal']?>"></iframe>
		<!-- Měřicí kód Zbozi.cz -->
		<iframe src="http://www.zbozi.cz/action/22378/conversion?chsum=o1dc7jMOpOK4TvZaW5rfwA==&price=<?php echo $order['Order']['orderfinaltotal']?>&uniqueId=<?php echo $order['Order']['id']?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:absolute; top:-3000px; left:-3000px; width:1px; height:1px; overflow:hidden;"></iframe>
		<iframe width="119" height="22" frameborder="0" scrolling="no" src="http://c.imedia.cz/checkConversion?c=100007593&color=ffffff&v=<?php echo $order['Order']['orderfinaltotal']?>"></iframe>
		<!-- Google Code for OBJEDNAVKA Conversion Page -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 960445286;
		var google_conversion_language = "cs";
		var google_conversion_format = "1";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "WRBaCOj6vlcQ5vb8yQM";
		var google_conversion_value = <?php echo $order['Order']['orderfinaltotal'] ?>;
		var google_conversion_currency = "CZK";
		var google_remarketing_only = false;
		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/960445286/?value=1.00&amp;currency_code=CZK&amp;label=WRBaCOj6vlcQ5vb8yQM&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		
		<!-- Merici kod pro Heureka.cz -->
		<script type="text/javascript">
		var _hrq = _hrq || [];
		    _hrq.push(['setKey', '2CE1ECA351EBBD47F06DF24414582CA0']);
		    _hrq.push(['setOrderId', '<?php echo $order['Order']['id'] ?>']);
		
		<?php foreach ($order['OrderedProduct'] as $op) {?>
		    _hrq.push(['addProduct', '<?php echo $op['Product']['name'] ?>', '<?php echo $op['product_price_with_dph'] ?>', '<?php echo $op['product_quantity'] ?>']);
		<?php } ?>
		    _hrq.push(['trackOrder']);
		
		(function() {
		    var ho = document.createElement('script'); ho.type = 'text/javascript'; ho.async = true;
		    ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.cz/direct/js/cache/1-roi-async.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ho, s);
		})();
		</script>
		
		<script type="text/javascript">
			fbq('track', 'Purchase', {
				content_type: 'product',
				content_ids: [<?php echo $fb_content_ids ?>],
				value: '<?php echo $order['Order']['orderfinaltotal'] ?>',
				currency: 'CZK'
			});
		</script>
		
<?php
	}
