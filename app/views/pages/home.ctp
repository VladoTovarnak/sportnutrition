		<div id="slides" class="slidorion">
			<div class="accordion">
				<?php foreach ($homepage_banners as $banner) { ?>
				<div class="header"><div style="padding-top: 10px"><?php echo $banner['HomepageBanner']['description']?></div></div>
				<div class="content"></div>
				<?php } ?>
			</div>
			<div class="slider">
				<?php foreach ($homepage_banners as $banner) { ?>
				<div class="slide">
					<a href="<?php echo $banner['HomepageBanner']['url']?>">
						<picture>
						    <source media="(min-width: 641px)" srcset="<?php echo $homepage_banner_folder . '/' . $banner['HomepageBanner']['image']?>">
								<img src="<?php echo $homepage_banner_folder ?>/placeholder.png" 
								alt="<?php echo $banner['HomepageBanner']['description']?>">
						</picture>
					</a>
				</div>
				<?php } ?>
			</div>
		</div>
        <h1><a title="Sportovní výživa a doplňky stravy pro sportovce - SNV Vávra" href="/sportovni-vyziva-c9">Sportovní výživa a doplňky stravy pro sportovce - SNV Vávra</a></h1>
		<h2><span>Doporučujeme</span></h2>
		<?php foreach ($hp_recommended as $suggested_product) {?>
		<div class="product card">
			<h3><a href="/<?php echo $suggested_product['Product']['url']?>"><?php echo $suggested_product['Product']['name']?></a></h3>
			<a class="image_holder" href="/<?php echo $suggested_product['Product']['url']?>">
				<img src="/product-images/small/<?php echo $suggested_product['Image']['name']?>" alt="<?php echo $suggested_product['Product']['title']?>" />
			</a>
			<!-- <div class="rating" data-average="<?php echo $suggested_product['Product']['rate']?>" data-id="<?php echo $suggested_product['Product']['id']?>"></div> -->
			<p class="comments"><a href="<?php echo $suggested_product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $suggested_product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
			<?php
			if (isset($suggested_product['Availability']['cart_allowed']) && $suggested_product['Availability']['cart_allowed']) {
				if ( count($suggested_product['Subproduct']) < 1 ){
					echo $this->Form->create('Product', array('url' => '/' . $suggested_product['Product']['url'], 'encoding' => false));
					echo $this->Form->hidden('Product.id', array('value' => $suggested_product['Product']['id'], 'id' => 'hprecomProductId' . $suggested_product['Product']['id']));
					echo $this->Form->hidden('Product.quantity', array('value' => 1, 'id' => 'hprecomProductQuantity' . $suggested_product['Product']['id']));
					echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add', 'onclick' => 'fireAddToCart(' . $suggested_product['Product']['id'] . ', "' . $suggested_product['Product']['name'] . '", "' . $suggested_product['CategoriesProduct'][0]['Category']['name']. '", ' . $suggested_product['Product']['price'] . ');'));
					echo $this->Form->end();
				} else {
					?>
						<a href="/<?php echo $suggested_product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
					<?php
				}
			} else { ?>
			<p class="product-not-available">Produkt nyní nelze objednat.</p>
			<?php } ?>
			<p class="prices">
				<span class="common">Běžná cena: <?php echo front_end_display_price($suggested_product['Product']['retail_price_with_dph'])?> Kč</span><br />
				<span class="price">Cena: <?php echo front_end_display_price($suggested_product['Product']['price'])?> Kč</span>
			</p>
		</div>
		<?php } ?>

		<div class="left">
			<h2><span>Nejprodávanější</span></h2>
			<div id="best_products" class="slidorion">
				<div class="slider">
<?php			foreach ($hp_most_sold as $product) { ?>
					<div class="slide product card big" style="z-index: 2; left: 0px; top: 0px;">
						<h3><a href="<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
						<a class="image_holder" href="/<?php echo $product['Product']['url']?>">
							<img src="/product-images/small/<?php echo $product['Image']['name']?>" alt="<?php echo $product['Product']['title']?>"/>
						</a>
						<!-- <div class="g_rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div> -->
						<p class="comments"><a href="<?php echo $product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
						<?php
						if (isset($product['Availability']['cart_allowed']) && $product['Availability']['cart_allowed']) {
							if ( count($product['Subproduct']) < 1 ){
								echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
								echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id'], 'id' => 'hpmosProductId' . $product['Product']['id']));
								echo $this->Form->hidden('Product.quantity', array('value' => 1, 'id' => 'hpmosProductQuantity' . $product['Product']['id']));
								echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add', 'onclick' => 'fireAddToCart(' . $product['Product']['id'] . ', "' . $product['Product']['name'] . '", "' . $product['CategoriesProduct'][0]['Category']['name']. '", ' . $product['Product']['price'] . ');'));
								echo $this->Form->end();
							} else {
						?>
								<a href="/<?php echo $product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
						<?php
							}
						} else { ?>
						<p class="product-not-available">Produkt nyní nelze objednat.</p>
						<?php } ?>
						<p class="prices">
							<span class="common">Běžná cena: <?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span><br />
							<span class="price">Cena: <?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
						</p>
					</div>
<?php			} ?>
				</div>
				<div class="accordion right best">
<?php			foreach ($hp_most_sold as $product) { ?>
					<div class="header"><?php echo $product['Product']['name']?></div>
					<div class="content" style="display: none;"></div>
<?php 			} ?>
				</div>
			</div>
		</div>

		<div class="right news">
			<h2><span>Aktuality</span></h2>
<?php 		if (empty($hp_news)) { ?>
			<p><em>Nemáme pro Vás žádné aktuality.</em></p>
<?php 		} else { ?>
			<div id="news_container">
<?php	 		foreach ($hp_news as $actuality) { ?>
			<h3><?php echo $this->Html->link($actuality['News']['title'], array('controller' => 'news', 'action' => 'view', $actuality['News']['id']))?></h3>
			<p><?php echo $actuality['News']['first_sentence']?> ...</p>
			<span class="date"><?php echo $actuality['News']['czech_date']?></span>
<?php 			} ?>
	 		</div>
<?php 		echo $this->Html->link('Všechny aktuality', '/aktuality', array('class' => 'open'));
	 		} ?>
		</div>

		<div class="left">
			<h2><span>Kde nás najdete</span></h2>
			<div id="map" style="width:585px;height:375px"></div>
		</div>
		<div class="right open">
			<h2><span>Otevírací doba prodejny v Olomouci</span></h2>
<?php 
	$weekday = date('N');
?>
			<table>
				<tr<?php echo ( $weekday == 1 ) ? ' class="active_weekday"' : ''?>><th>Pondělí</th><td><?php echo $opening_hours[1]?></td></tr>
				<tr<?php echo ( $weekday == 2 ) ? ' class="active_weekday"' : ''?>><th>Úterý</th><td><?php echo $opening_hours[2]?></td></tr>
				<tr<?php echo ( $weekday == 3 ) ? ' class="active_weekday"' : ''?>><th>Středa</th><td><?php echo $opening_hours[3]?></td></tr>
				<tr<?php echo ( $weekday == 4 ) ? ' class="active_weekday"' : ''?>><th>Čtvrtek</th><td><?php echo $opening_hours[4]?></td></tr>
				<tr<?php echo ( $weekday == 5 ) ? ' class="active_weekday"' : ''?>><th>Pátek</th><td><?php echo $opening_hours[5]?></td></tr>
				<tr<?php echo ( $weekday == 6 ) ? ' class="active_weekday"' : ''?>><th>Sobota</th><td><?php echo $opening_hours[6]?></td></tr>
				<tr<?php echo ( $weekday == 7 ) ? ' class="active_weekday"' : ''?>><th>Neděle</th><td><?php echo $opening_hours[7]?></td></tr>
			</table>
			<p><a href="/firma.htm">Kontakty a další informace o prodejně naleznete <strong>zde</strong></a>.</p>
		</div>
		
		<div style="clear:both;"></div>
		
		<h2><span>Akční zboží</span></h2>
		
		<?php foreach ($hp_discounted as $product) {?>
		<div class="product card">
			<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
			<a class="image_holder" href="/<?php echo $product['Product']['url']?>"><img src="/product-images/small/<?php echo $product['Image']['name']?>" alt="<?php echo $product['Product']['title']?>" /></a>
			<!-- <div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>  -->
			<p class="comments"><a href="<?php echo $product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
			<?php
			if (isset($product['Availability']['cart_allowed']) && $product['Availability']['cart_allowed']) {
				if ( count($product['Subproduct']) < 1 ){
					echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
					echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id'], 'id' => 'hpdisProductId' . $product['Product']['id']));
					echo $this->Form->hidden('Product.quantity', array('value' => 1, 'id' => 'hpdisProductQuantity' . $product['Product']['id']));
					echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add', 'onclick' => 'fireAddToCart(' . $product['Product']['id'] . ', "' . $product['Product']['name'] . '", "' . $product['CategoriesProduct'][0]['Category']['name']. '", ' . $product['Product']['price'] . ');'));
					echo $this->Form->end();
				} else {
					?>
						<a href="/<?php echo $product['Product']['url']?>#AddProductWithVariantsForm" class="cart_add">Vybrat variantu</a>
					<?php
				}
			} else { ?>
			<p class="product-not-available">Produkt nyní nelze objednat.</p>
			<?php } ?>
			<p class="prices">
				<span class="common">Běžná cena: <?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span><br />
				<span class="price">Cena: <?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
			</p>
		</div>
		<?php } ?>