<script type="text/javascript">
	$(document).ready(function() {
		$('#copyAddressLink').click(function(e) {
			e.preventDefault();
			$('#Address1Street').val($('#Address0Street').val());
			$('#Address1StreetNo').val($('#Address0StreetNo').val());
			$('#Address1City').val($('#Address0City').val());
			$('#Address1Zip').val($('#Address0Zip').val());
			$('#Address1State').val($('#Address0State option:selected').val());
		});
	});
</script>

<h2><span>Osobní údaje</span></h2>
<? if (!$session->check('Customer')){ ?>
<p><strong>Jste-li již našim zákazníkem</strong>, přihlašte se prosím pomocí formuláře v záhlaví stranky.</p>
<? } ?>

<?php echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'order_personal_info')))?>
<table>
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Firma</th>
		<td><?php echo $this->Form->input('Customer.company_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČ</th>
		<td><?php echo $this->Form->input('Customer.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $this->Form->input('Customer.dic', array('label' => false))?></td>
	</tr>
</table>
<h3>Fakturační adresa</h3>
<table>
	<tr>
		<th>Ulice</th>
		<td><?php echo $this->Form->input('Address.0.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné</th>
		<td><?php echo $this->Form->input('Address.0.street_no', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město</th>
		<td><?php echo $this->Form->input('Address.0.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $this->Form->input('Address.0.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Stát</th>
		<td><?php echo $this->Form->input('Address.0.state', array('label' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika', 'Slovensko' => 'Slovensko'))); ?></td>
	</tr>
</table>

<h3>Doručovací adresa</h3>
<p><a href="#" id="copyAddressLink">Klikněte zde, pokud je stejná jako fakturační.</a>
<table>
	<tr>
		<th>Ulice</th>
		<td><?php echo $this->Form->input('Address.1.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné</th>
		<td><?php echo $this->Form->input('Address.1.street_no', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město</th>
		<td><?php echo $this->Form->input('Address.1.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $this->Form->input('Address.1.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Stát</th>
		<td><?php echo $this->Form->input('Address.1.state', array('label' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika', 'Slovensko' => 'Slovensko'))); ?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'f')));
	echo $this->Form->hidden('Address.1.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'd')));
	
	echo $this->Form->submit('>> Krok 2/4: Výběr dopravy a platby');
	echo $this->Form->end();
?>