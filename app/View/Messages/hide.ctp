<?php 
    echo $this->Html->script('jquery-1.7.2.min', false);
    echo $this->Html->script('modernizr.custom', false);
    echo $this->Html->script('message_manager_client', false);
    echo $this->Html->script('boilerplate', false); 
?>

<div class="mm-message form">
	<h2><?php echo __('Hide message'); ?></h2>
	<div class="mm-messages">
    	<dl style="margin:2em 0">
    		<dt>
    			Sender
    		</dt>
    		<dd>
    			<?php echo h($message['Message']['from_address'])?>
    			&nbsp;
    		</dd>
    		<dt>
    			Message
    		</dt>
    		<dd>
    			<p class="message-text"><?php echo h($message['Message']['message'])?></p>
    		</dd>
    		<dt>Status</dt>
    		<dd class="status-<?php echo h($message['Status']['name']) ?>">
    			<strong><?php echo h($message['Status']['name']) ?></strong>
    			&nbsp;
    		</dd>
    	</dl>
	</div>
	<div class="boilerplate-chooser" id="mm-boilerplate-hide-reasons-box">
		<label for="boilerplate-reasons">Use preloaded reason for hiding:</label>
		<select name="boilerplate-reasons" id="mm-boilerplate-reasons">
		</select>
	</div>
	<?php echo $this->Form->create();?>
		<div>
			<?php
				echo $this->Form->input('reason_text', array('name' => 'reason_text', 'type' => 'textarea', 'escape' => false, 'label' => "Reason for hiding"));
			?>
		</div>
		<div class="actions inline-buttons">
			<?php echo $this->Form->submit(__('Hide message', array('style' => 'float:right;'))); ?>
			<?php echo $this->Html->link(__('Cancel'), array('action' => 'view', $message['Message']['id']));?>
		</div>
	<?php echo $this->Form->end(); ?>
	
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View message'), array('action' => 'view', $message['Message']['id']));?></li>
		<li>&nbsp;</li>
		<li><?php echo $this->Html->link(__('List all messages'), array('action' => 'index'));?></li>
	</ul>
</div>
