<form id="details" action="<?php echo $this->fc->getUrl('iphone','detail'); ?>" method="post">
	<div class="toolbar">
		<h1>Task details</h1>
		<a href="#" class="back">Back</a>
		<a href="#edit" class="modify flip">Edit</a>
	</div>
	<ul class="edgetoedge">
		<li id="dv_title">...</li>
		<li id="dv_note" class="note">...</li>
	</ul>
	<ul class="rounded">
		<li>
			Deadline
			<small id="dv_deadline">...</small>
		</li>
		<li>
			Priority
			<small id="dv_priority">...</small>
		</li>
		<li class="arrow">
			<small id="dv_spent">...</small>
			<a href="#details-spent">Time spent</a>
		</li>
	</ul>
	<p>
		<input id="dv_id" type="hidden" name="id" value="" />
		<input id="dv_action" type="hidden" name="action" value="" />
		<span id="dv_stop" class="hide"><a href="#" class="react darkButton"><?php TR::phtml('button','stop'); ?></a></span>
		<span id="dv_start" class="hide"><a href="#" class="react lightButton"><?php TR::phtml('button','start'); ?></a></span>
	</p>
	<?php
		$this->incView('iphone/inc_foot');
	?>
</form>
<div id="details-spent">
	<div class="toolbar">
		<h1>Task timesheet</h1>
		<a href="#" class="back">Back</a>
		<!-- a href="" class="button slideup">+</a -->
	</div>
	<ul class="rounded">
	</ul>
	<?php
	$this->incView('iphone/inc_foot');
	?>
</div>