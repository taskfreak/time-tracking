<?php
if ($this->current) {
?>
	<div id="timerstatus">
	<?php
	if ($this->current->isEmpty('stop')) {
		// task is running
	?>
		<!-- button type="submit" id="b_pause" name="pause" value="1" class="submit" tabindex="2">pause</button -->
		<button type="submit" id="b_stop" name="stop" value="1" tabindex="2"><?php TR::phtml('button','stop'); ?></button>
		<button type="submit" id="b_close" name="close" value="1" tabindex="3" onclick="return confirm('<?php TR::phtml('ui','done_confirm'); ?>')"><?php TR::phtml('button','mark_done'); ?></button>
	<?php
	} else {
		// task is paused (requested from ajax only)
	?>
		<button type="submit" id="b_resume" name="resume" value="1" tabindex="2"><?php TR::phtml('button','resume'); ?></button>
		<button type="submit" id="b_stop" name="stop" value="1" tabindex="3"><?php TR::phtml('button','stop'); ?></button>
	<?php
	}
	?>
	</div>
	<p>
		<input type="hidden" name="id" value="<?php echo $this->current->getUid(); ?>" />
		<input type="hidden" id="i_timer" name="timer" value="<?php echo $this->current->getRealSpentSecs(); ?>" />
		<span id="dtimer"><?php echo $this->current->getRealSpent(); ?></span>
		<?php echo $this->current->html('title'); ?>
	</p>
<?php
} else {
?>
	<div id="timerstatus">
		<button type="submit" id="b_save" name="save" value="1" tabindex="2"><?php TR::phtml('button','save'); ?></button>
		<button type="submit" id="b_start" name="start" value="1" tabindex="3"><?php TR::phtml('button','save_and_start'); ?></button>
	</div>
	<p>
		<input type="text" name="title" value="" tabindex="1" />
	</p>
<?php
}
if (isset($this->jsCode)) {
	echo '<script type="text/javascript">';
	echo $this->jsCode;
	echo '</script>';
}
?>