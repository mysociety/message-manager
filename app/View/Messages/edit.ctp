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
				MSISDN
			</dt>
			<dd>
				<?php echo h($message['Message']['msisdn'])?>
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
		<div>
				<?php
					// echo $this->Form->input('status',array('type'=>'radio','options'=>$statuses));	
					echo $this->Form->input('tag');	
				?>
		</div>
	<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View message'), array('action' => 'view', $message['Message']['id']));?></li>
		<li>&nbsp;</li>
		<li><?php echo $this->Html->link(__('List all messages'), array('action' => 'index'));?></li>
	</ul>
</div>