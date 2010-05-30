<?php
$this->incView('include/page-top', false);
?>
<form action="<?php echo $this->fc->getUrl('login'); ?>" method="post">
	<?php
	if ($str = $this->fc->user->getAuthError()) {
		echo '<p>Can not login: '.VarStr::html($str).'</p>';
	}
	?>
	<ol class="fields side">
		<li>
			<label for="i_username">Username</label>
			<?php echo $this->fc->user->iText('username'); ?>
		</li>
		<li>
			<label for="i_password">Password</label>
			<?php echo $this->fc->user->iPass('password'); ?>
		</li>
		<li class="buttons">
			<button type="submit" name="login" value="1" class="submit">Login</button>
		</li>
	</ol>
</form>
<?php
if ($GLOBALS['config']['log_debug'] == 2) {
	$this->fc->user->htmlAllErrors();
	echo $this->fc->user;
}

$this->incView('include/page-bot', false);