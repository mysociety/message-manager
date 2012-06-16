<?php 
echo $this->Html->script('jquery-1.7.2.min', false); 
echo $this->Html->script('client', false); 
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
	<div class="dummy-client" <?php if ($group_name != 'message-sources') { ?>style="float:right;"<?php } ?>>
		<h3>Incoming Message</h3>
		<p> Simulates a message coming in from e.g., SMS gateway.</p>
		<?php 
			echo $this->Form->create('Message', array('action' => 'incoming')); 
			// echo $this->Form->input('username');
			// echo $this->Form->input('password');
			echo $this->Form->input('messageSource_id', array('name' => 'data[Message][source_id]'));
			echo $this->Form->input('external_id', array('label' => 'External ID (optional: a message ID)', 'type' => 'text'));
			echo $this->Form->input('msisdn', array('label' => 'MSISDN'));
			echo $this->Form->input('message', array('label' => 'Message'));
			echo $this->Form->submit();
			echo $this->Form->end();
		?>
		<p>
			Note: no FMS IDs in incoming messages: assign them with an AJAX call.
		</p>
	</div>
<?php } ?>
<?php if ($group_name != 'message-sources') { ?>
	<div class="dummy-client">
		<p id="mm-username">
			tag:&nbsp;<?php echo (empty($allowed_tags)? "<i>any</i>":strtoupper($allowed_tags)); ?>,
			username:&nbsp;<span><?php echo $username?></span> 
		</p>
		<h3>Mock FMS Client</h3>
		<div class="status-message-container">
			<div id="status-message"></div>
		</div>
		<div id="message-list" style="min-height:1em;"></div>
		<?php 
			echo $this->Form->create(); 
			//echo $this->Form->submit('Get available messages', array('id' => 'available-submit'));
			echo $this->Js->submit('Get available messages', array(
				'id'      => 'available-submit',
				'type'    => 'json',
				'before'  => $this->Js->get('#status-message')->effect('fadeIn'),
				'success'=> 'show_available_messages(data)',
				'url' => $this->Html->url(array(
					"controller" => "messages",
					"action" => "available",
					"ext" => "json"
				))
			));
			echo $this->Form->end();
		?>
	</div>
	<!--<div class="dummy-client"><h3>AJAX: release all locks</h3></div>-->
	<div class="dummy-client">
		<div id="assign-fms-container">
			<?php 
				echo $this->Form->input('message_id', array('label'=>'Message ID', 'type'=>'text', 'name'=>'message_id', 'id'=>'message_id'));
				echo $this->Form->create(array('id' => 'assign-fms-form','default'=>false));
				echo $this->Form->input('fms_id', array('label'=>'FMS ID', 'type'=>'text', 'name'=>'fms_id', 'id'=>'fms_id'));
				echo $this->Form->submit(__('Assign FMS ID'), array('id' => 'assign-fms-submit'));
				echo $this->Form->end();
			?>
			<p style="clear:both;padding-top:1em;">
				<input name="random-fms-id" id="random-fms-id" type="checkbox"><label for="random-fms-id">randomize FMS ID integers</label>
			</p>
		</div>
	</div>
<?php } ?>

<div style="clear:both;"></div>
