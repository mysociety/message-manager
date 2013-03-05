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
