	<div id="main">
			<?php
				echo $this->element(REDESIGN_PATH . 'view_named/' . $id . '/before');
			
				if (!empty($products)) {
					echo $this->element(REDESIGN_PATH . $listing_style);
				} else {
			?>
					<div id="mainContentWrapper">
						<p>Tato kategorie neobsahuje žádné produkty ani podkategorie.</p>
					</div>
			<?php
				}
				echo $this->element(REDESIGN_PATH . 'view_named/' . $id . '/after');
			?>
			
	</div>