<div id="footer">
	<div class="info">
		<h2>Informace</h2>
		<ul>
			<li><a href="/jak-nakupovat.htm">Jak nakupovat?</a></li>
			<li><a href="#">Často kladené dotazy</a></li>
			<li><a href="/firma.htm">Kontaktní a reklamační údaje</a></li>
			<li><a href="/doprava.htm">Ceník dopravy</a></li>
		</ul>
	</div>
	<div class="recommend">
		<h2>Doporučit známému</h2>
		<p>Zadejte e-mailovou adresu
		<br /> kam máme zaslat odkaz</p>
		<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send'), 'encoding' => false)); ?>
		<div class="pair">
			<?php
				echo $this->Form->input('Recommendation.target_email', array('label' => false, 'div' => false, 'placeholder' => 'Napište e-mail', 'error' => false));
				echo $this->Form->hidden('Recommendation.request_uri', array('value' => $_SERVER['REQUEST_URI']));
			?>
			<button name="recommend">OK</button>
		</div>
		<?php echo $this->Form->end()?>
	</div>
	<div class="company">
		<h2>Provozovatel</h2>
		<p><?php echo CUST_COMPANY?><br />
		IČO: <?php echo CUST_ICO ?><br />
		DIČ: <?php echo CUST_DIC ?><br />
		TEL: <b><?php echo CUST_PHONE?></b></p>
	</div>
	<div class="news">
		<h2 id="subscription">Novinky e-mailem</h2>
		<p>Odebírejte naše novinky e-mailem</p>
		<?php echo $this->Form->create('Subscriber', array('url' => array('controller' => 'subscribers', 'action' => 'add'), 'encoding' => false)); ?>
		<div class="pair" style="width:262px">
			<?php
				echo $this->Form->input('Subscriber.email', array('label' => false, 'div' => false, 'placeholder' => 'Napište e-mail', 'error' => false));
				echo $this->Form->hidden('Subscriber.request_uri', array('value' => $_SERVER['REQUEST_URI']));
			?>
			<button name="news">OK</button>
			<?php echo $this->Form->error('Subscriber.email'); ?>
		</div>
		<?php echo $this->Form->end() ?>
	</div>
	<hr />

	<div class="copyright">
		<img src="/images/<?php echo REDESIGN_PATH ?>logo_dark.png" alt="<?php echo CUST_NAME?>" />
		&copy; <a href="/">www.<?php echo CUST_ROOT?></a> All rights Reserved.
		<a href="https://www.facebook.com/pages/SportNutrition/230263693680695?fref=ts" target="_blank"><img src="/images/<?php echo REDESIGN_PATH ?>logo_facebook.png" alt="Facebook" /></a>
		<a href="https://plus.google.com/b/107543305825459915226/107543305825459915226/about?hl=cs" target="_blank"><img src="/images/<?php echo REDESIGN_PATH ?>logo_google.png" alt="Google+" /></a>
		<a href="http://www.youtube.com/channel/UCheMzjYoHUiq-9I5OH3DdKA" target="_blank"><img src="/images/<?php echo REDESIGN_PATH ?>logo_youtube.png" alt="YouTube" /></a>
	</div>
</div>
<?php if ($this->params['controller'] != 'orders' && $this->params['action'] != 'finished') { ?>
 		<script type="text/javascript">
 		var _gaq = _gaq || [];
 		_gaq.push(['_setAccount', 'UA-2497074-16']);

 		_gaq.push(['_addOrganic', 'zbozi.cz', 'q']);
 		_gaq.push(['_addOrganic', 'centrum.cz', 'q']);
 		_gaq.push(['_addOrganic', 'atlas.cz', 'q']);
 		_gaq.push(['_addOrganic', 'zoohoo.cz', 'q']);
 		_gaq.push(['_addOrganic', 'jyxo.cz', 'q']);
 		_gaq.push(['_addOrganic', 'atomz.cz', 'sp-q']);
 		_gaq.push(['_addOrganic', 'tiscali.cz', 'query']);
 		_gaq.push(['_addOrganic', 'volny.cz', 'search']);
 		_gaq.push(['_addOrganic', '1.cz', 'q']);
 		_gaq.push(['_addOrganic', 'toplist.cz', 'search']);
 		_gaq.push(['_addOrganic', 'navrcholu.cz', 'q']);
 		_gaq.push(['_addOrganic', 'tiscali.cz', 'query']);
 		_gaq.push(['_addOrganic', 'bezvaportal.cz', 'fulltext']);
 		_gaq.push(['_addOrganic', 'images.google.cz', 'q']);
 		_gaq.push(['_addOrganic', 'opendir.cz', 'cohledas']);
 		_gaq.push(['_addOrganic', 'stavit.cz', 'search_text']);
 		_gaq.push(['_addOrganic', 'stopa.cz', 'q']);
 		_gaq.push(['_addOrganic', 'obrazky.cz', 'q']);
 		_gaq.push(['_addOrganic', '1188.cz', 'q']);
 		_gaq.push(['_addOrganic', 'citysearch.cz', 'retezec']);
 		_gaq.push(['_addOrganic', 'obrazky.cz', 'q']);
 		_gaq.push(['_addOrganic', 'info-brno.cz', 'search']);
 		_gaq.push(['_addOrganic', 'internetoveobchody.cz', 'phrase']);
 		_gaq.push(['_addOrganic', 'zona.cz', 'kw']);
 		_gaq.push(['_addOrganic', 'heureka.cz', 'h[fraze]']);
 		
 		_gaq.push(['_addOrganic', 'zoznam.sk', 's']);
 		_gaq.push(['_addOrganic', 'atlas.sk', 'phrase']);
 		_gaq.push(['_addOrganic', 'zoohoo.sk', 'q']);
 		_gaq.push(['_addOrganic', 'hladas.sk', 'slovo']);
 		_gaq.push(['_addOrganic', 'centrum.sk', 'q']);
 		_gaq.push(['_addOrganic', 'vsevedko.sk', 'keyword']);
 		_gaq.push(['_addOrganic', 'szm.sk', 'WS']);
 		_gaq.push(['_addOrganic', 'morfeo.sk', 'q']);
 		_gaq.push(['_addOrganic', 'azet.sk', 'sq']);
 		_gaq.push(['_addOrganic', 'toplist.sk', 'search']);
 		_gaq.push(['_addOrganic', 'webstranka.sk', 'kword']);
 		_gaq.push(['_addOrganic', 'images.google.sk', 'q']);
 		
 		_gaq.push(['_trackPageview']);
 		
 		//GA DATA
 		
 	  (function() {
 		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
 		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
 		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
 		  })();
			
 		</script>
<?php } ?>
<?php echo $this->element('sql_dump')?>