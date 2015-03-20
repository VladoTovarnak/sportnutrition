<?php
	if (!empty($products)) {
		echo $this->element(REDESIGN_PATH . $listing_style);
?>

	<div class="other_products">
		 <a target="_blank" href="/proteiny-c16">
		 	ZOBRAZIT DALŠÍ PROTEINY Z NABÍDKY
		 </a>
	</div>
	
	<!-- SOCIALNI SITE -->
	<div class="social" style="margin-top:30px;">
		<div id="social_holder">
			<div class="fb-like" data-href="http://www.<?php echo CUST_ROOT?>/proteiny-akce" data-width="100" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>
			<div><a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="http://www.<?php echo CUST_ROOT?>/proteiny-akce" data-count="none">Tweet</a></div>
			<div style="float:left">
				<div class="g-plusone" data-href="http://www.<?php echo CUST_ROOT?>/proteiny-akce" data-size="medium" data-annotation="none" style="float:left"></div>
			</div>
		</div>
	</div>


<?php
	} else {
?>
		<div id="mainContentWrapper">
			<p>Tato kategorie neobsahuje žádné produkty ani podkategorie.</p>
		</div>
<? } ?>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/cs_CZ/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

<script>
!function(d,s,id){
	var js,fjs=d.getElementsByTagName(s)[0];
	if(!d.getElementById(id)){
		js=d.createElement(s);
		js.id=id;
		js.src="https://platform.twitter.com/widgets.js";
		fjs.parentNode.insertBefore(js,fjs);
}}(document,"script","twitter-wjs");</script>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  window.___gcfg = {lang: 'cs'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>