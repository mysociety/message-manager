<ul>
	<?php if (AuthComponent::user('id')) { ?>
		<li class="msg-link"><?php echo $this->Html->link(__('Received'), array('controller' => 'messages', 'action' => 'index', 'received')); ?></li>
		<li class="msg-link"><?php echo $this->Html->link(__('Sent'), array('controller' => 'messages', 'action' => 'index', 'sent')); ?></li>
		<li class="msg-link"><?php echo $this->Html->link(__('Available'), array('controller' => 'messages', 'action' => 'available')); ?></li>
		<li class="msg-link"><?php echo $this->Html->link(__('All'), array('controller' => 'messages', 'action' => 'index')); ?></li>
		<li>&nbsp;&nbsp;&nbsp;</li>
		<li><?php echo $this->Html->link(__('Sources'), array('controller' => 'MessageSources', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('Strings'), array('controller' => 'BoilerplateStrings', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('Activity'), array('controller' => 'actions', 'action' => 'index')); ?></li>
		<?php if (Configure::read('enable_dummy_client')==1) { ?> 
			<li><?php echo $this->Html->link(__('Dummy client'), array('controller' => 'MessageSources', 'action' => 'client')); ?></li>
		<?php } ?>
	<?php } else { ?>
		<li><?php echo $this->Html->link(__('Log in'), array('controller' => 'users', 'action' => 'login')); ?></li>
	<?php } ?>
</ul>
