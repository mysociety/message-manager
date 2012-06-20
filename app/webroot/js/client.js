//------------------ configure here, if needed -------------------//

// url to message manager (include trailing slash!)
// e.g. http://www.example.com/mm

var message_manager_url_root = "/";

// unique locks relinquish other locks when claiming a new one (recommended)

var want_unique_locks = true; 

//-----------------------------------------------------------------//

var username = "";
var msg_prefix = "msg-";

function make_base_auth(user, password) {
    var tok = user + ':' + password;
    var hash = btoa(tok);
    return "Basic " + hash;
}


function show_available_messages(data) {
    var messages = data['messages'];
    username = data['username'];
    $('#mm-received-username').text(username); // username is returned by API
    var $output = $('#message-list');
    if (messages instanceof Array) {
        if (messages.length==0) {
            $output.html('<p>No messages available.</p>');
        } else {
            var $ul = $('<ul/>');
            $output.empty().append($ul);
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i]['Message'];
                // var $msg_div = $('<div class="msg-text"/>').text(message['message']);
                // var $li = $('<li id="' + msg_prefix + message['id'] + '"></li>').append($msg_div);
                // $ul.append($li);
                var css_class = '';
                var lockkeeper = messages[i]['Lockkeeper']['username'];
                if (lockkeeper) {
                    css_class = lockkeeper==username? 'msg-is-owned':'msg-is-locked'; 
                }
                var escaped_text = $('<div/>').text(message['message']).html(); 
                var tag = message['tag'];
                if (tag == 'null' || tag == "") {
                    tag = '&nbsp;'
                }
                $ul.append(
                    $('<li id="' + msg_prefix + message['id'] + '"></li>').
                        addClass(css_class).
                        append($('<div class="msg-tag"/>').html(message['tag'])).
                        append($('<div class="msg-text"/>').html(escaped_text + '&nbsp;')));
            }
        }
    } else {
        $output.html('<p>No messages (server did not send a list).</p>');        
    }
    $('#status-message').stop().fadeOut();
}

$(document).ready(function () {

    // determine the username from the page, which MM has created:
    // On FMS, username needs to be established through a login JSON if
    // cross-domain allows it, else send username and password with each request (https)
    username = $('#mm-username span').text();

    var $message_list = $('#message-list');
    
    function say_lock_status(msg){
        $("#status-message").stop().show();
        $("#status-message").text(msg);
    }
    
    //.lock-msg-button
    $message_list.on('click', 'li', function(event){ 
        var $li = $(this).closest('li');
        var id = $li.attr('id').replace(msg_prefix, '');
        if ($li.hasClass('msg-is-locked')) {
            say_lock_status("message id=" + id + " is locked... trying for lock");
        } else if ($li.hasClass('msg-is-owned')) {
            say_lock_status("you own message id=" + id + ", looks OK (will check though)");
        } else {
            say_lock_status("message id=" + id + " is not locked... trying for lock");
        }
        mm_request_lock($li, id, want_unique_locks);
    });
    
    $('#available-submit').click(function(){
        $('#mm-login-container').stop().hide();
        $.ajax({
            dataType: "json", 
            type:     "post", 
            url:      message_manager_url_root +"messages/available.json",
            beforeSend: function (xhr){
                xhr.setRequestHeader('Authorization', make_base_auth(
                    $('#mm-htauth-username').val(),
                    $('#mm-htauth-password').val()
                ));
            },
            success:  function(data, textStatus) {show_available_messages(data)}, 
            error:    function(jqXHR, textStatus, errorThrown) {
                        var st = jqXHR.status; 
                        if (st == 401 || st == 403) {
                            var msg = (st == 401)? "Invalid username or password" : "Access denied: please log in";
                            $("#status-message").text(msg);
                            $('#mm-login-container').stop().slideDown();
                        } else {
                            $("#status-message").text("Error: " + st + " " + textStatus);
                        }
                      }
        });    
    });
    
    $('#assign-fms-submit').click(function() {
        var msg_id = $('#message_id').val();
        var fms_id = $('#fms_id').val();
        mm_assign_fms_id(msg_id, fms_id);
    });
    
    // wee routine for making the dummy client: remove in the Real World
    function for_dummy_only(msg_id) {
        $('#message_id').val(msg_id); 
        if ($('#random-fms-id').prop("checked")) {
             $('#fms_id').val(100+Math.floor(Math.random()*899));
        }        
    }
    
    function mm_request_lock($li, msg_id, want_unique_lock) {
        $li.addClass('msg-is-busy');
        $.ajax({
            dataType:"json", 
            type:"post", 
            url: message_manager_url_root +"messages/" +
                (want_unique_lock? "lock_unique" : "lock") + 
                "/" + msg_id + ".json",
            success:function(data, textStatus) {
                // returned data['data'] is 'Message', 'Source', 'Lockkeeper'
                if (data['success']) {
                    console.log("success! lock granted OK");
                    if (want_unique_lock) {
                        $('.msg-is-owned', $message_list).removeClass('msg-is-owned')
                    }
                    $li.removeClass('msg-is-busy msg-is-locked').addClass('msg-is-owned');
                    say_lock_status("lock granted"); // to data['data']['Lockkeeper']['username']?
                    for_dummy_only(msg_id); // for dummy demo only: delete this
                } else {
                    $li.removeClass('msg-is-busy').addClass('msg-is-locked');
                    say_lock_status("failed: " + data['error']);
                }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
                say_lock_status("error: " + textStatus + ": " + errorThrown);
                $li.removeClass('msg-is-busy');
            }
        });
    }

    function mm_assign_fms_id(msg_id, fms_id) {
        var $li = $('#' + msg_prefix + msg_id);
        if ($li.size() == 0) {
            say_lock_status("Couldn't find message with ID " + msg_id);
            return;
        }
        if (isNaN(parseInt(fms_id,0))) {
            say_lock_status("missing FMS id (make one up if you're testing: use an integer)");
            return;            
        }
        $li.addClass('msg-is-busy');
        $.ajax({
            dataType:"json", 
            type:"post", 
            data:$("#assign-fms-submit").closest("form").serialize(),
            url: message_manager_url_root +"messages/assign_fms_id/" + msg_id + ".json",
            success:function(data, textStatus) {
                if (data['success']) {
                    console.log("success! FMS ID assigned OK");
                    // returned data['data'] is 'Message', 'Source', 'Lockkeeper['username']'
                    $li.removeClass('msg-is-busy msg-is-locked').addClass('msg-is-owned').fadeOut('slow'); // no longer available
                    say_lock_status("FMS ID assigned"); // to data['data']['Lockkeeper']['username']?
                    $('#fms_id,#message_id').val(''); // for dummy demo only
                } else {
                    $li.removeClass('msg-is-busy').addClass('msg-is-locked');
                    say_lock_status("failed: " + data['error']);
                }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
                say_lock_status("error: " + textStatus + ": " + errorThrown);
                $li.removeClass('msg-is-busy');
            }
        });
    }
});
