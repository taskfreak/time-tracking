<div class="info">
		<?php 
			echo $this->fc->user->html('nickname');
			/*
			| <a href="#login" class="logout slideup"><?php TR::phtml('security','logout'); ?></a>
			*/
		?>
		<br /><small><?php echo VarDtm::html(APP_SQL_NOW,APP_DATETIME_LNX); ?></small>
	</div>