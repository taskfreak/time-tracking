<?php
if ($this->fc->user->isLoggedIn()) {
	$this->incView('iphone/inc_main');
	$this->incList('list-st0',TR::html('pages','todo'),$this->dataTodo);
	$this->incList('list-st1',TR::html('pages','done'),$this->dataDone);
	$this->incList('list-st2',TR::html('pages','valid'),$this->dataValid);
}
?>
<form id="login" action="<?php echo $this->fc->getUrl('iphone','login'); ?>" method="post" class="<?php
	if (!$this->fc->user->isLoggedIn()) {
		echo 'current';
	}
?>">
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
		<li><input type="text" name="password" value="" placeholder="<?php TR::phtml('form','password'); ?>" /></li>
	</ul>
	<p><a href="#" class="login lightButton"><?php TR::phtml('button','login'); ?></a></p>
	<div class="info bottom">
		TaskFreak! Time Tracking v0.5
	</div>
</form>
<?php
$this->incView('iphone/edit');
$this->incView('iphone/view');
?>
