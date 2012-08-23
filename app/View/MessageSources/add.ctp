<div class="mm-messagesources form">
<?php echo $this->Form->create('MessageSource');?>
	<fieldset>
		<legend><?php echo __('Add Message Source'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('url');
		echo $this->Form->input('ip_addresses');
		echo $this->Form->input('user_id', array('label' => __('Allocated user (note: must be a member of the %s group)', $source_group_name)));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List sources'), array('action' => 'index'));?></li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
