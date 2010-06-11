<?php
echo $this->timer->iForm('task_timer','post',$this->fc->getUrl('timer','main'));
?>
<input type="hidden" name="id" value="<?php echo $this->data->getUid(); ?>" />
<ol class="fields multicol side">
	<li class="nline">
		<label><?php TR::phtml('form','date'); ?></label>
		<input type="text" id="i_date" name="date" class="date" value="<?php echo VarDte::value(APP_SQL_TODAY); ?>" />
	</li>
	<li class="nline">
		<label><?php TR::phtml('form','start'); ?></label>
		<input type="text" id="i_start_time" name="start_time" class="time" />
	</li>
	<li>
		<label><?php TR::phtml('form','stop'); ?></label>
		<input type="text" id="i_stop_time" name="stop_time" class="time" />
	</li>
	<li class="nline">
		<label><?php TR::phtml('task','spent'); ?></label>
		<input type="text" id="i_spent" name="spent" class="duration" value="00:00" />
	</li>
	<li class="nline buttons">
		<button type="submit" name="save" value="1" class="save"><?php TR::phtml('button','save_report'); ?></button>
		<a href="javascript:document.task_timer.reset()"><?php TR::phtml('button','reset'); ?></a>
	</li>
</ol>
<hr class="clear" />
<div class="help">
	<?php
		$this->fc->loadLangFile('help_timer_creation.php');
	?>
</div>