<h2><span><?php echo $page_heading?></span></h2>
<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
<h3>Změna údajů zákazníka</h3>
<table class="leftHeading">
	<tr>
		<th>jméno</th>
		<td><?=$form->input('Customer.first_name', array('label'=> false))?></td>
	</tr>
	<tr>
		<th>příjmení</th>
		<td><?=$form->input('Customer.last_name', array('label'=> false))?></td>
	</tr>
	<tr>
		<th>telefon</th>
		<td><?=$form->input('Customer.phone', array('label'=> false))?></td>
	</tr>
	<tr>
		<th>email</th>
		<td><?=$form->input('Customer.email', array('label'=> false))?></td>
	</tr>
</table>

<h3>Přístupové údaje</h3>
<?php foreach ($customer['CustomerLogin'] as $index => $customer_login) { ?>
<table class="leftHeading">
	<tr>
		<th>login</th>
		<td><?php echo $this->Form->input('CustomerLogin.' . $index . '.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>původní heslo</th>
		<td>
			<?=$form->input('CustomerLogin.' . $index . '.old_password', array('label' => false, 'type' => 'password'))?>
			<?php echo $this->Form->hidden('CustomerLogin.' . $index . '.password')?>
		</td>
	</tr>
	<tr>
		<th>nové heslo</th>
		<td><?=$form->input('CustomerLogin.' . $index . '.new_password', array('label'=> false, 'type' => 'password'))?></td>
	</tr>
	<tr>
		<th>zopakujte nové heslo</th>
		<td>
			<?=$form->input('CustomerLogin.' . $index . '.new_password_rep', array('label'=> false, 'type' => 'password'))?>
			<?php echo $this->Form->hidden('CustomerLogin.' . $index . '.id', array('value' => $customer_login['id']))?>
		</td>
	</tr>
</table>
<?php } ?>
<?php echo $this->Form->hidden('Customer.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>