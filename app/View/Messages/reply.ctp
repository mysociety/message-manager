<?php 
    echo $this->Html->script('jquery-1.7.2.min', false); 
?>
<script>
$(document).ready(function() {

    // taken from message_manager_clients.js
    var _url_root = '/';
    var $boilerplate_replies = $('#mm-boilerplate-replies-box');
    
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
</script>

<div class="mm-message form">
	<h2><?php echo __('Reply to message'); ?></h2>
	<div class="mm-messages">
	<dl style="margin:2em 0">
		<dt>
			Sender
		</dt>
		<dd>
			<?php echo h($message['Message']['from_address'])?>
			&nbsp;
		</dd>
		<dt>
			Message
		</dt>
		<dd>
			<p class="message-text"><?php echo h($message['Message']['message'])?></p>
		</dd>
		<dt>Status</dt>
		<dd class="status-<?php echo h($message['Status']['name']) ?>">
			<strong><?php echo h($message['Status']['name']) ?></strong>
			&nbsp;
		</dd>
	</dl>
	</div>
	<div class="input" id="mm-boilerplate-replies-box">
		<label for="boilerplate-replies">Use preloaded reply:</label>
		<select name="boilerplate-replies" id="mm-boilerplate-replies">
		</select>
	</div>
	<?php echo $this->Form->create();?>
		<div>
				<?php
					echo $this->Form->input('reply_text', array('name' => 'reply_text', 'type' => 'textarea', 'escape' => false, 'label' => "Reply message text"));
				?>
		</div>
		<div class="actions inline-buttons">
			<?php echo $this->Form->submit(__('Send reply', array('style' => 'float:right;'))); ?>
			<?php echo $this->Html->link(__('Cancel'), array('action' => 'view', $message['Message']['id']));?>
		</div>
	<?php echo $this->Form->end(); ?>
	
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('View message'), array('action' => 'view', $message['Message']['id']));?></li>
		<li>&nbsp;</li>
		<li><?php echo $this->Html->link(__('List all messages'), array('action' => 'index'));?></li>
	</ul>
</div>
