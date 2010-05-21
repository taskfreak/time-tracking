<?php
if ($this->current) {
?>
	<div id="timerstatus">
	<?php
	if ($this->current->isEmpty('stop')) {
		// task is running
	?>
		<!-- button type="submit" name="pause" value="1" class="submit" tabindex="2">pause</button -->
		<button type="submit" name="stop" value="1" class="submit" tabindex="2">stop</button>
		<button type="submit" name="close" value="1" class="warn" tabindex="3" onclick="return confirm('mark this task as done ?')">done</button>
	<?php
	} else {
		// task is paused (requested from ajax only)
	?>
		<button type="submit" name="resume" value="1" class="submit" tabindex="2">resume</button>
		<button type="submit" name="stop" value="1" class="warn" tabindex="3">stop</button>
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
		<button type="submit" name="save" value="1" class="saveadd" tabindex="2">Save</button>
		<button type="submit" name="start" value="1" class="save" tabindex="3">Start</button>
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