<?php
// generate timesheet
$total = 0;
$str ='';
if ($this->data->get('spent')) {
	$str = '<ul class="rounded">';
	do {
		$total += $this->data->get('spent');
		$str .= '<li>';
		// start and stop times
		$str .= $this->data->htmlTimes();
		// time spent
		$str .= '<small>'.$this->data->getTimeSpent().'</small>';
		$str .= '</li>';
	} while ($this->data->next());
	$str .= '<li>'.TR::html('ui','total').'<small>'.TaskSummary::htmlTime($total).'</small></li>';
	$str .= '</ul>';
}
?>

<form id="details-<?php echo $this->data->getUid(); ?>" action="<?php echo $this->fc->getUrl('iphone','detail'); ?>" method="post">
	<div class="toolbar">
		<h1>Task details</h1>
		<a href="#" class="back">Back</a>
		<a href="<?php echo $this->fc->getUrl('iphone','edit',array('id'=>$this->data->getUid())); ?>" class="modify flip">Edit</a>
	</div>
	<ul class="edgetoedge">
		<li><?php echo $this->data->html('title'); ?></li>
		<?php
		if (!$this->data->isEmpty('note')) {
		?>
		<li class="note"><?php echo $this->data->html('note'); ?></li>
		<?php
		}
		?>
	</ul>
	<ul class="rounded">
		<li>
			Deadline
			<small><?php echo $this->data->htmlDeadline(); ?></small>
		</li>
		<li>
			Priority
			<small><?php echo $this->data->htmlPriority(); ?></small>
		</li>
		<li class="arrow">
			<small><?php echo TaskSummary::htmlTime($total); ?></small>
			<a href="#details-<?php echo $this->data->getUid(); ?>-spent">Time spent</a>
		</li>
	</ul>
	<p>
		<input type="hidden" name="id" value="<?php echo $this->data->getUid(); ?>" />
		
		<?php
		if ($this->current && ($this->current->getUid() == $this->data->getUid())) {
			// task is currently running
			echo '<input type="hidden" name="action" value="stop" />';
			echo '<a href="#" class="react darkButton">'.TR::html('button','stop').'</a>';
		} else {
			// not running, wanna start ?
			echo '<input type="hidden" name="action" value="start" />';
			echo '<a href="#" class="react lightButton">'.TR::html('button','start').'</a>';
		}
		?>
	</p>
	<script>
	jQuery(function () {
		$(".react").tap(function (e) {
  			return app.viewAction(this);
		});
		console.log('wiired');
		$("#details-<?php echo $this->data->getUid(); ?> a.modify").css('color','#f00').click(function(e){
			console.log('edit clicked');
			return app.editAction(this);
		});
		console.log('wiiiired');
	});
	</script>
	<?php
		$this->incView('iphone/inc_foot');
	?>
</form>
<div id="details-<?php echo $this->data->getUid(); ?>-spent">
	<div class="toolbar">
		<h1>Task timesheet</h1>
		<a href="#" class="back">Back</a>
		<a href="<?php echo $this->fc->getUrl('iphone','timer',array('id'=>$this->data->getUid())); ?>" class="button slideup">+</a>
	</div>
	<?php
	if ($str) {
		echo $str;
	} else {
		echo '<ul class="rounded"><li>'.TR::html('ui','history_empty').'</li></ul>';
	}
	$this->incView('iphone/inc_foot');
?>
</div>