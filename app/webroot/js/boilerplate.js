// dropping boilerplate strings into hide and reply pages *within* MM
// taken from message_manager_clients.js (not DRY :-( )

$(document).ready(function() {

    message_manager.config({
	    url_root: "/"
    });
    
    $('#mm-boilerplate-replies').change(function(e){
        var old_txt = $('#MessageReplyText').val();
        var new_txt = $(this).val().replace(/(^\.\.\.|\.\.\.$)/, old_txt);
        $('#MessageReplyText').val(new_txt); 
    });

    $('#mm-boilerplate-reasons').change(function(e){
        var old_txt = $('#MessageReasonText').val();
        var new_txt = $(this).val().replace(/(^\.\.\.|\.\.\.$)/, old_txt);
        $('#MessageReasonText').val(new_txt);
    });
    
    message_manager.populate_boilerplate_strings('reply');
    message_manager.populate_boilerplate_strings('hide-reason');
});
