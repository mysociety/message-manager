// dropping boilerplate strings into hide and reply pages *within* MM
// taken from message_manager_clients.js (not DRY :-( )

$(document).ready(function() {

    var _url_root = '/';
    var $boilerplate_replies = $('#mm-boilerplate-replies-box');
    var $hide_reasons = '';
    
    var populate_boilerplate_strings = function(boilerplate_type, options) {
        $.ajax({
            dataType:"json", 
            type:"get",
            url: _url_root +"boilerplate_strings/index/" + boilerplate_type + ".json",
            success:function(data, textStatus) {
                if (data.success) {
                    var raw_data = data.data;
                    var select_html = get_select_tag_html(data.data, boilerplate_type);
                    populate_boilerplate(boilerplate_type, select_html);
                } else {
                    // console.log("failed to load boilerplate");
                }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
                // console.log("boilerplate error: " + textStatus + ": " + errorThrown);
            }
        });
    };

    var get_select_tag_html = function(boilerplate_data, boilerplate_type) {
        var html = "<option value=''>--none--</option>\n";
        var qty_langs = 0;
        var qty_strings = 0;
        if (boilerplate_data.langs) {
            for (var i=0; i< boilerplate_data.langs.length; i++) {
                var lang = boilerplate_data.langs[i];
                var options = "";
                for (var j in boilerplate_data[lang]) {
                    if (boilerplate_data[lang].hasOwnProperty(j)) {
                        options += "<option>" + boilerplate_data[lang][j] + "</option>\n";
                        qty_strings++;
                    }
                }
                if (boilerplate_data.langs.length > 1) { // really need pretty name for language
                    options = '<optgroup label="' + lang + '">\n' + options + '</optgroup>\n';
                }
                html += options;
            }
        }
        if (qty_strings === 0) {
            html = '';
        }
        return html;
    };
    
    // actually load the select tag
    var populate_boilerplate = function(boilerplate_type, html) {
        var $target = null;
        switch(boilerplate_type) {
            case 'hide-reason': $target = $hide_reasons; break;
            case 'reply': $target = $boilerplate_replies; break;
        }
        if ($target) {
            if (html) {
                $target.show().find('select').html(html);
            } else {
                $target.hide();
            }
        }
    };
    
    $('#mm-boilerplate-replies').change(function(e){
        var old_txt = $('#MessageReplyText').val();
        var new_txt = $(this).val().replace(/(^\.\.\.|\.\.\.$)/, old_txt);
        $('#MessageReplyText').val(new_txt); // load reason_text with boilerplate reason
    });
    
    populate_boilerplate_strings('reply');
});
