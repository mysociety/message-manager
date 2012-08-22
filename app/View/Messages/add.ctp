<div class="mm-messages form">
	<?php echo $this->Form->create('Message');?>
	<fieldset>
		<legend><?php echo __('Add Message'); ?></legend>
	<?php
	 	// note: cake expects this be to a true foreign key; change name? $this->Form->input('external_id');
		echo $this->Form->input('source_id');
		echo $this->Form->input('from_address');
		echo $this->Form->input('message');
		echo $this->Form->input('tag');
	?>
	</fieldset>
	<?php echo $this->Form->end(__('Submit'));?>
	<p class="footnote">
		Note: adding here for admin only &mdash; normally messages come in from SMS gateway.
	</p>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List all messages'), array('action' => 'index'));?></li>
	</ul>
</div>
