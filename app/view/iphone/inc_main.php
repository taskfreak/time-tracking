<div id="main" class="dyn">
	<div class="toolbar">
		<h1>TaskFreak!</h1>
		<a href="/index.php?c=iphone&a=logout" class="logout out"><?php TR::phtml('security','logout'); ?></a>
	</div>
	<?php
	if ($this->current) {
		$this->incView('iphone/inc_timer');
	}
	?>
	<h2>By Status</h2>
	<ul class="rounded">
		<li class="arrow"><a href="#list-st0"><?php TR::phtml('pages','todo'); ?></a><small class="counter"><?php echo $this->dataTodo->count(); ?></small></li>
		<li class="arrow"><a href="#list-st1"><?php TR::phtml('pages','done'); ?></a><small class="counter"><?php echo $this->dataDone->count(); ?></small></li>
		<li class="arrow"><a href="#list-st2"><?php TR::phtml('pages','valid'); ?></a><small class="counter"><?php echo $this->dataValid->count(); ?></small></li>
	</ul>
	<?php
	/*
	if (count($this->dataTags)) {
	?>
	<h2>By Tag</h2>
	<ul class="rounded">
	<?php
		foreach ($this->dataTags as $row) {
			$tag = trim($row['tag']);
			echo '<li class="arrow"><a href="'
				.$this->fc->getUrl('iphone','list',array('tag'=>$tag))
				.'">'.VarStr::html($tag).'</a></li>';
		}
	?>
	</ul>
	<?php
	}
	*/
	$this->incView('iphone/inc_foot');
	?>
</div>