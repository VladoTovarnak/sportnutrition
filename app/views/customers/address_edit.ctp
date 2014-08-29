<h2><span><?php echo $page_heading?></span></h2>
<?=$form->create('Address', array('url' => array('controller' => 'customers', 'action' => 'address_edit', 'type' => $this->params['named']['type']), '')) ?>
<table id="customerLayout">
	<tr>
		<td>
			<table class="leftHeading">
			<tr>
				<th>
					jméno a příjmení <br /> / název společnosti
				</th>
				<td>
					<?=$form->input('Address.name', array('label' => false, 'class' => 'content')) ?>
				</td>
			</tr>
			<tr>
				<th>
					ulice
				</th>
				<td>
					<?=$form->input('Address.street', array('label' => false, 'class' => 'content')) ?>
				</td>
			</tr>
			<tr>
				<th>
					číslo popisné
				</th>
				<td>
					<?=$form->input('Address.street_no', array('label' => false, 'size' => 5)) ?>
				</td>
			</tr>
			<tr>
				<th>
					město / obec
				</th>
				<td>
					<?=$form->input('Address.city', array('label' => false, 'class' => 'content')) ?>
				</td>
			</tr>
			<tr>
				<th>
					psč
				</th>
				<td>
					<?=$form->input('Address.zip', array('label' => false)) ?>
				</td>
			</tr>
			<tr>
				<th>
					stát
				</th>
				<td>
					<input type="text" name="fakeState" value="Česká Republika" disabled class="content"/>
					<?=$form->hidden('Address.state', array('value' => 'Česká Republika'))?>
				</td>
			</tr>

		</table>
	</td>
	</tr>
</table>
<?=$form->hidden('Address.type') ?>
<?php echo $this->Form->submit('Uložit', array('class' => 'content'))?>
<?=$form->end() ?>