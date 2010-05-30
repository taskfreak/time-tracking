	</div>
</div>
<div id="dfoot">
	<div id="dcopy">
		<a href="http://www.taskfreak.com" target="_blank">TaskFreak</a>
		<span>TT v0.3</span>
	</div>
	<?php
	$err = false;
	$tmp = $this->fc->getHelper('messaging');
	$tmp = $tmp->getMessages($err);
	if ($tmp) {
		echo '<span id="message" class="'.(($err)?'error':'message').'">'.$tmp.'</span>';
	} else {
		echo '<span id="message">...</span>';
	}
	?>
</div>
