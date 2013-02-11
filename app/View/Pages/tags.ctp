<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: tags -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Statuses'), array('action' => 'statuses'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Locks'), array('action' => 'locks'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Message tags
    </h2>
    <p>
        Message Manager handles two kinds of prefix tags on messages, both of which are optional. <em>Message tags</em> convey information about the message which is shared with users. <em>Gateway tags</em> are artefacts of the way certain gateways work and, if present, are deleted.
    </p>
    <h3>
        Message tags
    </h3>
    <p>
        You may have set the system up such that users can add a tag
        when they send their messages &mdash; for example to indicate the
        location or region of a problem, or the category to which it applies.
    <p>
    <p>
        For example, in the FixMyBarangay project, message tags <code>BSN</code>
        and <code>BSN</code> are used to indicate that the message relates to
        a problem in either barangay (area) Basak San Nikolas or Luz respectively.
    </p>
    <p>
        The message tag is identified and stored as part of the message record. Optionally, it
        can be stripped from the message text at this point too.
    </p>
    <p>
        Tags are useful because API users can be configured to only have access
        to messages that where marked with the tag that relates to their work.
        In this way, incoming messages are filtered so FMS users only see the messages that
        are relevant to themselves.
    </p>
    <p>
        A manager or admin user can manually allocate a tag to a message. This
        may be necessary if a message came in with no tag, or with the wrong
        tag. See <?php echo $this->Html->link('editing a message', array('action' => 'editing')); ?>.        
    </p>
    <p>
        The tags which the Message Manager recognises are defined by the configuration setting <code>tags</code>. Current values:
    </p>
    <dl style="margin-bottom:2em;">
		<?php $tags = Configure::read('tags'); 
		foreach ($tags as $tag => $full) { ?>
			<dt><?php echo strtoupper(h($tag)); ?></dt>
			<dd><?php echo h($full); ?></dd>
		<?php } ?>
	</dl>
	
    <p>
        The tags will be left in the message text unless the configuration setting <code>remove_tags_when_matched</code> is true (currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
    </p>
    <h3>
        Gateway tags
    </h3>
    <p>
        Some message sources accept messages with a prefix that is part of their routing. These tags, if present, can be automatically stripped from
        incoming messages. Once these tags have been stripped, they cannot
        be recovered, so you should only do this where it's part of the mechanism
        of the particular message source that you don't want appearing in
        the Message Manager itself.
    </p>
    <p>
        For example, if the message source accepts only SMS messages starting
        with <code>FMS</code> (probably because the same gateway is routing messages to
        multiple systems), you can remove them when they are accepted by the
        Message Manager by setting the configuration setting <code>strip_prefix_tags</code> to <code>array('FMS')</code>.
    </p>
    <p>
        Don't confuse gateway tags with message tags. Unlike message tags, gateway tags are stripped <em>and discarded</em> when the message is received.
    </p>
        
</div>
