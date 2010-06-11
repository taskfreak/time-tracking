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
			echo '<p><a href="javascript:showmenu()">'.$this->fc->user->html('nickname');
			if ($this->fc->getSessionVariable('switch_id') != $this->fc->user->getUid()) {
				echo ' as '.varStr::html($this->fc->getSessionVariable('switch_name'));
			}
			echo '<br /><small>'.TR::html('ui','user_menu').'</small></a></p>';
			echo '<ul id="dmenu">';
			if ($this->fc->user->checkAcl('task_see_all')) {
				echo '<li><a href="'.$this->fc->getUrl('admin','switch').'" class="ajax box">'.TR::html('ui','switch').'</a></li>';
			}
			echo '<li><a href="'.$this->fc->getUrl('admin','edit',array('id'=>$this->fc->user->getUid())).'" class="ajax box">'.TR::html('security','my_account').'</a></li>';
			if ($this->fc->user->checkAcl('admin_user')) {
				echo '<li><a href="'.$this->fc->getUrl('admin').'">'.TR::html('ui','admin').'</a></li>';
			}
			echo '<li><a href="'.$this->fc->getUrl('login','out').'">'.TR::html('security','logout').'</a></li>';
			echo '</ul>';
		} else {
			echo 'TaskFreak!<br /><small>Time Tracking</small>';
		}
		?>
		</div>
	</div>
	<div id="dwork">
