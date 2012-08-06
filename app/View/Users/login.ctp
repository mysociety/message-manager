<div class="users index">

	<?php
		echo $this->Form->create('User', array('action' => 'login'));
		echo $this->Form->inputs(array(
		    'legend' => __('Login'),
		    'login',
		    'password'
		));
		echo $this->Form->end('Login');
	?>
</div>
