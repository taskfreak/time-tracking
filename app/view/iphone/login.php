<form id="login" action="<?php echo $this->fc->thisUrl(); ?>" method="post" class="current slideup">
	<div class="toolbar">
		<h1>TaskFreak!</h1>
	</div>
	<ul class="form rounded">
		<?php
		if ($str = $this->fc->user->getAuthError()) {
			echo '<li class="error">'.TR::html('error',$str).'</li>';
		}
		?>
		<li><input type="text" name="username" value="<?php echo $this->fc->user->value('username'); ?>"  placeholder="<?php TR::phtml('form','username'); ?>" /></li>
		<li><input type="text" name="password" value=""  placeholder="<?php TR::phtml('form','password'); ?>" /></li>
	</ul>
	<p><button type="submit" name="login" class="darkButton submit slideup"><?php TR::phtml('button','login'); ?></button></p>
	<div class="info">
		TaskFreak! Time Tracking v0.5
	</div>
</form>