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
		<h2><span><?php echo $category['Category']['heading']?></span></h2>
		<div class="paginator">
			<div class="sorter">
				<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'id' => 'filter_form', 'encoding' => false))?>
				Řadit podle:
				<?php echo $this->Form->input('sorting', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
				Na stránku:
				<?php echo $this->Form->input('paging', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
				<?php echo $this->Form->hidden('manufacturer_id')?>
				<?php echo $this->Form->hidden('attribute_id')?>
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
		
			echo $this->Paginator->counter(array('format' => '<strong>%count%</strong> položek&nbsp;'));
			echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
?>
			</div>
			<div class="clearer"></div>
		</div>
		
		<?php echo $this->element(REDESIGN_PATH . $listing_style); ?>
		<div class="paginator">
			<div class="numbers">
<?php
			echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
?>
				<div class="item_count_holder">
					<?php echo $this->Paginator->counter(array('format' => 'V kategorii se nachází celkem <strong>%count%</strong> položek.')); ?>
				</div>

			</div>
			<div class="clearer"></div>
			<div class="sorter">
			<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'encoding' => false))?>
			Řadit podle:
			<?php echo $this->Form->input('sorting', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
			Na stránku:
			<?php echo $this->Form->input('paging', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
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