<?php 
	if ( $_SERVER['DOCUMENT_ROOT'] != 'C:/wamp/www/sportnutrition.cz' ){
?>
<h3 class="facebook">Facebook</h3>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/cs_CZ/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like-box" data-href="https://www.facebook.com/pages/SportNutrition/230263693680695?fref=ts" data-width="193" data-height="400" data-show-faces="true" data-stream="false" data-header="true"></div>
<?php 
	}
?>