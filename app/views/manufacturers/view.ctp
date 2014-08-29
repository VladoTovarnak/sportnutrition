<?php if (isset($manufacturer_most_sold) && !empty($manufacturer_most_sold)) { ?>
<h2><span>Nejprodávanější v této kategorii</span></h2>
<div class="dark">
<?php 	foreach ($manufacturer_most_sold as $most_sold_product) {
			$image = '/images/na.jpg';
			if (isset($most_sold_product['Image']) && !empty($most_sold_product['Image'])) {
				$image = '/product-images/small/' . $most_sold_product['Image']['name'];
			}
?>
	<div class="product card">
		<h3><a href="/<?php echo $most_sold_product['Product']['url']?>"><?php echo $most_sold_product['Product']['name']?></a></h3>
		<a href="/<?php echo $most_sold_product['Product']['url']?>"><img src="<?php echo $image?>" alt="Obrázek <?php echo $most_sold_product['Product']['name'] ?>" /></a>
		<div class="rating" data-average="<?php echo $most_sold_product['Product']['rate']?>" data-id="<?php echo $most_sold_product['Product']['id']?>"></div>
		<p><?php echo $most_sold_product['Product']['short_description']?></p>
		<b class="price"><?php echo $most_sold_product['Product']['price']?> Kč</b>
	</div>
	<?php } ?>
	<hr class="cleaner" />
</div>
<?php } ?>

<h2><span><?php echo $manufacturer['Manufacturer']['name']?></span></h2>
<?php if (!empty($products) ){ ?>
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
	
	echo $this->Paginator->counter(array('format' => '<b>%count%</b> položek&nbsp;'));
	echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
?>
	</div>
	<div class="clearer"></div>
</div>
<?php echo $this->element(REDESIGN_PATH . $listing_style); ?>
<div class="paginator">
	<div class="sorter">
	<?php echo $this->Form->create(null, array('url' => '/' . $this->params['url']['url'], 'type' => 'get', 'encoding' => false))?>
	Řadit podle:
	<?php echo $this->Form->input('sorting', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $sorting_options, 'div' => false, 'class' => 'sorting'))?>
	Na stránku:
	<?php echo $this->Form->input('paging', array('label' => false, 'type' => 'select', 'empty' => false, 'options' => $paging_options, 'div' => false, 'class' => 'paging'))?>
	<?php echo $this->Form->end()?>
	</div>
	<div class="numbers">
<?php
	echo $this->Paginator->counter(array('format' => '<b>%count%</b> položek&nbsp;'));
	echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'first' => 1, 'last' => 1, 'modulus' => 3));
?>
	</div>
	<div class="clearer"></div>
</div>
 <?php } else { ?>
	<div id="mainContentWrapper">
		<p>Tato kategorie neobsahuje žádné produkty ani podkategorie.</p>
	</div>
<? } ?>