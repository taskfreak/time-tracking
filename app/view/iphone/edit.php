<form id="edit" action="<?php echo $this->fc->getUrl('iphone','edit'); ?>" method="post">
	<div class="toolbar">
		<h1>Edit task</h1>
		<a href="#" class="back">Cancel</a>
	</div>
	<ul class="edit rounded">
		<li><input id="i_title" type="text" name="title" placeholder="<?php TR::phtml('form','title'); ?>" value="" /></li>
		<li><textarea id="i_note" type="text" name="note" placeholder="<?php TR::phtml('task','note'); ?>"></textarea></li>
	</ul>
	<ul class="edit rounded">
		<li><input id="i_deadline" type="number" name="deadline" placeholder="<?php TR::phtml('form','deadline'); ?>" value="" /></li>
		<li><?php echo $this->edit->iSelectTranslate('priority','priority'); ?></li>
		<li><?php echo $this->edit->iSelectTranslate('status','task'); ?></li>
	</ul>
	<p>
		<input type="hidden" id="i_id" name="id" value="" />
		<a href="#" class="save lightButton"><?php TR::phtml('button','save'); ?></a>
	</p>
	<?php
	$this->incView('iphone/inc_foot');
	?>
</form>