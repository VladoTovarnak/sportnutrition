<h2><span>Aktuality</span></h2>
<br/>
<?php if (empty($news)) { ?>
<p><em>Nemáme pro Vás žádné aktuality.</em></p>
<?php } else {
		foreach ($news as $actuality) { ?>
<div class="actuality">
	<h3><?php echo $this->Html->link($actuality['News']['title'], array('controller' => 'news', 'action' => 'view', $actuality['News']['id']))?></h3>
	<p><?php echo $actuality['News']['first_sentence']?></p>
	<span class="date"><?php echo $actuality['News']['czech_date']?></span>
</div>
<?php	}
} ?>