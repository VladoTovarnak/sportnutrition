<h2>Přesunout produkt</h2>
<p>Zvolte kategorii, do které chcete produkt <strong><?php echo $this->data['Product']['name']?></strong> přesunout.</p>
<?php
	echo $form->Create('CategoriesProduct');
	echo $form->hidden('CategoriesProduct.id');
	echo $form->hidden('CategoriesProduct.product_id');
	echo $form->select('CategoriesProduct.category_id', $categories, null, array('empty' => false));
	echo $form->Submit('přesunout');
	echo $form->end();
?>