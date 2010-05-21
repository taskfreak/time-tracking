<?php
$this->incView('include/page-top', false);

// echo $this->data->iAutoForm('user_edit','post','','top');
echo '<h1>';
if ($id = $this->data->getUid()) {
	if ($id == $this->fc->user->getUid()) {
		echo 'My preferences';
	} else {
		echo 'Edit a user';
	}
} else {
	echo 'Create new user';
}
echo '</h1>';


echo $this->data->iForm('user_edit','post');
echo $this->data->iHidden('id');
?>
<ol class="fields side">
	<?php
	echo $this->data->iFieldLabelled('nickname','','','li class="compulsory"');
	echo $this->data->iFieldLabelled('email');
	?>
	<li>
		<label for="i_time_zone">time zone</label>
		<?php echo $this->data->iTimeZone('time_zone'); ?>
	</li>
	<?php
	echo $this->data->iFieldLabelled('username','','','li class="compulsory"');
	?>
	<li>
		<label for="i_pass1">password</label>
		<?php echo $this->data->iPass('pass1', false); ?>
	</li>
	<li>
		<label for="i_pass2">(repeat)</label>
		<?php echo $this->data->iPass('pass2', false); ?>
	</li>
	<?php
	if ($this->data->getUid() != $this->fc->user->getUid()) {
		// can only change rights for other users
	?>
	<li>
		<label>user rights</label>
		<ul>
			<li><input type="checkbox" id="i_acl_task_see_all" name="acl_task_see_all" value="1"<?php
				if ($this->data->checkAcl('task_see_all')) echo ' checked="checked"';
			?> /> <label for="i_acl_task_see_all">task manager</label></li>
			<li><input type="checkbox" id="i_acl_admin_user" name="acl_admin_user" value="1"<?php
				if ($this->data->checkAcl('admin_user')) echo ' checked="checked"';
			?> /> <label for="i_acl_admin_user">user admin</label></li>
		</ul>
	</li>
	<?php
	} 
	?>
	<li class="buttons">
		<button type="submit" name="save" value="1" class="save">Save</button>
		<?php
		if ($this->canDeleteThisUser) {
		?>
		<a href="<?php echo $this->fc->getUrl('admin','delete',array('id'=>$this->data->getUid())); ?>"
			onclick="return confirm('really delete the dude ?');" class="button marge delete">Delete</a>
		<?php
		}
		?>
	</li>
</ol>
<?php

$this->incView('include/page-bot', false);
 