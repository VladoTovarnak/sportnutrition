<h1>Správa bannerů na hlavní stránce</h1>
<table class="tabulka">
	<?php foreach ($banners as $index => $banner) { ?>
	<tr>
		<td rowspan="2" nowrap>
			<div style="float:left">
				<a href="/<?php echo $banner['HomepageBanner']['url']?>" target="_blank">
					<img src="/<?php echo $folder . '/'. $banner['HomepageBanner']['image']?>" width="334px" />
				</a>
			</div>
			<div style="float:left;margin:80px 0 0 10px;">
			<?php
			echo $this->Form->create('HomepageBanner', array('type' => 'file'));
			echo $this->Form->input('HomepageBanner.image', array('label' => false, 'type' => 'file'));
			echo $this->Form->hidden('HomepageBanner.id', array('value' => $banner['HomepageBanner']['id']));
			echo $this->Form->hidden('HomepageBanner.action', array('value' => 'change_image'));
			echo $this->Form->submit('Změnit obrázek');
			echo $this->Form->end();
			?>
			</div>
		</td>
		<td>
		<?php
			echo $this->Form->create('HomepageBanner', array('type' => 'file'));
			echo $this->Form->input('HomepageBanner.description', array('label' => false, 'type' => 'textarea', 'value' => $banner['HomepageBanner']['description'], 'style' => 'height:50px', 'id' => 'HomepageBanner' . $index . 'Description'));
			echo $this->Form->hidden('HomepageBanner.id', array('value' => $banner['HomepageBanner']['id']));
			echo $this->Form->hidden('HomepageBanner.action', array('value' => 'change_description'));
			echo $this->Form->submit('Změnit popis');
			echo $this->Form->end();
			?>
		</td>
		<td rowspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'homepage_banners', 'action' => 'move_up', $banner['HomepageBanner']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td rowspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'homepage_banners', 'action' => 'move_down', $banner['HomepageBanner']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	</tr>
	<tr>
		<td>
			<?php
			echo $this->Form->create('HomepageBanner', array('type' => 'file'));
			echo $this->Form->input('HomepageBanner.url', array('label' => false, 'value' => $banner['HomepageBanner']['url'], 'type' => 'text', 'size' => 75));
			echo $this->Form->hidden('HomepageBanner.id', array('value' => $banner['HomepageBanner']['id']));
			echo $this->Form->hidden('HomepageBanner.action', array('value' => 'change_url'));
			echo $this->Form->submit('Změnit URL');
			echo $this->Form->end();
			?>
		</td>
	</tr>
	<?php } ?>
</table>