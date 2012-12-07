<?php echo $this->Html->script('jquery-1.7.2.min', false); ?>
<?php echo $this->element('boilerplate_choices_js'); ?>
<div class="mm-boilerplatestrings form">
<?php echo $this->Form->create('BoilerplateString');?>
	<fieldset>
		<legend><?php echo __('Edit boilerplate string'); ?></legend>
	<?php
		echo $this->element('boilerplate_choices', array("choices" => $all_langs, "name" => "lang"));
		echo $this->Form->input('lang', array('label' => 'Language'));
		echo $this->element('boilerplate_choices', array("choices" => $all_types, "name" => "type"));	
		echo $this->Form->input('type');
		echo $this->Form->input('text_value');
		echo $this->Form->input('sort_order', array('label' => 'Sort index (used to determine display order in drop-down menu)'));
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
		<?php echo $this->element('sidebar/strings'); ?>
	</ul>
</div>
