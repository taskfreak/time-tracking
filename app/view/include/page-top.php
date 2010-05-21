<?php
if (isset($this->current)) {
?>
<form id="drun" action="/task/timer"<?php
	if ($this->current) {
		echo ' class="running"';
	}
	?> method="post">
	<?php
	$this->incView('include/timer');
	?>
</form>
<?php
}
?>
<div id="global">
	<div id="dtop">
		<h1><a href="/">TaskFreak</a></h1>
		<div id="duser">
		<?php
		if (APP_SETUP_USER_MODEL && $this->fc->user->isLoggedIn()) {
			echo $this->fc->user->html('nickname');
			if ($this->fc->getSessionVariable('switch_id') != $this->fc->user->getUid()) {
				echo ' as '.varStr::html($this->fc->getSessionVariable('switch_name'));
			}
			echo '<br /><small>';
			if ($this->fc->user->checkAcl('task_see_all')) {
				echo '<a href="'.APP_WWW_URI.'admin/switch" class="ajax box">switch</a> | ';
			}
			if ($this->fc->user->checkAcl('admin_user')) {
				echo '<a href="'.APP_WWW_URI.'admin">admin</a> | ';
			} else {
				echo '<a href="'.APP_WWW_URI.'admin/edit" class="ajax box">profile</a> | ';
			}
			echo '<a href="'.APP_WWW_URI.'login/out">logout</a>';
			echo '</small>';
		} else {
			echo 'TaskFreak!<br /><small>Time Tracking</small>';
		}
		?>
		</div>
	</div>
	<div id="dwork">
