<ul>
	<?php if (AuthComponent::user('id')) { ?>
		<li><?php echo $this->Html->link(__('Messages'), array('controller' => 'messages')); ?></li>
		<li><?php echo $this->Html->link(__('Message sources'), array('controller' => 'MessageSources')); ?></li>
		<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users')); ?></li>
		<li><?php echo $this->Html->link(__('Activity'), array('controller' => 'actions')); ?></li>
		<?php if (Configure::read('enable_dummy_client')==1) { ?> 
			<li><?php echo $this->Html->link(__('Dummy client'), array('controller' => 'MessageSources', 'action' => 'client')); ?></li>
		<?php } ?>
	<?php } else { ?>
		<li><?php echo $this->Html->link(__('Log in'), array('controller' => 'users', 'action' => 'login')); ?></li>
	<?php } ?>
</ul>