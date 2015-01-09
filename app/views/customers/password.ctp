<h2><span><?php echo $page_heading?></span></h2>
<p>Do pole prosím vložte Vaši emailovou adresu, kterou používáte ve spojitosti s účtem na www.<?php echo CUST_ROOT?></p>
<?=$form->create('Customer', array('action' => 'password', 'url' => $this->passedArgs)) ?>
	<table id="form">
		<tr>
			<th>Emailová adresa:</th>
			<td>
				<?=$form->input('Customer.email', array('label' => false, 'class' => 'content')) ?>
				<?php echo $this->Form->hidden('Customer.back', array('value' => $back))?>
			</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><?=$form->submit('ODESLAT', array('class' => 'content'))?></td>
		</tr>
	</table>
<?=$form->end() ?>