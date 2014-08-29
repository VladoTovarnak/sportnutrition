<?php
	if (!isset($_title) || $_title == '') {
		$_title = 'Sportovní výživa, doplňky stravy, kloubní výživa, vitamíny';
	}
	if (!isset($_description) || $_description == '') {
		$_description = 'Sportovní výživa od Sport Nutrition. Proteinové a sacharidové přípravky (proteiny, sacharidy, gainery), vitamíny a aminokyseliny.';
	}
?>
		<meta charset="utf-8" />
		<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
		<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?>style000.css?<?php echo str_replace(' ', '%20', date('l jS \of F Y h:i:s A'))?>" type="text/css" media="screen" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.js" type="text/javascript"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.js"></script>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.css" media="screen" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.easing.js" type="text/javascript"></script>
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.slidorion.js" type="text/javascript"></script>
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>main.js" type="text/javascript"></script>
		<title><?php echo $_title?> : Sportnutrition</title>
		<meta name="description" content="<?php echo $_description?>" />
<?php if (isset($_keywords) && !empty($_keywords) && is_string($_keywords)) {?>
		<meta name="keywords" content="<?php echo $_keywords?>" />
<?php } ?>
<?php if ($this->params['controller'] == 'searches' && $this->params['action'] == 'do_search') { ?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>search_filter.js"></script>
<?php } ?>
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH ?>jqueryui/style.css" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery-ui.js" type="text/javascript"></script>
		
		<!-- JRATING -->
		<!-- include CSS & JS files -->
		<!-- CSS file -->
		<link rel="stylesheet" type="text/css" href="/jRating-master/jquery/jRating.jquery.css" media="screen" />
		<!-- jQuery files -->
		<script type="text/javascript" src="/jRating-master/jquery/jRating.jquery.js"></script>
		<script type="text/javascript">
			var ratingStarType = 'small';
			<?php if (isset($this->params['controller']) && isset($this->params['action']) && $this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
				ratingStarType = 'big';
			<?php } ?> 
		</script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>/product_rating_management.js"></script>