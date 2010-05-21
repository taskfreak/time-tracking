<?php
$this->incView('include/page-top', false);
?>
<div id="sidepanel">
	<p>Enter one task per line.</p>
	<h4>Task format</h4>
	<p>Task format : [date] [priority] [label :] task title<br />
	<small>fields within brackets are optional</small></p>
	<p><em>date</em> can be :</p>
	<ul>
		<li>a date in format dd/mm[/yy]</li>
		<li>a + followed by number of days in the future</li>
		<li>a - means no deadline</li>
		<li>nothing means deadline is today</li>
	</ul>
	<p><em>priority</em> is a number between 1 and 9, followed by a )<br />
	<small>&laquo; 1) &raquo; for urgent tasks, &laquo; 5) &raquo; for normal priority</small></p>
	<p><em>label</em> is optional</p>
	<h4>Defaults for multiple tasks</h4>
	<p>a line starting by a * sets up defaults</p>
	<p>Format is : * [date] [label]<br />
	<small>&laquo; * +1 taskfreak &raquo; or &laquo; * 12/04 &raquo;</small></p>
	<p>reset defaults with a line with &laquo; ** &raquo</p>
</div>
<?php
$hh = new HtmlFormHelper();
echo $hh->iForm('task_batch','post',$this->fc->thisUrl());
echo '<p>'.$hh->iTextArea('data').'</p>';
echo '<p><button type="submit" name="save" value="1" class="save">Save</button></p>';
echo '</form>';

$this->incView('include/page-bot', false);