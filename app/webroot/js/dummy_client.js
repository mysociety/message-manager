$(document).ready(function() {
	
	var dummy_busy = false;

	var dummy_populate_assign_boxes = function(data) {
		if (('success' in data) && data.success) {
			if ('Message' in data['data']) {
				var msg_id = data.data['Message'].id;
				$('#message_id').val(msg_id); 
				if ($('#random-fms-id').prop("checked")) {
					$('#fms_id').val(100+Math.floor(Math.random()*899));
				}
			}
		}
	}

	var dummy_populate_username = function(data) {
		$('#mm-username span').text(data['username']);
	}
	
	var dummy_clear_assign_boxes = function() {
        $('#fms_id,#message_id').val(''); // for dummy demo only
	}
	
	var dummy_reply_cleanup = function(data) {
		$('#reply_tet').val('');
		$('#reply-form-container').stop().hide(500);
		dummy_busy = false;
	}

	var dummy_hide_cleanup = function(data) {
		dummy_busy = false;
	}

	//------------------------------------------------------------
	// message_manager has been declared in clients.js

	message_manager.config({url_root: "/"});

	message_manager.setup_click_listener({callback: dummy_populate_assign_boxes});

	$('#available-submit').click(function(){
		message_manager.get_available_messages(dummy_populate_username);
	});

	$('#assign-fms-submit').click(function() {
		message_manager.assign_fms_id(
		    $('#message_id').val(), 
		    $('#fms_id').val(), 
		    {callback:dummy_clear_assign_boxes});
	});    

	$('#reveal-reply-form').click(function(e) {
		e.preventDefault();
		if (!$('#message_id').val()) {
			$('#reply-form-container').stop().hide(500);
		} else {
			$('#reply-form-container').stop().toggle(1000);
		}
	});

	$('#hide-button').click(function() {
		if ($('#message_id').val()) {
			message_manager.hide(
			    $('#message_id').val(), 
			    {callback:dummy_hide_cleanup});
		}
	});
	
	$('#reply-submit').click(function(e) {
		e.preventDefault();
		if (! dummy_busy) {
			if (! $('#message_id').val()) {
				alert("No: won't send without a message ID");
			} else {
				dummy_busy = true;
				message_manager.reply(
				    $('#message_id').val(), 
				    $('#reply_text').val(), 
				    {callback:dummy_reply_cleanup});
			}
		}
	});
    
});
