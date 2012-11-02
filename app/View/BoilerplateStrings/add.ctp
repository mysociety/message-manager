<div class="mm-boilerplatestrings form">
<?php echo $this->Form->create('BoilerplateString');?>
	<fieldset>
		<legend><?php echo __('Add new boilerplate string'); ?></legend>
	<?php
		echo $this->Form->input('lang');
		echo $this->Form->input('type');
		echo $this->Form->input('text_value');
		echo $this->Form->input('sort_order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List all strings'), array('action' => 'index'));?></li>
	</ul>
</div>
