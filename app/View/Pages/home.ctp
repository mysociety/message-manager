<div class="mm-page view">
	<h2>Welcome to the Message Manager</h2>
	<p>
		Messages for FixMyStreet and similar systems.
	</p>
	<div class="home-block">
		<h3>Tags</h3>
		<dl>
			<?php $tags = Configure::read('tags'); 
			foreach ($tags as $tag => $full) { ?>
				<dt><?php echo strtoupper(h($tag)); ?></dt>
				<dd><?php echo h($full); ?></dd>
			<?php } ?>
		</dl>
		<p>
			Tags <strong><?php echo(Configure::read('remove_tags_when_matched')?"are":"are not"); ?></strong>
			stripped from <em>the start of</em> incoming messages.
		</p>
	</div>
	<div class="home-block">
		<h3>Settings</h3>
		<dl>
			<dt>
				FMS site URL
			</dt>
			<dd>
				<?php 
					$fms_url = Configure::read('fms_site_url'); 
					echo $this->Html->link($fms_url, $fms_url, array('class'=>'no-decoration'));
				?>
			</dd>
			<dt>
				FMS report URL
			</dt>
			<dd>
				<?php
				 	// to demo, quirkily send '%s' as the replacement string for, um, '%s' :-)
					echo $this->MessageUtils->fms_report_url('%s'); 
				?>
			</dd>
			<dt>
				Lock expiry
			</dt>
			<dd>
				<?php echo Configure::read('lock_expiry_seconds'); ?> seconds
			</dd>
		</dl>
	</div>
	<p>
		To change tags and settings, edit <code>app/Config/MessageManager.php</code>
	</p>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Messages'), array('controller' => 'messages')); ?></li>
		<li><?php echo $this->Html->link(__('Message sources'), array('controller' => 'messagesources')); ?></li>
		<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users')); ?></li>
		<li><?php echo $this->Html->link(__('Activity'), array('controller' => 'actions')); ?></li>
		<?php if (Configure::read('enable_dummy_client')==1) { ?> 
			<li>&nbsp;</li>
			<li><?php echo $this->Html->link(__('Dummy client'), array('controller' => 'MessageSources', 'action' => 'client')); ?></li>
		<?php } ?>
	</ul>
</div>
