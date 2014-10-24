<div class="mainContentWrapper">
	<h2><span><?php echo $page_heading?></span></h2>
	<p>Vaše objednávka byla nyní <strong>přijata do našeho systému</strong>, již brzy Vás budeme informovat o stavu Vaší objednávky.</p>
<?	// jedna se o neprihlaseneho zakaznika, pro ktereho byl vytvoren ucet
	if (!empty($order['Customer']['login']) && !empty($order['Customer']['password'])) { ?>
	<p>Pro Vaše větší pohodlí jsme pro Vás vytvořili zákaznický účet. Pokud jste uvedl(a) v objednávce Vaši emailovou adresu,
	byly Vám odeslány tyto přihlašovací údaje:<br /><br />
	<strong>LOGIN: </strong><?=$order['Customer']['login']?><br />
	<strong>HESLO: </strong><?=$order['Customer']['password']?></p>
	<p>Doporučujeme Vám, <strong>poznamenat si tyto údaje</strong>, abyste mohl(a) plně využívat výhod Vašeho zákaznického účtu. Pro komunikaci
	emailem a telefonicky Vám stačí poznamenat si login.</p>
<? } else { ?>
	<p>Pomocí Vašeho <a href="/customers">uživatelského účtu</a> můžete kontrolovat stav Vaší objednávky.</p>
<? } ?>
	<h3>Děkujeme za Vaši důvěru.</h3>
</div>
	
<!-- Měřicí kód Sklik.cz -->
<iframe width="119" height="22" frameborder="0" scrolling="no" src="http://c.imedia.cz/checkConversion?c=100007593&color=ffffff&v=<?php echo $order['Order']['orderfinaltotal']?>"></iframe>