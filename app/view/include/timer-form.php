<?php
echo $this->timer->iForm('task_timer','post',APP_WWW_URI.'timer/main');
?>
<input type="hidden" name="id" value="<?php echo $this->data->getUid(); ?>" />
<ol class="fields multicol side">
	<li class="nline">
		<label>date</label>
		<input type="text" id="i_date" name="date" class="date" value="<?php echo VarDte::value(APP_SQL_TODAY); ?>" />
	</li>
	<li class="nline">
		<label>start</label>
		<input type="text" id="i_start_time" name="start_time" class="time" />
	</li>
	<li>
		<label>stop</label>
		<input type="text" id="i_stop_time" name="stop_time" class="time" />
	</li>
	<li class="nline">
		<label>spent</label>
		<input type="text" id="i_spent" name="spent" class="duration" value="00:00" />
	</li>
	<li class="nline buttons">
		<button type="submit" name="save" value="1" class="save">Save report</button>
		<a href="javascript:document.task_timer.reset()">reset</a>
	</li>
</ol>
<hr class="clear" />
<div class="help">
	<p>Enter either :</p>
	<ul>
		<li>A start time only (will calculate time from then until now)</li>
		<li>A time spent only (will consider you've just finished the task)</li>
		<li>A start time and an stop time</li>
		<li>A start time and a time spent</li>
		<li>A stop time and a time spent</li>
	</ul>
</div>