<div class="mm-page">
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
				Autodetect reply period
			</dt>
			<dd>
				<?php echo Configure::read('autodetect_reply_period'); ?>
			</dd>
		</dl>
	</div>
	<p>
		To change tags and settings, edit 
		<?php if (Configure::read('might_use_general_yml')=='1') { ?>
			<code>app/Config/general.yml</code> or 
		<? } ?>
		<code>app/Config/MessageManager.php</code>.
	</p>
</div>
