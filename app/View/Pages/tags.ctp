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
    <h3>Filtering messages by tag: no-tag</h3>
    <p>
        Each user account (login) has tags associated with it. These are used
        to filter the messages that are delivered by the <em>available</em> API
        call (in effect this means the messages that they can see within
        FixMyStreet).
    </p>
    <p>
        A user with no tags will be shown <em>all</em> incoming messages in
        response to the <code>available</code> API call.        
    </p>
    <p>
        In addition to nominating one or more tags which <em>must</em> match
        in order for the message to be shown, you can also specify a
        <code>NO-TAG</code> which applies to any message which was received
        without any tag.
    </p>
    <p>
        This is useful if you want staff to be able to access messages that
        are explicitly intended for them (i.e., have their tag) but also those
        that have been received without them. This is a little different from 
        a user with no tags at all specified. A use with tags <code>FOO</code>
        and <code>NO-TAG</code> specified will see messages with the
        <code>FOO</code> tag or no tag at all, but will <em>not</em> be shown
        messages tagged with <code>BAR</code>.
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
        Messages are never filtered by gateway tag because by the time they
        are stored in the Message Manager, those tags have been removed.
    </p>

</div>
