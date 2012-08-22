<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('User.id');
		echo $this->Form->input('User.username', array('errors'=>true));
		echo $this->Form->input('new_password', array('type'=>'password','label'=>'New password'));
		echo $this->Form->input('confirm_password', array('type'=>'password','label'=>'Repeat password to confirm'));
		echo $this->Form->input('User.email', array('errors'=>true));
		echo $this->Form->input('group_id');
		echo $this->Form->input('allowed_tags');
		echo $this->Form->input('can_reply', array('label'=>'Can send replies?'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('User.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index'));?></li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
