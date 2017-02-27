<h2>Editovat přesměrování</h2>
<?php echo $form->Create('Redirect', array('url' => array('controller' => 'redirects', 'action' => 'edit')))?>
	<fieldset>
 		<legend>Detaily přesměrování</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Odkud
				</th>
				<td>
					<?php echo $form->input('Redirect.request_uri', array('label' => false, 'size' => 100))?>
				</td>
			</tr>
			<tr>

				<th>
					Kam
				</th>
				<td>
					<?php echo $form->input('Redirect.target_uri', array('label' => false, 'size' => 100))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?php
	echo $form->hidden('Redirect.id');
	echo $form->end('uložit')
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam přesměrování', array('action' => 'index'))?></li>
	</ul>
</div>