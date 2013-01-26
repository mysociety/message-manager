<div class="mm-messagesources view">
<h2><?php  echo __('Message sources: client test');?></h2>
<h3>
	Gateway test for message source <b><?php echo h($message_source['MessageSource']['name']); ?></b>.
</h3>
<p>
	Connection test result:
</p>
<?php if (! empty($connection_test_result)) { ?>
	<pre style="margin:3em 0;"><?php echo h($connection_test_result); ?></pre>
<?php } ?>

	

</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View source'), array('action' => 'view', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Edit source'), array('action' => 'edit', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List all sources'), array('action' => 'index')); ?> </li>
	
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
