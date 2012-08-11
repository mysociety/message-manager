<div class="mm-actions view">
	<h2>Action <?php echo h($action['Action']['id'])?></h2>

	<table>
		<tr>
			<th>Timestamp</th>
			<th>Message</th>
			<th>User</th>
			<th>Action</th>
			<th>Item</th>
			<th>Note</th>
		</tr>

		<tr>
			<td><?php echo h($action['Action']['created']); ?></td>
			<td>
				<?php if ($action['Action']['message_id']) {
					echo $this->Html->link($action['Message']['from_address'],
						array('controller' => 'messages', 'action' => 'view', $action['Action']['message_id']));
				} else { ?>
					&mdash;
				<?php } ?>
			</td>
			<td>
				<?php if ($action['Action']['user_id']) {
					echo $this->Html->link($action['User']['username'],
						array('controller' => 'users', 'action' => 'view', $action['Action']['user_id']));
				} else { ?>
					&mdash;
				<?php } ?>
			</td>
			<td><?php echo h($action['ActionType']['description']); ?></td>
			<td><?php echo h($action['Action']['item_id']); ?></td>
			<td><?php echo h($action['Action']['note']); ?></td>
		</tr>
	</table>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete Action'), array('action' => 'delete', $action['Action']['id']), null, __('Are you sure you want to delete # %s?', $action['Action']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('View all Actions'), array('action' => 'index')); ?> </li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
