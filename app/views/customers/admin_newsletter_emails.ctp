<h1>Emaily pro novinky</h1>
<p><?php echo count($emails)?> záznamů.</p>
<p><?php
foreach ($emails as $email) {
	echo $email['Customer']['email'] . '<br/>';
} ?></p>