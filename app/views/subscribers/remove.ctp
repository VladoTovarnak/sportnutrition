<h2 class="product_name">Odhlášení ze seznamu emailových adres.</h2>
<div class="option">
<?php echo $form->create('Subscriber', array('id' => "SubscriberHomeForm"));?>
		<table class="customer_info_form" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Emailová adresa k&nbsp;odhlášení
				</th>
				<td>
					<?php echo $form->text('email_address', array('style' => 'width:200px;'))?>
					<?php echo $form->error('name')?>
				</td>
			</tr>
		</table>
	<?php echo $form->button('Odhlásit se ze seznamu', array('style' => "background: #E2001A;-webkit-border-radius: 3px;border-radius: 3px;border: none;color: #fff;padding: 8px 35px 8px 35px;float: left;cursor: pointer;"));?>
</div>