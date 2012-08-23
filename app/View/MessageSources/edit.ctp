<div class="mm-messagesources form">
<?php echo $this->Form->create('MessageSource');?>
	<fieldset>
		<legend><?php echo __('Edit message source'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('url');
		echo $this->Form->input('ip_addresses', array('type' => 'text'));
		echo $this->Form->input('user_id', array('label' => __('Allocated user (note: must be a member of the %s group)', $source_group_name)));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', 
			$this->Form->value('MessageSource.id')), null, 
			__('Are you sure you want to delete # %s?', $this->Form->value('MessageSource.name'))); ?>
		</li>
		<li><?php echo $this->Html->link(__('List sources'), array('action' => 'index'));?></li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
