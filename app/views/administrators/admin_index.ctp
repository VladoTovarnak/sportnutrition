<h2>Seznam administrátorů</h2>
<table class="topHeading">
	<tr>
		<th>ID</th>
		<th>příjmení, jméno</th>
		<th>&nbsp;</th>
	</tr>
	<?php
foreach ($admins as $admin) { ?>
	<tr>
		<td><?php echo $admin['Administrator']['id'] ?></td>
		<td><?php echo $admin['Administrator']['last_name'] . ', ' . $admin['Administrator']['first_name'] ?></td>
		<td>
			<?php echo $html->link('upravit', array('controller' => 'administrators', 'action' => 'edit', $admin['Administrator']['id'])) ?>
		</td>

	</tr>
		
	<?php
} ?>
</table>
<div class="actions">
	<ul>
		<li><?php echo $html->link('nový administrátor', array('controller' => 'administrators', 'action' => 'add'))?></li>
	</ul>
</div>