<div class="mm-page view">
	<h2>About the Message Manager</h2>
	<p><strong>
		Messages for FixMyStreet and similar systems.
	</strong></p>
	<p>
		The Message Manager sits between a message source (such as an SMS gateway) and a 
		<a href="http://code.fixmystreet.com/">FixMyStreet-like application</a>.
		It accepts incoming messages, and makes them available to nominated users on the
		FMS system.
	</p>
	<h3>Simple life cycle of a message</h3>
	<ul>
		<li>incoming message received from message source, </li>
		<li>message offered to nominated (e.g., admin) FMS users</li>
		<li>FMS user assigns message to FMS report</li>
		<li><em>FMS user replies to user</em></li>
	</ul>
	<p>
		And that's it. There is some message-locking going on, to discourage FMS users from
		colliding. 
	</p>

	<h3>JSON API</h3>
	<p>
		FMS communicates with the Message Manager with AJAX calls sending JSON &mdash; see the 
		<?php echo $this->Html->link(__('Message Manager JSON API'), array('action' => 'api')); ?>
		for details and examples.
	</p>	
		
</div>
<?php echo $this->element('sidebar/pages'); ?>
