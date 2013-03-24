<?php if ($show_results && count($messages) == 0) { ?>
	<div id="flashMessage" class="message">No records found</div>
<?php } ?>

<div class="mm-messages">
	<h2><?php echo $title ?></h2>
	<div id="mini-search">
		<?php echo $this->Form->create('Message', array('type' => 'get', 'action' => 'search'));?>
			<div>
				<?php
					echo $this->Form->input('search_term', 
						array('name' => 'q', 'type' => 'text', 'escape' => false, 'label' => "Message contains", 'value' => $search_term));
				?>
			</div>
			<?php echo $this->Form->submit(__('Search')); ?>
		<?php echo $this->Form->end(); ?>
	</div>
	<?php if ($show_results) { ?>
		<table>
		    <tr>
		        <th><?php echo $this->Paginator->sort('created');?></th>
		        <th><?php echo $this->Paginator->sort('status');?></th>
		        <th><?php echo $this->Paginator->sort('source');?></th>
		        <th><?php echo $this->Paginator->sort('from_address');?></th>
		        <th><?php echo $this->Paginator->sort('tag');?></th>
		        <th><?php echo $this->Paginator->sort('message');?></th>
		        <th> </th>
		    </tr>

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
		            <?php echo h($message['Message']['from_address']); ?>
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
	<?php } ?>
</div>
