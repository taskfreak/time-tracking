<?php
$this->incView('include/page-top', false);

?>
<div id="dmain" class="full">
	<div id="dfilters">
		<span>
			<a href="<?php echo $this->fc->getUrl('admin/edit');?>" class="ajax box new inv" title="<?php TR::phtml('ui','create_user'); ?>"><?php TR::phtml('ui','create_user'); ?></a>
		</span>
		<ul class="links horiz">
			<?php
			$arr = array('all'=>0,'task managers'=>1,'user admins'=>2);
			foreach($arr as $lbl => $val) {
				echo '<li';
				if ($this->filter == $val) {
					echo ' class="active"';
				}
				echo '><a href='.$this->fc->thisUrl(array('userfilter'=>$val)).'>'.$lbl.'</a></li>';
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
				echo '><a href='.$this->fc->thisUrl(array('userlimit'=>$val)).'>'.$lbl.'</a></li>';
			}
			?>
		</ul>
		<form id="search" action="<?php $this->fc->thisUrl(); ?>" method="get">
			<p>
				<input type="text" name="search" value="<?php echo $this->search; ?>" tabindex="4" />
				<button type="submit" name="go" value="1"><?php TR::phtml('data','search'); ?></button>
				<button type="button" onclick="this.form.elements[0].value='';this.form.submit()">x</button>
			</p>
		</form>
	</div>
	<div id="dlist" class="users">
	<?php
	if ($this->data->count()) {
	?>
	<table>
	<thead>
		<tr>
			<th><?php TR::phtml('form','name'); ?></th>
			<th><?php TR::phtml('form','last_visit_date'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	while ($this->data->next()) {
		$id = $this->data->getUid();
		echo '<tr>';
		echo '<td>';
		echo '<a href="'.$this->fc->getUrl('admin','edit',array('id'=>$id)).'" class="onhold ajax box" title="'
			.TR::html('button','edit').'">'.TR::html('button','edit').'</a>';
		echo $this->data->html('nickname');
		if ($tmp = $this->data->htmlRights()) {
			echo ' <small>'.$tmp.'</small>';
		}
		echo '</td>';
		echo '<td>'.$this->data->html('last_login_date').'</td>';
		echo '</tr>';
	}
	?>
	</tbody>
	</table>
	<?php
	} else {
	?>
	<p class="empty"><?php TR::html('data','search_empty'); ?></p>
	<?php
	}
	?>
	</div>
</div>
<?php


$this->incView('include/page-bot', false);
