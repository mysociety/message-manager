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
		message_manager.get_available_messages({callback: dummy_populate_username});
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
	
	$('#mm-hide-reasons').change(function(e){
	    $('#reason_text').val($(this).val()); // load reason_text with boilerplate reason
	});
	$('#mm-boilerplate-replies').change(function(e){
	    $('#reply_text').val($(this).val()); // load reason_text with boilerplate reason
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

    /* TODO: callbacks not running as expected
    $("a#reply").fancybox({onClosed: function(){
        alert("cleared replies");
        dummy_busy=false; 
        $('#reply_text').val(""); 
        $("#mm-boilerplate-replies option").removeAttr("selected"); 
        $("#mm-boilerplate-replies option:first-child").attr("selected", true); 
    }});
    $("a#hide").fancybox({onClosed: function(){
        dummy_busy=false; $('#reason_text').val(""); $("#mm-hide-reasons option:first-child").attr("selected", true); 
    }});
    */
    
    message_manager.populate_boilerplate_strings('hide-reason');
    message_manager.populate_boilerplate_strings('reply');
});
