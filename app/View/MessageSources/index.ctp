<div class="mm-messagesources view">
	<h2><?php echo __('Message sources');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('name');?></th>
		<th><?php echo $this->Paginator->sort('description');?></th>
		<th><?php echo $this->Paginator->sort('user');?></th>
		<th><?php echo $this->Paginator->sort('created');?></th>
		<th > </th>
	</tr>
	<?php
	foreach ($message_sources as $source): ?>
	<tr>
		<td><?php echo h($source['MessageSource']['name']); ?>&nbsp;</td>
		<td><?php echo h($source['MessageSource']['description']); ?>&nbsp;</td>
		<td><?php echo h($source['User']['username']); ?>&nbsp;</td>
		<td><?php echo h($source['MessageSource']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $source['MessageSource']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $source['MessageSource']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', 
				$source['MessageSource']['id']), null, __('Are you sure you want to delete source %s?', h($source['MessageSource']['name']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p class="pagination-legend">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('New source'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List all sources'), array('action' => 'index')); ?></li>
	</ul>
</div>
