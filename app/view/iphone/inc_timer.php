<form id="running" action="<?php echo $this->fc->getUrl('iphone','home'); ?>" method="post" class="highlight">
	<input type="hidden" name="id" value="<?php echo $this->current->getUid(); ?>" />
	<input type="hidden" id="runaction" name="action" value="" />
	<h3><a href="#details" onclick="app.view(<?php echo $this->current->getUid(); ?>)" class="pop"><?php echo $this->current->html('title'); ?></a></h3>
	<?php
	if ($this->current->isEmpty('stop')) {
		// task is running
	?>
	<p class="triple">
		<span><a id="b_pause" href="#pause" class="lightButton action"><?php TR::phtml('button','pause'); ?></a></span>
		<span><a id="b_stop" href="#stop" class="darkButton action"><?php TR::phtml('button','stop'); ?></a></span>
		<span><a id="b_close" href="<?php echo $this->fc->getUrl('iphone','main',array('id'=>$this->current->getUid(),'action'=>'close')); ?>" class="blackButton action" onclick="return confirm('<?php TR::phtml('ui','done_confirm'); ?>')"><?php TR::phtml('button','done'); ?></a></span>
	</p>
	<?php
	} else {
		// task is paused (requested from ajax only)
	?>
	<p class="dual">
		<span><a id="b_resume" href="#resume" class="lightButton action"><?php TR::phtml('button','resume'); ?></a></span>
		<span><a id="b_stop" href="#stop" class="darkButton action"><?php TR::phtml('button','stop'); ?></a></span>
	</p>
	<?php
	}
	?>
	<script type="text/javascript">
	jQuery(function () {
		$(".action").tap(function (e) {
  			return app.taskAction(this);
		});
	});
	</script>
</form>