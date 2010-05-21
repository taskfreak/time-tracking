<?php
if ($this->data->count()) {
	echo HtmlFormHelper::iForm('tasks');
?>
<table>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>deadline</th>
			<th>
				<small><?php echo $this->data->total(); ?> item(s) found</small>
				task
			</th>
			<th>start</th>
			<th>stop</th>
			<th>spent</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$arr = array();
	$i = $total = 0;
	$cid = ($this->current)?$this->current->getUid():0;
	while ($this->data->next()) {
		$this->data->chkDeadline();
		$id = $this->data->getUid();
		echo '<tr id="tr_'.$id.'"'
			.((!$this->filter)?$this->data->curCss(($cid == $id)?'current':''):'')
			.'>';
		echo '<td>';
			if (!in_array($id, $arr)) {
				echo '<input type="checkbox" id="chk_'.$i.'" name="chk[]" '
					.'value="'.$id.'" />';
				$i++;
				$arr[] = $id;
			} else {
				echo '<input type="checkbox" disabled="disabled" />';
			}
			$total += $this->data->get('spent');
		?></td>
			<td><?php echo $this->data->htmlDeadline(); ?></td>
			<td>
				<a href="<?php echo $this->fc->getUrl('task','edit',array('id'=>$id)); ?>" class="onhold ajax box" title="Edit task">edit</a>
				<?php
				// priority
				echo '<span class="prio pr'.$this->data->get('priority');
				echo '">'.$this->data->get('priority').'</span> ';
				// note
				echo '<a href="'.$this->fc->getUrl('task','view',array('id'=>$id)).'" ';
					if ($this->data->isEmpty('note')) {
						echo 'class="ajax box clickme"';
					} else {
						echo 'class="note ajax box clickme" ';
						echo 'title="'.$this->data->html('note',200).'"';
					}
				echo '>';
				// title
				echo $this->data->html('title');
				echo '</a>';
				?>
			</td>
			<td><?php echo $this->data->htmlBegin(); ?></td>
			<td><?php echo $this->data->htmlEnd(); ?></td>
			<td id="sts_<?php echo $id; ?>">
				<?php
				if ($this->data->isOpened($this->user_id)) {
					echo '<a href="'.$this->fc->getUrl('task','timer',array('id'=>$id)).'" '
						.'class="onhold clock ajax" title="start task" rel="drun">start</a>';
				}
				
				echo '<span>';
				if (!$this->expand && $cid == $id) {
					echo 'running';
				} else {
					echo $this->data->getTimeSpent();
				}
				echo '</span>';
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<a href="javascript:checkAll('f_tasks')">select all</a> |
				<?php
				foreach ($this->actions as $key => $label) {
					echo ' <button type="submit" name="'.$key.'" '
						.'value="1">'.VarStr::html($label).'</button>';
				}
				?>
			</td>
			<td colspan="2">TOTAL</td>
			<td><?php echo TaskSummary::htmlTime($total); ?></td>
		</tr>
	</tfoot>
</table>
</form>
<?php
} else {
	echo '<p class="empty">No Task found</p>';
}
