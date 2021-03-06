<div id="footer">
	<div class="info">
		<h2>Informace</h2>
		<ul>
			<li><a href="/jak-nakupovat.htm">Jak nakupovat?</a></li>
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
		<img src="/images/<?php echo REDESIGN_PATH?>placeholder.png" class="lazy" data-src="/images/<?php echo REDESIGN_PATH ?>logo_dark.png" width="102" height="44" alt="<?php echo CUST_NAME?>" />
		&copy; <a href="/">www.<?php echo CUST_ROOT?></a> All rights Reserved.
		<a rel="noreferrer" href="https://www.facebook.com/pages/SportNutrition/230263693680695?fref=ts" target="_blank"><img src="/images/<?php echo REDESIGN_PATH?>placeholder.png" class="lazy" data-src="/images/<?php echo REDESIGN_PATH ?>logo_facebook.png" width="38" height="38" alt="Facebook" /></a>
	</div>
	<p class="eet_warrning">
		Podle zákona o evidenci tržeb je prodávající povinen vystavit kupujícímu účtenku.
		Zároveň je povinen zaevidovat přijatou tržbu u správce daně online;<br>
  		v případě technického výpadku pak nejpozději do 48hodin.
  	</p>
</div>

<?php 
	if (!defined('ISDEVELOPER')){
?>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-55908391-1']);
		
		<?php
			if ($searchers = file_get_contents('js/ga-add.js')) {
				echo $searchers;
			}
		?>
			_gaq.push(['_setSiteSpeedSampleRate', 90]);
			_gaq.push(['_trackPageview']);
		<?php // data do GA o objednavce na dekovaci strance
		if ($this->params['controller'] == 'orders' && $this->params['action'] == 'finished' && isset($jscript_code)) {
			echo $jscript_code;
		} ?>
		
			(function() {
		 	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		 	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		 	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		<a href="https://www.toplist.cz/" target="_top"><img src="https://toplist.cz/dot.asp?id=116188" alt="TOPlist" width="1" height="1"/></a>
		
		<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/4e4ba0f3dec228fafc401116e/b2be5f45949d03ab5b0e19e3e.js");</script>
<?php 
	} // !defined('ISDEVELOPER')
?>


<?php echo $this->element(REDESIGN_PATH . 'nutrishop_banner')?>
<?php echo $this->element(REDESIGN_PATH . 'cookies_panel')?>