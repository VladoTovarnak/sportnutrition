<?php
if (isset($breadcrumbs)) {
	$arr2link = function ($item) {
		return $this->Html->link($item['anchor'], $item['href']);
	};
	
	$breadcrumbs = array_map($arr2link, $breadcrumbs);
	$breadcrumbs = implode('</li><li>', $breadcrumbs);
	$breadcrumbs = '<ul class="breadcrumbs"><li><a href="/">Úvodní stránka</a></li><li>' . $breadcrumbs . '</li></ul>';
	echo $breadcrumbs;
}
?>
<hr class="cleaner" />