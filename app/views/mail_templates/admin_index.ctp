﻿<h2>Seznam emailových šablon</h2>

<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			ID
		</th>
		<th>
			předmět
		</th>
		<th>
			&nbsp;
		</th>
	</tr>
<?
	foreach ( $mail_templates as $mail_template ){
		echo '
		<tr>
			<td>
				' . $mail_template['MailTemplate']['id'] . '
			</td>
			<td>
				' . $mail_template['MailTemplate']['subject'] . '
			</td>
			<td>
				' . $html->link('upravit', array('controller' => 'mail_templates', 'action' => 'edit', $mail_template['MailTemplate']['id'])) . '
				' . $html->link('smazat', array('controller' => 'mail_templates', 'action' => 'del', $mail_template['MailTemplate']['id']), array(), 'Opravdu chcete tuto šablonu smazat?') . '
			</td>
		</tr>
		';
	}
?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('nová šablona', array('controller' => 'mail_templates', 'action' => 'add'))?></li>
	</ul>
</div>