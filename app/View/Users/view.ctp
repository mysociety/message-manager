<div class="users view">
<h2><?php  echo __('User');?></h2>
	<dl>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Group'); ?></dt>
		<dd>
			<?php echo h($user['Group']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tags'); ?></dt>
		<dd>
			<?php
				if (empty($user['User']['allowed_tags'])) {
					echo('<em>' . __("any") . '</em>');
				} else {
			 		echo h(strtoupper($user['User']['allowed_tags'])); 
				}
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($user['User']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
	
	<h3>History/activity</h3>

	<table>
		<tr>
			<th>Timestamp</th>
			<th>Activity</th>
			<th>Message</th>
			<th>Note</th>
		</tr>
	<?php foreach($user['Action'] as $action){ ?>
		<tr>
			<td><?php echo $action['created']; ?></td>
			<!--echo $action['ActionType']['name']; -->
			<td><?php echo $action['ActionType']['description']; ?></td>
			<td>
				<?php if ($action['message_id']) {
					echo $this->Html->link($action['Message']['msisdn'],
						array('controller' => 'messages', 'action' => 'view', $action['message_id']));
				} else { ?>
					&mdash;
				<?php } ?>
			</td>
			<!-- TODO item_id links to whatever item type is relevant to this action -->
			<td><?php echo $action['note']; ?></td>
		</tr>
	<?php } ?>
	</table>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index'));?></li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
