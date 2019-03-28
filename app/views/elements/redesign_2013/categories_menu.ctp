<h2>Sportovní výživa</h2>
<?php
$categories = $categories_menu['categories'];
$path_ids = $categories_menu['path_ids'];
?>
<ul id="menu" class="hideMobileOnly">
<?php
foreach ($categories as $category) { ?>
	<li<?php echo (isset($opened_category_id) && in_array($category['Category']['id'], $path_ids)) ? ' class="open"' : ''?>>
		<?php echo (isset($opened_category_id) && in_array($category['Category']['id'], $path_ids)) ? '<span></span>' : ''?>
		<a href="/<?php echo $category['Category']['url'] ?>"><?php echo (!empty($category['children']) ? '<span></span>' : '') ?><?php echo $category['Category']['name'] ?></a>
<?php
	if (!empty($category['children'])) { ?>
		<ul>
<?php
		foreach ($category['children'] as $subcategory) { ?>
			<li<?php echo (isset($opened_category_id) && in_array($subcategory['Category']['id'], $path_ids)) ? ' class="open"' : ''?>>
				<?php echo (isset($opened_category_id) && in_array($subcategory['Category']['id'], $path_ids)) ? '<span></span>' : ''?>
				<a href="/<?php echo $subcategory['Category']['url'] ?>"><?php echo (!empty($subcategory['children']) ? '<span></span>' : '')?><?php echo $subcategory['Category']['name'] ?></a>
				<?php if (!empty($subcategory['children'])) { ?>
				<ul>
<?php 				foreach ($subcategory['children'] as $subsubcategory) { ?>
					<li<?php echo (isset($opened_category_id) && in_array($subsubcategory['Category']['id'], $path_ids)) ? ' class="open"' : ''?>>
						<a href="/<?php echo $subsubcategory['Category']['url'] ?>"><?php echo $subsubcategory['Category']['name'] ?></a>
						<?php if (!empty($subsubcategory['children'])) { ?>
						<ul>
							<?php foreach ($subsubcategory['children'] as $leaf_category) { ?>
							<li<?php echo (isset($opened_category_id) && in_array($leaf_category['Category']['id'], $path_ids)) ? ' class="open"' : ''?>>
								<a href="/<?php echo $leaf_category['Category']['url'] ?>"><?php echo $leaf_category['Category']['name'] ?></a>
							</li>
							<?php } ?>
						</ul>
						<?php }?>
					</li>
<?php 				} ?>
				</ul>
<?php			} ?>
			</li>
<?php
			} ?>
		</ul>
<?php
	} ?>
	</li>
<?php
} ?>
</ul>