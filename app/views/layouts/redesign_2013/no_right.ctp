<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
		<style type="text/css">
			#body #main{
				float: right;
  				width: 980px;
			}
			#body #main div.left{
				width: 980px;
			}
			#body #main em{
				font-size:12px;
			}
			#body #main div.left div.products{
				border-top:none;
				width:900px;
				margin:auto;
			}
			#body #main p.cite{
				font-size:21px;
				margin:0px;
			}
			#body #main div.other_products{
				padding:10px;
				border:1px solid #E2001A;
				width: 370px;
				text-align: center;
				font-size: 21px;
				margin-top:30px;
				background:#F5F5F5;
			}
			#body #main div.other_products a{
				text-decoration:none;
				color:#E2001A;
			}
			#body #main div.other_products a:hover{
				font-weight:bold;
			}
		</style>
	</head>
<body>

<div id="body">
	<div id="header">
		<a id="logo" href="/"><img src="/images/redesign_2013/logo_snv.png" width="240px" height="125px" alt="SNV - sportovní výživa pro Vás" /></a>
		<?php
			echo $this->element(REDESIGN_PATH . 'login_box');
			echo $this->element(REDESIGN_PATH . 'horizontal_menu');
		?>
	</div>
	<?php
		echo $this->element(REDESIGN_PATH . 'sidebox');
		echo $this->element(REDESIGN_PATH . 'submenu');
		echo $this->element(REDESIGN_PATH . 'search_box');
	?>

	<hr class="cleaner" />

	<?php 
		if ($session->check('Message.flash')){
			echo $session->flash();
		}
		echo $content_for_layout;
	?>



	<hr class="cleaner" />
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
</div>
<?php
	echo $this->element(REDESIGN_PATH . 'heureka_overeno');
	echo $this->element(REDESIGN_PATH . 'facebook_prava');
	echo $this->element(REDESIGN_PATH . 'default_foot2');
?>
</body>
</html>
<?php echo $this->element('sql_dump')?>