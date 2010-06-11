<?php
$this->incView('include/page-top', false);
?>
<div id="dmain" class="full">
	<div id="dfilters">
		<span>
			<a href="<?php echo $this->fc->getUrl('task','edit');?>" class="ajax box new inv" title="<?php TR::phtml('ui','create_single'); ?>"><?php TR::phtml('ui','create_single'); ?></a>
			<a href="<?php echo $this->fc->getUrl('task','create');?>" class="ajax bigbox new multi inv" title="<?php TR::phtml('ui','create_multi'); ?>"><?php TR::phtml('ui','create_multi'); ?></a>
			<a href="<?php echo $this->fc->getUrl('task','main',array('ajax'=>1)); ?>" class="new reload inv" title="<?php TR::phtml('ui','reload_list'); ?>"><?php TR::phtml('ui','reload'); ?></a>
		</span>
		<ul class="links horiz">
			<?php
			$arr = array('todo'=>0,'done'=>1,'valid'=>2,'archived'=>3);
			foreach($arr as $lbl => $val) {
				echo '<li';
				if ($this->filter == $val) {
					echo ' class="active"';
				}
				echo '><a href='.$this->fc->thisUrl(array('filter'=>$val)).'>'.TR::html('task',$lbl).'</a></li>';
			}
			?>
		</ul>
		<ul class="links horiz">
			<?php
			$arr = array('compact'=>0,'expand'=>1);
			foreach($arr as $lbl => $val) {
				echo '<li';
				if ($this->expand == $val) {
					echo ' class="active"';
				}
				echo '><a href='.$this->fc->thisUrl(array('expand'=>$val)).'>'.TR::html('ui',$lbl).'</a></li>';
			}
			?>
		</ul>
		<ul class="links horiz">
			<?php
			$arr = $GLOBALS['config']['task']['pagination'];
			foreach($arr as $lbl => $val) {
				echo '<li';
				if ($this->limit == $val) {
					echo ' class="active"';
				}
				echo '><a href='.$this->fc->thisUrl(array('limit'=>$val)).'>'.(is_int($lbl)?$lbl:TR::html('data',$lbl)).'</a></li>';
			}
			?>
		</ul>
		<form id="search" action="<?php echo $this->fc->thisUrl(); ?>" method="get"<?php
			if ($this->search) { echo ' class="filled"'; }
		?>>
			<p>
				<?php
				if (!APP_URL_REWRITE) {
				?>
				<input type="hidden" name="c" value="<?php echo $this->fc->controller; ?>" />
				<input type="hidden" name="a" value="<?php echo $this->fc->action; ?>" />
				<?php
				}
				?>
				<input type="text" name="search" value="<?php echo $this->search; ?>" tabindex="4" />
				<button id="b_submit" type="submit" name="go" value="1"><?php TR::phtml('data','search'); ?></button>
				<button id="b_clear" type="button" onclick="this.form.elements['search'].value='';this.form.submit()">x</button>
			</p>
		</form>
	</div>
	<div id="dlist" class="tasks">
	<?php
		if ($this->expand) {
			$this->incView('include/list-expand');
		} else {
			$this->incView('include/list-compact');
		}
	?>
	</div>
</div>
<?php
$this->incView('include/page-bot', false);
