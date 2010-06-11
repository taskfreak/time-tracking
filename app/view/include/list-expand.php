<?php
if ($this->data->count()) {

	// prepare array of data
	$arrData = array();
	$c = $d = $cid = -1;
	while ($this->data->next()) {
		if ($cid != $this->data->getUid()) {
			$c++;
			$d=0;
			$cid = $this->data->getUid();
		}
		$arrData[$c][$d] = clone($this->data);
		$d++;
	}

	// start list
	echo HtmlFormHelper::iForm('tasks');
	
?>
<table>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th<?php
				if ($this->order == 'deadline') echo ' class="active"';
			?>><a href="<?php echo $this->fc->getUrl('task','main',array('order'=>'deadline')); ?>"><?php TR::phtml('form','deadline'); ?></a></th>
			<th<?php
				if ($this->order == 'priority') echo ' class="active"';
			?>>
				<small><?php 
					$ct =  count($arrData);
					echo $ct.' '.TR::html('data',($ct>1)?'items_found':'item_found');
				?></small>
				<a href="<?php echo $this->fc->getUrl('task','main',array('order'=>'priority')); ?>"><?php TR::phtml('ui','task'); ?></a>
			</th>
			<th<?php
				if ($this->order == 'start') echo ' class="active"';
			?>><a href="<?php echo $this->fc->getUrl('task','main',array('order'=>'start')); ?>"><?php TR::phtml('form','start'); ?></a></th>
			<th<?php
				if ($this->order == 'stop') echo ' class="active"';
			?>><a href="<?php echo $this->fc->getUrl('task','main',array('order'=>'stop')); ?>"><?php TR::phtml('form','stop'); ?></a></th>
			<th<?php
				if ($this->order == 'spent') echo ' class="active"';
			?>><a href="<?php echo $this->fc->getUrl('task','main',array('order'=>'spent')); ?>"><?php TR::phtml('task','spent'); ?></a></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$arr = array();
	$i = $total = 0;
	
	$cid = ($this->current)?$this->current->getUid():0;
	
	for($j=0;$j<=$c;$j++) {
		$arrObj = $arrData[$j];
		$d = count($arrObj);
		$k=0;
		$str = '';
		$subtotal = 0;
		$obj = $arrObj[0];
		
		$id = $obj->getUid();
		
		// first loop to get total time spent on task
		foreach ($arrObj as $obj) {
			$subtotal += $obj->get('spent');
		}
		$total += $subtotal;
		
		$obj->chkDeadline();
		
		// first row displays task information
		echo '<tr id="tr_'.$id.'"'
			.((!$this->filter)?$obj->curCss(($d>1)?'noline':''):($d>1?' class="noline"':''))
			.'>';
			
			// checkbox
			echo '<td><input type="checkbox" id="chk_'.$i.'" name="chk[]" '
				.'value="'.$id.'" /></td>';
				
			// deadline
			echo '<td>'.$obj->htmlDeadline().'</td>';
			
			echo '<td>';
			// edit link
			echo '<a href="'.$this->fc->getUrl('task','edit',array('id'=>$id)).'" class="onhold ajax box" title="'
				.TR::html('ui','edit_task').'">'.TR::html('button','edit').'</a>';
			// priority
			echo '<span class="prio pr'.$obj->get('priority');
			echo '" title="'.$obj->htmlPriority().'">'.$obj->get('priority').'</span> ';
			// note
			echo '<a href="'.$this->fc->getUrl('task','view',array('id'=>$id)).'" ';
				if ($obj->isEmpty('note')) {
					echo 'class="ajax box clickme"';
				} else {
					echo 'class="note ajax box clickme" ';
					echo 'title="'.$obj->html('note',200).'"';
				}
			echo '>';
			// title
			echo $obj->html('title');
			echo '</a>';
			echo '</td>';
			
			// start and end
			if ($d>1) {
				// many timer, skip info here
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
			} else {
				echo '<td>'.$obj->htmlBegin().'</td>';
				echo '<td>'.$obj->htmlEnd().'</td>';
			}
			
			// subtotal
			echo '<td id="sts_'.$id,'">';
			if ($obj->isOpened($this->user_id)) {
				echo '<a href="'.$this->fc->getUrl('task','timer',array('id'=>$id)).'" '
						.'class="onhold clock ajax" title="'.TR::html('ui','start_task').'" rel="drun">'.TR::html('button','start').'</a>';
			}
			echo TaskSummary::htmlTime($subtotal);
			echo '</td>';
			
		echo '</tr>';
		
		if ($d == 1) {
			// no timers defined, skip to next task
			continue;
		}
		
		// second loop to display timers
		foreach ($arrObj as $obj) {
			$id = $obj->getUid();
			$k++;
			echo '<tr id="tr_'.$id.'_'.$k.'" class="timer'
				.(($k<$d)?' noline':'')
				.'">';
			
			// checkbox
			echo '<td>&nbsp;</td>';
			// deadline
			echo '<td>&nbsp;</td>';
			// title
			echo '<td>&nbsp;</td>';
			// start
			echo '<td>'.$obj->htmlBegin().'</td>';
			// stop
			echo '<td>'.$obj->htmlEnd().'</td>';
			// time spent
			echo '<td>'.$obj->getTimeSpent().'</td>';
			
			echo '</tr>';
		}
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<a href="javascript:checkAll('f_tasks')"><?php TR::phtml('ui','select_all'); ?></a> |
				<?php
				foreach ($this->actions as $key => $label) {
					echo ' <button type="submit" name="'.$key.'" '
						.'value="1">'.TR::html('button',$label).'</button>';
				}
				?>
			</td>
			<td colspan="2"><?php TR::phtml('ui','total'); ?></td>
			<td><?php echo TaskSummary::htmlTime($total); ?></td>
		</tr>
	</tfoot>
</table>
</form>
<?php
} else {
	echo '<p class="empty">'.TR::html('error','search_empty').'</p>';
}
