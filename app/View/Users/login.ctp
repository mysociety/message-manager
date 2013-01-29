<div class="users index">

	<?php
		echo $this->Form->create('User', array('action' => 'login'));
		echo $this->Form->inputs(array(
		    'legend' => __('Login'),
		    'login' => array('label' => 'Login with username or email address'),
		    'password'
		));
		echo $this->Form->end('Login');
	?>
</div>
