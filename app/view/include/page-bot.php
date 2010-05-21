	</div>
</div>
<div id="dfoot">
	<div id="dcopy">
		<a href="http://www.taskfreak.com" target="_blank">TaskFreak</a>
		<span>TT v0.2</span>
	</div>
	<?php
	$err = false;
	$tmp = $this->fc->getHelper('messaging');
	$tmp = $tmp->getMessages($err);
	if ($tmp) {
		echo $tmp;
	} else {
		echo '...';
	}
	?>
</div>
