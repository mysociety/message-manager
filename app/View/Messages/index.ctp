<div class="mm-messages">
	<h2>Messages</h2>
	<table>
	    <tr>
	        <th><?php echo $this->Paginator->sort('created');?></th>
	        <th><?php echo $this->Paginator->sort('status');?></th>
	        <th><?php echo $this->Paginator->sort('source');?></th>
	        <th><?php echo $this->Paginator->sort('MSISDN');?></th>
	        <th><?php echo $this->Paginator->sort('tag');?></th>
	        <th><?php echo $this->Paginator->sort('message');?></th>
	        <th> </th>
	    </tr>

	    <!-- Here is where we loop through our $posts array, printing out post info -->

	    <?php $c_locks = 0; foreach ($messages as $message): ?>
	    <tr>
	        <td><?php echo h($message['Message']['created']); ?></td>
	        <td class="status-<?php echo h($message['Status']['name']); ?>">
				<?php if (! empty($message['Message']['lock_expires'])) {
					echo('<abbr title="locked by: ' . h($message['Lockkeeper']['username']) . '">' . h($message['Status']['name']) . ' *</abbr>');
					$c_locks++;
				} else { 
					echo h($message['Status']['name']);
				} ?>
				</td>
	        <td><?php echo h($message['Source']['name']); ?></td>
	        <td>
	            <?php echo h($message['Message']['msisdn']); ?>
	        </td>
	        <td><?php echo h($message['Message']['tag']); ?></td>
	        <td><?php echo h($message['Message']['message']); ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('action' => 'view', $message['Message']['id']), null); ?>
			</td>
	    </tr>
	    <?php endforeach; ?>

	</table>
	<p class="pagination-legend">
	<?php
		if ($c_locks > 0) {
			echo '* record may be locked</p><p class="pagination-legend">';
		}
		echo $this->Paginator->counter(array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
	?>
	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
	<div class="actions">
		<ul>
			<?php if ($c_locks > 0) { ?>
				<li>
					<?php echo $this->Form->postLink(__('Purge all locks'), array('action' => 'purge_locks')); ?> 
				</li>
			<?php } ?>
		</ul>		
	</div>
</div>
