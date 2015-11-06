	<div id="main">
			<?php
				echo $this->element(REDESIGN_PATH . 'view_named/' . $id . '/before');
			
				if (!empty($products) && $id != 2 ) {
					echo $this->element(REDESIGN_PATH . $listing_style);
				}
				echo $this->element(REDESIGN_PATH . 'view_named/' . $id . '/after');
			?>
			
	</div>