<?php if (isset($category_most_sold) && !empty($category_most_sold)) { ?>
<h2><span>Nejprodávanější v této kategorii</span></h2>
<div class="dark">
<?php 	foreach ($category_most_sold as $most_sold_product) {
			$image = '/images/na.jpg';
			if (isset($most_sold_product['Image']) && !empty($most_sold_product['Image'])) {
				$image = '/product-images/small/' . $most_sold_product['Image']['name'];
			}
?>
	<div class="product card">
		<h3><a href="/<?php echo $most_sold_product['Product']['url']?>"><?php echo $most_sold_product['Product']['name']?></a></h3>
		<a href="/<?php echo $most_sold_product['Product']['url']?>"><img src="<?php echo $image?>" alt="Obrázek <?php echo $most_sold_product['Product']['name'] ?>" /></a>
		<div class="g_rating" data-average="<?php echo $most_sold_product['Product']['rate']?>" data-id="<?php echo $most_sold_product['Product']['id']?>"></div>
		<p><?php echo $most_sold_product['Product']['short_description']?></p>
		<b class="price"><?php echo $most_sold_product['Product']['price']?> Kč</b>
	</div>
	<?php } ?>
	<hr class="cleaner" />
</div>
<?php } ?>

<?php // nechci zatim zobrazovat popis kategorie
if (false) { ?>
<? if (isset($category['Category']['content']) && !empty($category['Category']['content'])) { ?>
<div><?php echo $category['Category']['content']?></div>
<?php } ?>
<?php } ?>

<?php if (!empty($products) ){ ?>
<h2><span><?php echo $category['Category']['name']?></span></h2>
<div class="paginator">
	<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'id' => 'filter_form', 'encoding' => false))?>
	Řadit podle:
	<?php echo $this->Form->input('sorting', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
	Na stránku:
	<?php echo $this->Form->input('paging', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
	<?php echo $this->Form->hidden('manufacturer_id')?>
	<?php echo $this->Form->hidden('attribute_id')?>
	<?php echo $this->Form->end()?>
	
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
<?php echo $this->element(REDESIGN_PATH . $listing_style); ?>
<div class="paginator">
	<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'encoding' => false))?>
	Řadit podle:
	<?php echo $this->Form->input('sorting', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
	Produktů stránku:
	<?php echo $this->Form->input('paging', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
	<?php echo $this->Form->end()?>
	
<?php
	echo $this->Paginator->counter(array('format' => '<strong>%count%</strong> položek&nbsp;'));
	echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
?>
</div>
 <?php } else { ?>
	<div id="mainContentWrapper">
		<p>Tato kategorie neobsahuje žádné produkty ani podkategorie.</p>
	</div>
<? } ?>
<?php echo $category['Category']['content']?>