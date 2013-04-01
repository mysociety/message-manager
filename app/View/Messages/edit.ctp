<div class="mm-message form">
	<h2><?php echo __('Update Message'); ?></h2>
	<?php echo $this->Form->create('Message');?>
		<p>
		<?php if ($is_admin_group) { ?>
			<!-- administrators should be able to edit the full record. -->
		<?php } else { ?>
			Note:
			you may only update the message's tag. <br/>
			You need an administrator login in order to edit the full record.
		<?php } ?>
		</p>
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
		</dl>
		<hr/>
		
		<?php
			if (Configure::read('allow_message_text_edits') && $is_admin_group) {
				echo $this->Form->input('message', array(
					'label' => "Message text (don't edit this unless you are certain you really, really need to)"
				)); 
			}
			// this suppressed because we're not allowing status edits
			// echo $this->Form->input('status',array('type'=>'radio','options'=>$statuses));	
			echo $this->Form->input('tag', array('style' => "width:5em;")); 
			echo $this->Form->input('parent_id', array(
				'type' => 'text',
				'style' => 'width:5em;',
				'label' => 'ID of parent message (if any). Use this to indicate that this message is a reply.'
			)); 
			if ($message['Message']['send_fail_count'] > 0) {
				echo "<div style='border:1px solid #f00; background:#fee; padding:0.5em;'>";
				echo $this->Form->input('send_fail_count', array('label' => "Send fail count (maybe set this to 0 to force retries, once you've cleared the problem)", 'style' => "width:5em;"));	
				if (!empty($message['Message']['send_fail_reason'])) {
					echo(__('Last failure message was: %s', h($message['Message']['send_fail_reason'])));
					if (!empty($message['Message']['send_failed_at'])) {
						echo(__('<br/> at %s', h($message['Message']['send_failed_at'])));
					}
				}
				echo "</div>";
			}
		?>
		
	<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View message'), array('action' => 'view', $message['Message']['id']));?></li>
		<li>&nbsp;</li>
		<li><?php echo $this->Html->link(__('List all messages'), array('action' => 'index'));?></li>
	</ul>
</div>
