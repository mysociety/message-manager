<div class="mm-boilerplatestrings view">
	<h2><?php echo $title;?></h2>
	<p>
		Boilerplate strings are short texts that are available to clients to help their users
		fill in forms (such as standard replies and reasons hiding messages).
	</p>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('id');?></th>
		<th><?php echo $this->Paginator->sort('lang');?></th>
		<th><?php echo $this->Paginator->sort('type');?></th>
		<th><?php echo $this->Paginator->sort('text');?></th>
		<th><?php echo $this->Paginator->sort('sort_index');?></th>
		<th > </th>
	</tr>
	<?php
	foreach ($boilerplate_strings as $string): ?>
	<tr>
		<td><?php echo h($string['BoilerplateString']['id']); ?>&nbsp;</td>
		<td><?php echo h($string['BoilerplateString']['lang']); ?>&nbsp;</td>
		<td><?php echo h($string['BoilerplateString']['type']); ?>&nbsp;</td>
		<td><?php echo h($string['BoilerplateString']['text_value']); ?>&nbsp;</td>
		<td><?php echo h($string['BoilerplateString']['sort_index']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $string['BoilerplateString']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', 
				$string['BoilerplateString']['id']), null, __('Are you sure you want to delete this string?\n\n"%s"\n', h($string['BoilerplateString']['text_value']))); ?>
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
		<li><?php echo $this->Html->link(__('Add new string'), array('action' => 'add')); ?></li>
		<?php echo $this->element('sidebar/strings'); ?>
	</ul>
</div>

