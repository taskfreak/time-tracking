<?php
if (isset($this->current)) {

	echo '<form id="drun" action="'.$this->fc->getUrl('task','timer').'"';
	if ($this->current) {
		echo ' class="running"';
	}
	echo ' method="post">';

	$this->incView('include/timer');
	
	echo '</form>';
}
?>
<div id="global">
	<div id="dtop">
		<h1><a href="<?php echo APP_WWW_URI; ?>">TaskFreak</a></h1>
		<div id="duser">
		<?php
		if (APP_SETUP_USER_MODEL && $this->fc->user->isLoggedIn()) {
			echo $this->fc->user->html('nickname');
			if ($this->fc->getSessionVariable('switch_id') != $this->fc->user->getUid()) {
				echo ' as '.varStr::html($this->fc->getSessionVariable('switch_name'));
			}
			echo '<br /><small>';
			if ($this->fc->user->checkAcl('task_see_all')) {
				echo '<a href="'.$this->fc->getUrl('admin','switch').'" class="ajax box">switch</a> | ';
			}
			if ($this->fc->user->checkAcl('admin_user')) {
				echo '<a href="'.$this->fc->getUrl('admin').'">admin</a> | ';
			} else {
				echo '<a href="'.$this->fc->getUrl('admin','edit').'" class="ajax box">profile</a> | ';
			}
			echo '<a href="'.$this->fc->getUrl('login','out').'">logout</a>';
			echo '</small>';
		} else {
			echo 'TaskFreak!<br /><small>Time Tracking</small>';
		}
		?>
		</div>
	</div>
	<div id="dwork">
