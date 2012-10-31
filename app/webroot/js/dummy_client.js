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
		$('#reply_text').val('');
		dummy_busy = false;
	}

	var dummy_hide_cleanup = function(data) {
		$('#reason_text').val('');
		dummy_busy = false;
	}
	
	function sanitise_id(css_id) {
	    return css_id.replace(/\D/g, "");
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
		
	$('#mm-message-list').on('mouseover', 'li.mm-msg', function(e){
		e.stopPropagation(); // because replies are nested
		$('.mm-msg-action', $('#mm-message-list')).stop().fadeOut(200);
		$(this).find('> .mm-msg-action').stop().show();
		console.log("clicked on: " + $(this).attr('id'));
	});

	$('#hide-submit').click(function(e) {
	    e.preventDefault();
		if (! dummy_busy) {
			dummy_busy = true;
			console.log("hiding message: " + $('#reply_to_msg_id').val());
			message_manager.hide(
			    $('#hide_msg_id').val(), 
			    $('#reason_text').val(), 
			    {callback:dummy_hide_cleanup});
		}
	});
		
	$('#mm-message-list').on('click', '.mm-info', function(e){
		message_manager.show_info(sanitise_id($(this).parent().attr('id')));
	});
	
	$('#mm-pro-reasons').change(function(e){
	    $('#reason_text').val($(this).val()); // load reason_text with proforma reason
	});
	
	$('#reply-submit').click(function(e) {
		e.preventDefault();
		if (! dummy_busy) {
			dummy_busy = true;
			console.log("sending reply to: " + $('#reply_to_msg_id').val());
			message_manager.reply(
			    $('#reply_to_msg_id').val(), 
			    $('#reply_text').val(), 
			    {callback:dummy_reply_cleanup});
		}
	});
    
    $("a#reply").fancybox({onClosed: function(){dummy_busy=false;}});
    $("a#hide").fancybox({onClosed: function(){dummy_busy=false;}});
});
