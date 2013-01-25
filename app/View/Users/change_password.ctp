<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo __('Change password for user "%s"', $username); ?></legend>
	<?php
		echo $this->Form->input('new_password', array('type'=>'password','label'=>'New password'));
		echo $this->Form->input('confirm_password', array('type'=>'password','label'=>'Repeat password to confirm'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li>
			<li><?php echo $this->Html->link(__('Edit user'), array('action' => 'edit'));?></li>
		</li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index'));?></li>
		<?php echo $this->element('sidebar/messages'); ?>
	</ul>
</div>
