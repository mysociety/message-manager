<div class="mm-page">
	<h2>Welcome to the Message Manager</h2>
	<p>
		<?php echo($welcome_msg); ?>
	</p>
	<?php if ($is_logged_in) { ?>
		<?php $fms_url = Configure::read('fms_site_url'); ?>
		<div class="mm-login-type">
			<?php if ($group_name == 'administrators') {  ?>
				<p>
					You are logged in as an <strong>administrator</strong>.
				</p>
				<p>
					You have full access to messages, users, and boilerplate strings.
				</p>
			<?php } elseif ($group_name == 'managers') {  ?>
				<p>
					You are logged in as a <strong>manager</strong>.
				</p>
				<p>
					You have full access to messages but you can't modify users.
				</p>
			<?php } elseif ($group_name == 'message-sources') { ?>
				<p>
					You are logged in as a <strong>message source</strong> user.
				</p>
				<p>
					You have <strong>no access</strong> to the Message Manager through this website. 
					This type of account can only be used for submitting messages
					(for example, automated login from an SMS gateway).
				</p>
			<?php } elseif ($group_name == 'api-users') { ?>
				<p>
					You are logged in as an <strong>API-user</strong>.
				</p>
				<p>
					You have <strong>no access</strong> to most of the features affecting messages or users on this website.
					This type of account can only be used from within client applications
					<?php if (!empty($fms_url)) { echo("(such as <a href='$fms_url'>$fms_url</a>).");} ?>
				</p>
			<?php } ?>
			<p>
				You can 
				<?php echo $this->Html->link(__('change your password'), array('controller' => 'Users', 'action' => 'change_password')); ?>
				(also via <strong>password</strong> link at the top right).
			</p>
		</div>
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
				The first word of an incoming message is used as a <em>tag</em> if it matches any of the values above.
				<br/>
				Tags <?php echo(Configure::read('remove_tags_when_matched')?"will be":"will not be"); ?>
				stripped from the start of incoming messages when they are received.
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
					CORS URL
				</dt>
				<dd>
					<?php 
						$cors_url = Configure::read('cors_allowed'); 
						if (empty($cors_url)) {
							echo "<i>none: no CORS requests allowed</i>";
						} else {
							echo $this->Html->link($cors_url, $cors_url, array('class'=>'no-decoration'));
						}
					?>
				</dd>
				<dt>
					Lock expiry
				</dt>
				<dd>
					<?php echo Configure::read('lock_expiry_seconds'); ?> seconds
				</dd>
				<dt>
					Reply period
				</dt>
				<dd>
					<?php echo Configure::read('autodetect_reply_period'); ?>
					(when determining to which message a reply belongs)
				</dd>
			</dl>
		</div>
		<?php if ($group_name == 'administrators') {  ?>
			<div class="home-block">
				<p>
					To change tags and settings, edit 
					<?php if (Configure::read('might_use_general_yml')=='1') { ?>
						<code>app/Config/general.yml</code> or 
					<? } ?>
					<code>app/Config/MessageManager.php</code>.
				</p>
				<p>
					Reply threads: <a href="/messages?recover_tree=1">tree recovery</a>
				</p>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="mm-login-type">
			Please <a href="/Users/login">log in</a> with an administrator or manager account.
		</div>
	<?php } ?>	
</div>
