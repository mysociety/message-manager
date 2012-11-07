<div class="mm-messagesources view">
<h2><?php  echo __('Message sources: client test');?></h2>
<p>
	Gateway test for message source <b><?php echo h($message_source['MessageSource']['name']); ?></b>.
</p>
<!-- not showing url and remote_id, because this page is not currently restricted by auth -->
<p>
	Connection test result: <b><?php echo h($connection_test_result); ?></b>
</p>
	

</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View source'), array('action' => 'view', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Edit source'), array('action' => 'edit', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List all sources'), array('action' => 'index')); ?> </li>
	
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>