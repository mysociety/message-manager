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
			username:&nbsp;<span id="mm-received-username"><?php echo (empty($username)? "<i>none</li>":$username); ?></span> 
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
	</div>
<?php } ?>

<div style="clear:both;"></div>


<script type="text/javascript">
	$(document).ready(function() {

		var dummy_populate_assign_boxes = function(data) {
			if ('Message' in data) {
				var msg_id = data['Message'].id;
				$('#message_id').val(msg_id); 
				if ($('#random-fms-id').prop("checked")) {
					$('#fms_id').val(100+Math.floor(Math.random()*899));
				}
			}
		}

		var dummy_populate_username = function(data) {
			$('#mm-username span').text(data['username']);
		}
		
		var dummy_clear_assign_boxes = function() {
	        $('#fms_id,#message_id').val(''); // for dummy demo only
		}
		


		//------------------------------------------------------------
		// message_manager has been declared in clients.js

		message_manager.config({url_root: "/"});

		message_manager.setup_click_listener({callback: dummy_populate_assign_boxes});

		$('#available-submit').click(function(){
			message_manager.get_available_messages(dummy_populate_username);
		});

		$('#assign-fms-submit').click(function() {
			message_manager.assign_fms_id($('#message_id').val(), $('#fms_id').val(), {callback:dummy_clear_assign_boxes});
		});    

	});
</script>