$(function() {
	$('.paging').change(function() {
		this.form.submit();
	});
	
	$('.sorting').change(function() {
		this.form.submit();
	})
	
	$('.filter_manufacturer').click(function() {
		$('#CategoriesProductManufacturerId').val($(this).attr('rel'));
		$('#CategoriesProductAttributeId').val('');
		$('#filter_form').submit();
	});
	
	$('.filter_attribute').click(function() {
		$('#CategoriesProductAttributeId').val($(this).attr('rel'));
		$('#CategoriesProductManufacturerId').val('');
		$('#filter_form').submit();
	});
});