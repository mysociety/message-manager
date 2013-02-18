<?php echo $this->Html->script('jquery-1.7.2.min', false); ?>
<?php echo $this->element('boilerplate_choices_js'); ?>
<div class="mm-boilerplatestrings form">
<?php echo $this->Form->create('BoilerplateString');?>
	<fieldset>
		<legend><?php echo __('Add new boilerplate string'); ?></legend>
		<?php 
			echo $this->element('boilerplate_choices', array("choices" => $all_langs, "name" => "lang"));
			echo $this->Form->input('lang', array('label' => 'Language'));
			echo $this->element('boilerplate_choices', array("choices" => $all_types, "name" => "type"));
			echo $this->Form->input('type');
			echo $this->Form->input('text_value');
			echo $this->element('boilerplate_ellipsis');
			echo $this->Form->input('sort_order', array('label' => 'Sort index (used to determine display order in drop-down menu)'));
		?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List all strings'), array('action' => 'index'));?></li>
		<?php echo $this->element('sidebar/strings'); ?>
	</ul>
</div>
