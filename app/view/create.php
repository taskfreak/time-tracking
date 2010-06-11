<?php
$this->incView('include/page-top', false);
?>
<div id="sidepanel">
	<?php
		$this->fc->loadLangFile('help_multi_creation.php');
	?>
</div>
<?php
$hh = new HtmlFormHelper();
echo $hh->iForm('task_batch','post',$this->fc->thisUrl());
echo '<p>'.$hh->iTextArea('data').'</p>';
echo '<p><button type="submit" name="save" value="1" class="save">'.TR::html('button','create').'</button></p>';
echo '</form>';

$this->incView('include/page-bot', false);