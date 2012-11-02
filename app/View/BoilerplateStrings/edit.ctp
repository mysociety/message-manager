<div class="mm-boilerplatestrings form">
<?php echo $this->Form->create('BoilerplateString');?>
	<fieldset>
		<legend><?php echo __('Edit boilerplate string'); ?></legend>
	<?php
		echo $this->Form->input('lang');
		echo $this->Form->input('type');
		echo $this->Form->input('text_value');
		echo $this->Form->input('sort_index');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', 
			$this->Form->value('BoilerplateString.id')), null, 
			__('Are you sure you want to delete this string?\n\n"%s"\n', $this->Form->value('BoilerplateString.text_value'))); ?>
		</li>
		<li><?php echo $this->Html->link(__('List all strings'), array('action' => 'index'));?></li>
	</ul>
</div>
