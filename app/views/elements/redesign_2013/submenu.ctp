<?php if (isset($categories_submenu) && !empty($categories_submenu)) { ?>
<ul class="submenu">
	<?php foreach ($categories_submenu as $category) { ?>
	<li><a href="/<?php echo $category['Category']['url']?>"><?php echo ucfirst(mb_convert_case($category['Category']['name'], MB_CASE_LOWER))?></a></li>
	<?php } ?>
</ul>
<?php } ?>