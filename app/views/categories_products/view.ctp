<?php 	if (isset($category['Category']['content']) && !empty($category['Category']['content'])) { ?>
		<div id="category-desc">
<?php 		echo $category['Category']['content']; ?>
		</div>
<?php 	} ?>
<?php if (isset($category_most_sold) && !empty($category_most_sold)) { ?>
<h2><span>Nejprodávanější v této kategorii</span></h2>
<div class="dark">
<?php
	foreach ($category_most_sold as $most_sold_product) {
		echo $this->element(REDESIGN_PATH . 'most_sold_product_card', array('most_sold_product' => $most_sold_product));		
	} ?>
	<hr class="cleaner" />
</div>
<?php
	}
	if (!empty($products)) {
?>
		<h2><span><?php echo $_heading?></span></h2>
		<div class="paginator">
			<div class="sorter">
				<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'id' => 'filter_form', 'encoding' => false))?>
				Řadit podle:
				<?php echo $this->Form->input('s', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
				Na stránku:
				<?php echo $this->Form->input('p', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
				<?php echo $this->Form->hidden('m')?>
				<?php echo $this->Form->hidden('a')?>
				<?php echo $this->Form->end()?>
				<div class="clearer"></div>
			</div>
			<div class="numbers">
<?php
			$url_options = $this->params['url'];
			$url = '';
			unset($url_options['url']);
			foreach ($url_options as $url_option_key => $url_option_value) {
				$url .= $url_option_key . '=' . $url_option_value . '&';
			}
		
			if (!empty($url)) {
				$url = rtrim($url, '&');
			}
	
			$this->Paginator->options(array('url' => array_merge($this->passedArgs, array('?' => $url))));
		
/*
			//PREV TLACITKO - nezapomen, ze je to dole jeste jednou
			echo $this->Paginator->prev('« Předchozí', null, null, array('style' => 'display:none'));
			// pokud je predchozi stranka
			if ($this->Paginator->hasPrev()) {
				// vypis mezeru mezi tlacitkem "predchozi" a cislem stranky
				echo '&nbsp;';
			}
*/
			echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
/*
			 //NEXT TLACITKO - nezapomen, ze je to dole jeste jednou
			 // pokud je dalsi stranka	
			 if ($this->Paginator->hasNext()) {
			 	// vypis mezeru mezi cislem stranky a tlacitkem "dalsi" 
				echo '&nbsp;';
			}
			echo $this->Paginator->next('Další »', null, null, array('style' => 'display:none')); 
*/
?>
				<div class="item_count_holder">
					<?php echo $this->Paginator->counter(array('format' => 'V kategorii se nachází celkem <strong>%count%</strong> položek.')); ?>
				</div>
			</div>
			<div class="clearer"></div>
		</div>
		
		<?php echo $this->element(REDESIGN_PATH . $listing_style); ?>
		<div class="paginator">
			<div class="numbers">
<?php
/*
			//PREV TLACITKO
			echo $this->Paginator->prev('« Předchozí', null, null, array('style' => 'display:none'));
			// pokud je predchozi stranka
			if ($this->Paginator->hasPrev()) {
				// vypis mezeru mezi tlacitkem "predchozi" a cislem stranky
				echo '&nbsp;';
			}
*/
			echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
/*
			 //NEXT TLACITKO
			 // pokud je dalsi stranka	
			 if ($this->Paginator->hasNext()) {
			 	// vypis mezeru mezi cislem stranky a tlacitkem "dalsi" 
				echo '&nbsp;';
			}
			echo $this->Paginator->next('Další »', null, null, array('style' => 'display:none')); 
*/
?>
				<div class="item_count_holder">
					<?php echo $this->Paginator->counter(array('format' => 'V kategorii se nachází celkem <strong>%count%</strong> položek.')); ?>
				</div>

				
<?php 
				// TLACITKO S ODKAZEM NA DALSI STRANKU A S INFORMACI O POCTU PRODUKTU NA DALSI STRANCE --- OBSAHUJE DALSI PRODUKTY
				if (false) {
?>
				<div style="clear:both"></div>
<?php 
				if ($this->Paginator->hasNext()) {
					// produktu v kategorii celkem
					$total_category_products = $this->Paginator->counter(array('format' => '%count%'));
					// cislo stranky, ktera je vypsana
					$page = $this->Paginator->current();
					// pocet produktu na stranku
					$page_products = $paging_options[$this->data['CategoriesProduct']['p']];
					// pocet produktu na dalsi strance
					$next_page_products_count = $total_category_products - ($page * $page_products);
					if ($next_page_products_count > $page_products) {
						$next_page_products_count = $page_products;
					}
					
					switch ($next_page_products_count) {
						case '1': $next_page_button_text = 'Vypsat poslední produkt'; break;
						case '2':
						case '3':
						case '4': $next_page_button_text = 'Vypsat další ' . $next_page_products_count . ' produkty'; break;
						default: $next_page_button_text = 'Vypsat dalších ' . $next_page_products_count . ' produktů'; break;
					}
					// pokud existuje dalsi stranka, vypis button s proklikem na dalsi stranku, kde v textu je pocet produktu na dalsi strance
					echo $this->Paginator->next($next_page_button_text, array('escape' => false), null, array('style' => 'display:none'));
				}
?>

			<?php } // end if TLACITKO S ODKAZEM NA DALSI STRANKU A S INFORMACI O POCTU PRODUKTU NA DALSI STRANCE?>
			</div>
			<div class="clearer"></div>
			<div class="sorter">
			<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'encoding' => false))?>
			Řadit podle:
			<?php echo $this->Form->input('s', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
			Na stránku:
			<?php echo $this->Form->input('p', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
			<?php echo $this->Form->end()?>
			</div>
			<div class="clearer"></div>
		</div>
 <?php
	} else {
?>
		<div id="mainContentWrapper">
			<p>Tato kategorie neobsahuje žádné produkty ani podkategorie.</p>
		</div>
<? } ?>