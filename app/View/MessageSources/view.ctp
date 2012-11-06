<div class="mm-messagesources view">
<h2><?php  echo __('Message source');?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['description']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('URL'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['url']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo h($message_source['User']['username']); ?>
			<em>used to authenticate incoming messages</em>
			&nbsp;
		</dd>
		<dt><?php echo __('IP addresses'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['ip_addresses']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Remote ID'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['remote_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($message_source['MessageSource']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
	
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit source'), array('action' => 'edit', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete source'), array('action' => 'delete', $message_source['MessageSource']['id']), 
			null, __('Are you sure you want to delete this message source?')); ?> </li>
			<li><?php echo $this->Html->link(__('Gateway test'), array('action' => 'gateway_test', $message_source['MessageSource']['id'])); ?> </li>
			<li><?php echo $this->Html->link(__('List all sources'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New source'), array('action' => 'add')); ?> </li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
