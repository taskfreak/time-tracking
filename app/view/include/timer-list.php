<?php
$total = 0;
if ($this->data->get('spent')) {
?>
<table class="list">
	<thead>
		<tr>
			<th><?php TR::phtml('form','start'); ?></th>
			<th><?php TR::phtml('form','stop'); ?></th>
			<th><?php TR::phtml('task','spent'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		do {
			$params = array(
				'id'	=>	$this->data->getUid(),
				'start' =>	$this->data->get('start')
			);
			$total += $this->data->get('spent');
			echo '<tr id="tr_'.$this->data->get('id').'_'.VarDtm::strToUnix($this->data->get('start')).'">';
			// start
			echo '<td>'.$this->data->htmlBegin().'</td>';
			// stop
			echo '<td>'.$this->data->htmlEnd().'</td>';
			// time spent
			echo '<td>';
			echo '<a href="'.$this->fc->getUrl('timer','delete',$params).'" class="onhold ajax confirm" rel="tab2">'.TR::html('button','delete').'</a>';
			echo $this->data->getTimeSpent();
			echo '</td>';
			echo '</tr>';
		} while ($this->data->next());
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"><?php TR::phtml('ui','total'); ?></td>
			<td><?php echo TaskSummary::htmlTime($total); ?></td>
		</tr>
	</tfoot>
</table>
<?php
} else {
	echo '<p class="empty">'.TR::html('ui','history_empty').'</p>';
}
?>
<p class="empty"><a href="#tab3" onclick="tabber.show(3); return false;"><?php TR::phtml('ui','report_spent'); ?></a></p>