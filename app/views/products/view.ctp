<div class="product">
	<h2><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['heading']?></a></h2>
	
	<!-- OBRAZKY -->
	<div class="photos">
<?php 
		if (empty($product['Image'])) {
			echo '<img src="/img/na_medium.jpg" alt="" />';
		} else {
			$class = 'big';
			$image_type = 'medium';

			foreach ($product['Image'] as $image_item) {
				$image = 'product-images/' . $image_type . '/' . $image_item['name'];
				echo '<a href="/product-images/' . $image_item['name'] . '" class="' . $class . ' fancybox"><img src="' . $image . '" alt="" /></a>';
				$class = 'min'; 
				$image_type = 'small';
			}
		} ?>
	</div>
	
	<div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
	<p><?php echo $product['Product']['short_description']?></p>
	
	<!-- VLOZENI DO KOSIKU, KDYZ PRODUKT NEMA VARIANTY -->
<?php
	if (empty($subproducts) && $product['Availability']['cart_allowed']) {
		echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
		echo $this->Form->input('Product.quantity', array('label' => false, 'div' => false, 'value' => 1, 'after' => '&nbsp;Ks'));
		echo '<b class="price">' . $product['Product']['price'] . '&nbsp;Kč</b>';
		echo $this->Form->button('Vložit do košíku');
		echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $this->Form->end();
	}
?>

<?php
	// zvyraznena cena se zobrazuje ve formulari pro objednani produktu bez variant, ale my ji chceme zobrazit i pokud se produkt neda objednat nebo pokud ma produkt varianty
	if (!$product['Availability']['cart_allowed'] || (!empty($subproducts) && $product['Availability']['cart_allowed'])) { ?>
	<p><b class="price"><?php echo $product['Product']['price'] ?>&nbsp;Kč</b></p>
<?php }
	// pokud se produkt neda objednat, zobrazim informaci
	if (!$product['Availability']['cart_allowed']) { ?>
	<p>Informaci o dostupnosti Vám rádi sdělíme na telefonu <strong><?php echo CUST_PHONE ?></strong> nebo e-mailu <strong><?php echo CUST_MAIL ?></strong>.</p>
<?php } ?>

	<!-- SOCIALNI SITE -->
	<div class="social">
		<div class="fb-like" data-href="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>" data-width="100" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
		<div><a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>">Tweet</a></div>
		<!-- Place this tag where you want the +1 button to render. -->
		<div class="g-plusone" data-href="http://www.<?php echo CUST_ROOT?>/<?php echo $product['Product']['url']?>"></div>
	</div>
	<hr class="cleaner" />
</div>

<!-- VLOZENI DO KOSIKU, KDYZ PRODUKT MA VARIANTY -->
<?php if (!empty($subproducts) && $product['Availability']['cart_allowed']) { ?>
<h3>Zvolte si variantu</h3>
<?php echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false)); ?>
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
	</div>
	<div id="tabs-2">
		<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'add'), 'id' => 'CommentAddForm', 'encoding' => false))?>
		<table>
			<tr>
				<th>Jméno:</th>
				<td><?php echo $this->Form->input('Comment.author', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><?php echo $this->Form->input('Comment.email', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Předmět:</th>
				<td><?php echo $this->Form->input('Comment.subject', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
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
			echo $this->Form->submit('Odeslat dotaz');
			echo $this->Form->end();
		?>
		
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
					<p>za <em><?php echo CUST_NAME?></em><br /><?php echo $comment['Administrator']['first_name']?> <?php echo $comment['Administrator']['last_name']?></p>
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