<?php
$this->incView('include/page-top', false);
?>
<form action="<?php echo $this->fc->getUrl('login'); ?>" method="post">
	<?php
	if ($str = $this->fc->user->getAuthError()) {
		echo '<p>'.TR::html('error','login_failed').': '.TR::html('error',$str).'</p>';
	}
	?>
	<ol class="fields side">
		<li>
			<label for="i_username"><?php TR::phtml('form','username'); ?></label>
			<input id="i_username" type="text" name="username" value="<?php echo $this->fc->user->value('username'); ?>" />
		</li>
		<li>
			<label for="i_password"><?php TR::phtml('form','password'); ?></label>
			<input id="i_password" type="password" name="password" value="" />
		</li>
		<li class="buttons">
			<button type="submit" name="login" value="1" class="submit"><?php TR::phtml('button','login'); ?></button>
		</li>
	</ol>
</form>
<?php
if ($GLOBALS['config']['log_debug'] == 2) {
	$this->fc->user->htmlAllErrors();
	echo $this->fc->user;
}

$this->incView('include/page-bot', false);