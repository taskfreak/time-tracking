<?php
$this->incView('include/page-top', false);

// echo $this->data->iAutoForm('task_edit','post','','top');
echo $this->data->iForm('task_edit','post');
echo $this->data->iHidden('id');
?>
<ol class="fields multicol top">
	<?php
	echo $this->data->iFieldLabelled('title','','','li class="nline"');
	// $this->data->iFieldLabelled('begin'); -TODO- unused ATM
	echo $this->data->iFieldLabelled('deadline','','','li class="nline"');
	?>
	<li>
		<label for="i_priority">priority</label>
		<?php echo $this->data->iSelect('priority'); ?>
	</li>
	<?php
	echo $this->data->iFieldLabelled('note','','','li class="nline"');
	?>
	<li class="nline inline">
		<label for="i_status">status</label> :
		<?php echo $this->data->iSelect('status'); ?>
	</li>
	<li class="inline">
		<?php echo $this->data->iCheckBox('archived'); ?>
		<label for="i_archived">mark task as archived</label>
	</li>
	<li class="nline buttons">
		<button type="submit" name="save" value="1" class="save">Save Task</button>
	</li>
</ol>
<?php
$this->incView('include/page-bot', false);
