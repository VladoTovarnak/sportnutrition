﻿<h2>Úprava kategorie - <?php echo $form->value('Category.name')?>(ID:<?php echo $form->value('Category.id')?>)</h2>
<?php echo $form->create('Category');?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název kategorie</td>
		<td>
			<?=$form->text('name', array('size' => 60))?>
		</td>
	</tr>
	<tr>
		<td>Nadpis</td>
		<td><?php echo $form->input('Category.heading', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>Breadcrumb</td>
		<td><?php echo $form->input('Category.breadcrumb', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>Popis</td>
		<td><?php echo $form->input('Category.content', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
	</tr>
	<tr>
		<td>Veřejná</td>
		<td><?php echo $this->Form->input('Category.public', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Aktivní</td>
		<td><?php echo $this->Form->input('Category.active', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			----------- níže uvedené nevyplňujte -----------
		</td>
	</tr>
	<tr>
		<td>Titulek</td>
		<td><?=$form->text('title', array('size' => 60))?></td>
	</tr>
	<tr>
		<td>Popisek</td>
		<td><?=$form->textarea('description', array('cols' => '50'))?></td>
	</tr>
	<tr>
		<td>
			URL
		</td>
		<td>
			<?=$form->text('url', array('size' => 60))?>
		</td>
	</tr>
</table>
<?php echo $form->hidden('Category.id'); ?>
<?php echo $form->end('Upravit');?>