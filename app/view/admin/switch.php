<?php
$this->incView('include/page-top', false);

?>
<h1><?php TR::phtml('ui','select_user'); ?></h1>
<ul>
<?php
while ($this->data->next()) {
	echo '<li>';
	if ($this->data->getUid() == $this->switch_id) {
		echo $this->data->html('nickname');
	} else {
		echo '<a href="'.$this->fc->getUrl('admin','switch',array('id'=>$this->data->getUid())).'">'.$this->data->html('nickname').'</a>';
	}
	echo '</li>';
}
?>
</ul>
<?php
$this->incView('include/page-bot', false);
