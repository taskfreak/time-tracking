<?php
$this->incView('include/page-top', false);

// echo $this->data->iAutoForm('task_edit','post','','top');
echo $this->data->iForm('task_edit','post');
echo $this->data->iHidden('id');
?>
<ol class="fields multicol top">
	<?php
	echo $this->data->iFieldLabelled('title',TR::html('form','title'),'','li id="f_title" class="nline"');
	// $this->data->iFieldLabelled('begin'); -TODO- unused ATM
	echo $this->data->iFieldLabelled('deadline',TR::html('form','deadline'),'','li class="nline"');
	?>
	<li>
		<label for="i_priority"><?php TR::phtml('task','priority'); ?></label>
		<?php echo $this->data->iSelectTranslate('priority','priority'); ?>
	</li>
	<?php
	echo $this->data->iFieldLabelled('note',TR::html('task','note'),'','li class="nline"');
	?>
	<li class="nline inline">
		<label for="i_status"><?php TR::phtml('form','status'); ?></label> :
		<?php echo $this->data->iSelectTranslate('status','task'); ?>
	</li>
	<li class="inline">
		<?php echo $this->data->iCheckBox('archived'); ?>
		<label for="i_archived"><?php TR::phtml('ui','mark_archived'); ?></label>
	</li>
	<li class="nline buttons">
		<button type="submit" name="save" value="1" class="save"><?php TR::phtml('button','save_task'); ?></button>
	</li>
</ol>
<script type="text/javascript" src="<?php echo APP_WWW_URI.'asset/js/jquery.jdpicker.js'; ?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#f_task_edit').submit(function() {
	  if ($.trim($('#i_title').val())) {
	  	return true;
	  } else {
	  	$('#f_title').addClass('error');
	  	$('#i_title').focus();
	  	return false;
	  }
	});
});
</script>
<?php
$this->incView('include/page-bot', false);
