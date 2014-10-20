<script>
	$(function() {
		if (window.location.hash == '#comment_list') {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		}

		$('.add_comment_link').click(function(e) {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		});

		$('.view_comments_link').click(function(e) {
			// zjistim id tabu s diskuzi
			var index = $('.tabs a[href="#tabs-2"]').parent().index();
			// tab nastavim jako otevreny
			$(".tabs").tabs("option", "active", index);
		});

	// pokud ma varianty, skryju pole pro vlozeni ks do kosiku
	// tlacitko se bude chovat jako odkaz na kotvu, ktera se nachazi u formulare pro vlozeni produktu s variantami
	<?php if (!empty($subproducts) && $product['Availability']['cart_allowed']) { ?>
		$('#ProductQuantity').hide();
		$('#AddToCartButton').click(function(e) {
			e.preventDefault();
			$('html, body').animate({
		        scrollTop: $("#AddProductWithVariantsForm").offset().top
		    }, 1000);
		});
	<?php } ?>
	});
</script>

<div class="product">
	<h2><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['heading']?></a></h2>
	
	<!-- OBRAZKY -->
	<div class="photos">
<?php 
		if (empty($product['Image'])) {
			$image = '/img/na_medium.jpg';
		} else {
			$class = 'big';
			$image_type = 'medium';

			foreach ($product['Image'] as $image_item) {
				$image = '/img/na_' . $image_type . '.jpg';
				$path = 'product-images/' . $image_type . '/' . $image_item['name'];
				if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
					$path = 'product-images-new/' . $image_type . '/' . $image_item['name'];
				}
				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
					echo '<a href="/product-images/' . $image_item['name'] . '" class="' . $class . ' fancybox"><img src="' . $image . '" alt="" /></a>';
				} else {
					echo '<img src="' . $image . '" alt="" />';
					break;
				}
				
				$class = 'min'; 
				$image_type = 'small';
			}
		} ?>
	</div>
	<p class="manufacturer"><?php echo $this->Html->link($product['Manufacturer']['name'], '/' . strip_diacritic($product['Manufacturer']['name'] . '-v' . $product['Manufacturer']['id']))?></p>
	<div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
	<p class="comments"><a href="#comment_list" class="view_comments_link">Přečíst komentáře</a> | <a href="#tabs-2" class="add_comment_link">Přidat komentář</a></p>
<?php if ($product['Product']['name'] != $product['Product']['short_description']) { ?>
	<p><?php echo $product['Product']['short_description']?> <a href="#tabs-1">Více informací...</a></p>
<?php } ?>
	<p class="prices">
<?php if (isset($product['Product']['retail_price_with_dph']) && $product['Product']['retail_price_with_dph'] > $product['Product']['price']) { ?>
		Běžná cena: <?php echo front_end_display_price($product['Product']['retail_price_with_dph']) ?>&nbsp;Kč<br/>
<?php } ?>
		<b class="price">Cena: <span id="price_str"><?php echo front_end_display_price($product['Product']['price']) ?></span>&nbsp;Kč</b>
	</p>
<?php if (isset($product['Product']['note']) && !empty($product['Product']['note'])) { ?>
	<p><b>Poznámka:</b> <?php echo $product['Product']['note']?></p>
<?php } ?>
<?php 
	// pokud se produkt neda objednat, zobrazim informaci
	if (!$product['Availability']['cart_allowed']) { ?>
	<p>Informaci o dostupnosti Vám rádi sdělíme na telefonu <strong><?php echo CUST_PHONE ?></strong> nebo e-mailu <strong><?php echo CUST_MAIL ?></strong>.</p>
<?php } ?>

	<!-- VLOZENI DO KOSIKU, KDYZ PRODUKT NEMA VARIANTY -->
<?php
	if (empty($subproducts) && $product['Availability']['cart_allowed']) {
		echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false, 'class' => 'on_detail	'));
?>
	<div id="FormElementsHolder">
		<div id="QuantityInput">
<?php 
		echo $this->Form->input('Product.quantity', array('label' => false, 'div' => false, 'value' => 1, 'after' => '<span>&nbsp;Ks</span>'));
?>
		</div>
<?php 
		echo $this->Form->button('Vložit do košíku', array('id' => 'AddToCartButton'));
?>
		<div class="clearer"></div>
	</div>
<?php 
		echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $this->Form->end();
	} elseif (!empty($subproducts) && $product['Availability']['cart_allowed']) {
		echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false, 'class' => 'on_detail	'));
		echo $this->Form->button('Vložit do košíku', array('id' => 'AddToCartButton'));
		echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $this->Form->end();
	}
?>
	<!-- SOCIALNI SITE -->
	<div class="social">
		<div id="social_holder">
			<div class="fb-like" data-href="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>" data-width="100" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>
			<div><a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>" data-count="none">Tweet</a></div>
			<div style="float:left">
				<div class="g-plusone" data-href="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>" data-size="medium" data-annotation="none" style="float:left"></div>
			</div>
		</div>
	</div>
	<hr class="cleaner" />
	<div class="availability">
		<b>Dostupnost:</b>&nbsp;<?php echo ucfirst($product['Availability']['name'])?><br/>
	</div>
</div>
<h3><a href="/garance-nejnizsi-ceny.htm">Garance nejnižší ceny</a></h3>
<p>Našli jste jinde lepší cenu ? Napište nám na <a href='info@sportnutrition.cz'>info@sportnutrition.cz</a> (uveďte výrobek, cenu a webovou adresu, kde jste jej našli). Pokud to bude možné, cenu Vám srovnáme či nabídneme nižší.</p>

<!-- VLOZENI DO KOSIKU, KDYZ PRODUKT MA VARIANTY -->
<?php if (!empty($subproducts) && $product['Availability']['cart_allowed']) { ?>
<h3>Zvolte si variantu</h3>
<?php echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false, 'id' => 'AddProductWithVariantsForm')); ?>
<table>
	<tr>
		<th>Varianta</th>
		<th>Cena</th>
		<th>Množství</th>
		<th>&nbsp;</th>
	</tr>
<?php 
foreach ($subproducts as $subproduct) {
	$information = '';
	foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
		$information .= $attributes_subproduct['Attribute']['Option']['name'] . ': ' . $attributes_subproduct['Attribute']['value'] . '<br/>';
	}
	
	$subproduct['Subproduct']['price_with_dph'] += $product['Product']['price']; 
?>
	<tr>
		<td><?php echo $information ?></td>
		<td class="center"><?php echo $subproduct['Subproduct']['price_with_dph']?>&nbsp;Kč</td>
		<td class="center">
			<?php echo $this->Form->input('Subproduct.' . $subproduct['Subproduct']['id'] . '.quantity', array('label' => false, 'div' => false, 'value' => 1, 'after' => '&nbsp;Ks'))?>
			<?php echo $this->Form->hidden('Subproduct.' . $subproduct['Subproduct']['id'] . '.id', array('value' => $subproduct['Subproduct']['id']))?>
		</td>
		<td class="center"><button name="data[Subproduct][<?php echo $subproduct['Subproduct']['id'] ?>][chosen]" value="1">Do košíku</button></td>
	</tr>
<?php } ?>
</table>
<?php echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']))?>
<?php echo $this->Form->end()?>
<?php } ?>

<!-- POPIS A DISKUZE -->
<div class="tabs">
	<ul>
		<li><a href="#tabs-1">Popis</a></li>
		<li><a href="#tabs-2">Diskuze</a></li>
	</ul>
	<div id="tabs-1">
		<?php
		echo $product['Product']['description'];
		if (!empty($product['Product']['video'])) {
			echo $product['Product']['video'];
		} ?>
		<?php if (isset($product['ProductType']['text']) && !empty($product['ProductType']['text'])) { ?>
		<h3>Upozornění</h3>
		<p><?php echo $product['ProductType']['text']?></p>
		<?php } ?>
	</div>
	<div id="tabs-2">
		<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'add'), 'id' => 'CommentAddForm', 'encoding' => false))?>
		<table>
			<tr>
				<th>Jméno:</th>
				<td><?php echo $this->Form->input('Comment.author', array('label' => false, 'class' => 'content'))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><?php echo $this->Form->input('Comment.email', array('label' => false, 'class' => 'content'))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Předmět:</th>
				<td><?php echo $this->Form->input('Comment.subject', array('label' => false, 'class' => 'content'))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Dotaz</th>
				<td>
					<?php echo $this->Form->input('Comment.body', array('label' => false, 'cols' => 63, 'rows' => 10))?><div class="formErrors"></div>
					<?php echo $this->Form->hidden('Comment.product_id', array('value' => $product['Product']['id']));
					echo $this->Form->hidden('Comment.request_uri', array('value' => $_SERVER['REQUEST_URI'])); ?>
				</td>
			</tr>
		</table>
		<?php 
			echo $this->Form->input('Comment.personal_email', array('label' => false, 'type' => 'text', 'class' => 'neukazovat', 'value' => ''));
			echo $this->Form->input('Comment.work_email', array('label' => false, 'type' => 'text', 'class' => 'neukazovat', 'value' => 'jan.novak@necoxyz.com'));
			echo $this->Form->submit('Odeslat dotaz');
			echo $this->Form->end();
		?>
		<div id="comment_list"></div>
		<?php if (empty($product['Comment'])) { ?>
		<p>Diskuze neobsahuje žádné komentáře pro tento produkt.</p>
		<?php } else { ?>
		<div style="margin-top:10px;">
		<?php foreach ($product['Comment'] as $comment) { ?>
			<div style="background-color:silver;padding:3px;">
				<p><strong><?php echo $comment['subject']?></strong> od <strong><?php echo $comment['author']?></strong> ze dne <em><?php echo cz_date_time($comment['created'])?></em></p>
			</div>
			<?php echo $comment['body']?>
			<?php if (!empty($comment['reply'])) { ?>
				<div style="margin-top:5px;padding-left:5px;margin-left:15px;border-left:1px solid black;">
					<p><?php echo $comment['reply']?></p>
					<p>za <em><?php echo CUST_NAME?></em><br />
					<?php
						$answerer_string = $comment['Administrator']['first_name'] . ' ' . $comment['Administrator']['last_name'];
						if (isset($comment['Administrator']['comment_string']) && !empty($comment['Administrator']['comment_string'])) {
							$answerer_string = $comment['Administrator']['comment_string'];
						}
						echo $answerer_string;
					?></p>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>

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