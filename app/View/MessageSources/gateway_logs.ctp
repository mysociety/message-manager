<div class="mm-messagesources view">
<h2><?php  echo __('Message sources: gateway log');?></h2>
<h3>
	<?php echo $subtitle; ?>
</h3>
<?php if (! empty($gateway_logs)) { ?>
	<pre style="margin:3em 0;"><?php echo h($gateway_logs); ?></pre>
<?php } ?>
<?php if (! empty($error_msg)) { ?>
    <div class="error-message">
        <?php echo h($error_msg); ?>
    </div>
<?php } ?>
<?php echo $this->Form->create(false);?>
	<?php
		echo $this->Form->input('date', array(
			'label' => 'Date of logged activity to retrieve (format YYYYMMDD or similar, or "yesterday", "today", etc.)',
			'style' => 'max-width: 8em; margin-top:0.5em;',
			'value' => $date
		));
	?>
<?php echo $this->Form->end(__('Retrieve log'));?>

</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View source'), array('action' => 'view', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Edit source'), array('action' => 'edit', $message_source['MessageSource']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Gateway test'), array('action' => 'gateway_test', $message_source['MessageSource']['id'])); ?> </li>
		<li>&nbsp;</li>
		<li><?php echo $this->Html->link(__('List all sources'), array('action' => 'index')); ?> </li>
	</ul>
</div>
