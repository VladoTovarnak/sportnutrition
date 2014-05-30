<script type="text/javascript">
$(document).ready(function() {
	$('#ManufacturerSelect').change(function() {
		 $("#ManufacturerSelect option:selected").each(function() {
			 manufacturerId = $(this).attr('value');
			 if (manufacturerId) {
				// natahnu vyrobce a presmeruju
				$.ajax({
					type: 'POST',
					url: '/manufacturers/ajax_get_url',
					dataType: 'json',
					data: {
						id: manufacturerId
					},
					success: function(data) {
						if (data.success) {
							window.location.href = data.message;
						}
					}
				});
			 }
		});
	});
});
</script>

<h3 class="star">Výrobci</h3>
<?php
	$selected = null;
	if ( isset($this->params['manufacturer_id']) ){
		$selected = $this->params['manufacturer_id'];
	}

	if ( isset($product['Manufacturer']['id']) ){
		$selected = $product['Manufacturer']['id'];
	}
	
	echo $this->Form->select('Manufacturer.id', $manufacturers_list, $selected, array('empty' => 'Vyberte výrobce', 'id' => 'ManufacturerSelect'));
?>