<?php 
echo $this->Html->css('/js/fancybox/jquery.fancybox-1.3.4.css');

echo $this->Html->script('jquery-1.7.2.min', false); 
echo $this->Html->script('modernizr.custom', false);
echo $this->Html->script('message_manager_client', false);
echo $this->Html->script('dummy_client', false);
echo $this->Html->script('/js/fancybox/jquery.fancybox-1.3.4.pack.js', false);
?>

<h2>
	Dummy Client &amp; Message Generator
</h2>
<p>
	This page provides a dummy client and a manual incoming message input 
	for testing the behaviour of MessageManager. Disable this in production:
	set <code>enable_dummy_client=0</code> in <code>app/Config/MessageManager.php</code>.
</p>
<?php if ($group_name == 'administrators' || $group_name == 'message-sources') { ?>
	<div class="dummy-client" 
		style="float:<?php if ($group_name != 'message-sources') { ?>right<?php } else { ?>left<?php } ?>;">
		<h3>Incoming Message</h3>
		<p> Simulates an incoming message from, e.g., SMS gateway.</p>
		<?php 
			echo $this->Form->create('Message', array('action' => 'incoming')); 
			// echo $this->Form->input('username');
			// echo $this->Form->input('password');
			echo $this->Form->input('messageSource_id', array('name' => 'data[Message][source_id]'));
			echo $this->Form->input('external_id', array('label' => 'External ID (optional: a transaction ID assigned by some gateways)', 'type' => 'text'));
			echo $this->Form->input('from_address', array('label' => 'Sender phone number'));
			echo $this->Form->input('message', array('label' => 'Message'));
			echo $this->Form->submit();
			echo $this->Form->end();
		?>
	</div>
	<?php if ($group_name == 'message-sources') { ?>
		<img src="img/mobile-phone.png" alt="mobile phone" style="float:left;"/>
	<?php } ?>
<?php } ?>
<?php if ($group_name != 'message-sources') { ?>
	<div class="dummy-client">
		<p id="mm-username">
			tags:&nbsp;<?php echo $this->MessageUtils->pretty_tag_list_html($allowed_tags); ?>;
			username:&nbsp;<span id="mm-received-username"><?php echo (empty($username)? "<i>none</i>":h($username)); ?></span> 
		</p>
		<h3>Mock FMS Client</h3>
		<div id="mm-status-message-container">
			<div id="mm-status-message"></div>
		</div>
		<div id="mm-message-list" style="min-height:1em;"></div>
		<?php echo $this->Form->create(array('default' => false)); ?>
			<div id="mm-login-container">
				<?php
					echo $this->Form->input('message_id', array('label'=>'MM username', 'type'=>'text', 'name'=>'mm-htauth-username', 'id'=>'mm-htauth-username'));
					echo $this->Form->input('fms_id', array('label'=>'Password', 'type'=>'password', 'name'=>'mm-htauth-password', 'id'=>'mm-htauth-password'));
				?>
			</div>
		<?php
			echo $this->Form->submit('Get available messages', array('id' => 'available-submit'));
			echo $this->Form->end();
		?>
    	<div id="reply-form-container">
    	    	<?php 
    				echo $this->Form->create(array('id' => 'reply-form','default'=>false));
				?>
				<!-- populated by Ajax call -->
				<div class="input" id="mm-boilerplate-replies-box">
					<label for="boilerplate-replies">Use preloaded reply:</label>
					<select name="boilerplate-replies" id="mm-boilerplate-replies">
					</select>
				</div>
				<?php
    				echo $this->Form->input('reply_text', array('label'=>'Reply text', 'type'=>'text', 'name'=>'reply_text', 'id'=>'reply_text'));
    				echo $this->Form->input('reply_to_msg_id', array('type'=>'hidden', 'name'=>'message_id', 'id'=>'reply_to_msg_id'));
    				echo $this->Form->submit(__('Send Reply'), array('id' => 'reply-submit'));
    				echo $this->Form->end();
    			?>
    	</div>
		<div id="assign-fms-container">
			<?php 
				echo $this->Form->create(array('id' => 'assign-fms-form','default'=>false));
				echo $this->Form->input('message_id', array('label'=>'Message ID', 'type'=>'text', 'name'=>'message_id', 'id'=>'message_id'));
				echo $this->Form->input('fms_id', array('label'=>'FMS ID', 'type'=>'text', 'name'=>'fms_id', 'id'=>'fms_id'));
				echo $this->Form->submit(__('Assign FMS ID'), array('id' => 'assign-fms-submit'));
				echo $this->Form->end();
			?>
			<p style="clear:both;padding-top:1em;">
				<input name="random-fms-id" id="random-fms-id" type="checkbox"><label for="random-fms-id">randomize FMS ID integers</label>
			</p>
		</div>
		<div id="hide-form-container">
			<p style="color:#000">Hiding message: <span id="hide-form-message-text"></span></p>
	    	<?php 
				echo $this->Form->create(array('id' => 'hide-form','default'=>false));
			?>
			<!-- populated by Ajax call -->
			<div class="input" id="mm-boilerplate-hide-reasons-box">
				<label for="boilerplate-hide-reasons">Use preloaded reason:</label>
				<select name="boilerplate-hide-reasons" id="mm-hide-reasons">
				</select>
			</div>
			<?php
				echo $this->Form->input('reason_text', array('label'=>'Reason for hiding message', 'type'=>'textarea', 'name'=>'reason_text', 'id'=>'reason_text'));
				echo $this->Form->input('msg_id', array('type'=>'hidden', 'name'=>'msg_id', 'id'=>'hide_msg_id'));
				echo $this->Form->submit(__('Hide Message'), array('id' => 'hide-submit'));
				echo $this->Form->end();
			?>
		</div>
	</div>
<?php } ?>

<div style="clear:both;"></div>
