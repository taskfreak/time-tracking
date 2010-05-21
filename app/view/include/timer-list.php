<?php
$total = 0;
if ($this->data->get('spent')) {
?>
<table class="list">
	<thead>
		<tr>
			<th>start</th>
			<th>stop</th>
			<th>spent</th>
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
			echo '<a href="'.$this->fc->getUrl('timer','delete',$params).'" class="onhold ajax confirm" rel="tab2">delete</a>';
			echo $this->data->getTimeSpent();
			echo '</td>';
			echo '</tr>';
		} while ($this->data->next());
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">Total</td>
			<td><?php echo TaskSummary::htmlTime($total); ?></td>
		</tr>
	</tfoot>
</table>
<?php
} else {
	echo '<p class="empty">No timers yet</p>';
}
?>
<p class="empty"><a href="#tab3" onclick="tabber.show(3); return false;">Report more time spent</a></p>