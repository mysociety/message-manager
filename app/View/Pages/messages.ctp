<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: messages -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Overview'), array('action' => 'overview'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Viewing'), array('action' => 'viewing'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        About messages
    </h2>
    <p>
        Messages are received from 
        <?php echo $this->Html->link('message sources', array('action' => 'sources')); ?>
        — for a simple set-up this might be a single SMS gateway. 
    </p>
    <h3>What can happen to a message</h3>
    <p>
        By way of an example, these are the sort of actions that can apply 
        to a message. Some of these operations can happen through the API,
        that is, from within the FMS interface &mdash; otherwise they are
        performed by logging directly into the Message Manager with an admin
        or manager account.
    </p>
	<ul>
		<li>incoming message received from message source (message created)</li>
		<li>message offered to nominated FMS users (e.g., staff accounts)</li>
		<li>FMS user assigns message to FMS report</li>
		<li>FMS user replies to incoming message (creating a new one)</li>
		<li>message hidden (no longer accessible via API)</li>
		<li>message tags edit</li>
		<li>message threads reorganised (e.g., if a message is incorrectly placed in the wrong thread)</li>
	</ul>
	<h3>
	    Message fields
	</h3>
	<p>
        Message have the following fields:
    </p>
    <dl>
        <dt>
            From address
        </dt>
        <dd>
            <p>
                The address (typically a phone number) of the sender of the message.
            </p>
        </dd>
        <dt>
            Sender token
        </dt>
        <dd>
            <p>
                A token that is unique for the sender address (e.g., phone number) (actually it's an MD5 hash of the address). You can use the token to determine when two messages have come from the same sender without knowing their number (this is useful because the JSON API doesn't share phone numbers).
            </p>
        </dd>
        <dt>
            Message
        </dt>
        <dd>
            <p>
                The message text itself. This may have had the <em>tag</em> stripped off it, depending on the system-wide configuration setting <code>remove_tags_when_matched</code> (which is currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
            </p>
        </dd>
        <dt>
            Status
        </dt>
        <dd>
            <p>
                The current status of the message. See <?php echo $this->Html->link('statuses', array('action' => 'statuses')); ?>
            </p>
        </dd>
        <dt>
            Lock
        </dt>
        <dd>
            <p>See <?php echo $this->Html->link('locks', array('message locks' => 'locks')); ?> for details.
            <p>
                A message is locked by an FMS user when they select it before using it to create or update an FMS problem report. The Message Manager shows the owner (in the API, this user is called the Lockkeeper) and the expiry time.
            </p>
            <p>
                When a lock is granted, it remains in place for <?php echo Configure::read('lock_expiry_seconds'); ?> seconds (this is the system-wide setting <code>lock_expiry_seconds</code>), unless a manager or administrator unlocks the message explicitly.
            </p>
        </dd>
        <dt>
            Note
        </dt>
        <dd>
            <p>
                You can add a note to any message when you are viewing it (there's an <em>Add note</em> button above its history/activity data). Notes can be seen by anyone who has access to the message's view page. Notes also appear as items in the message's history.
            </p>
        </dd>
        <dt>
            FMS ID
        </dt>
        <dd>
            <p>
                This is the ID of the report to which the message has been assigned, if any.
            </p>
        </dd>
        <dt>
            Received
        </dt>
        <dd>
            <p>
                When the message source received the message (if known).
            </p>
        </dd>
        <dt>
            Created
        </dt>
        <dd>
            <p>
                When the message was first accepted in the Message Manager.
            </p>
        </dd>
        <dt>
            Modified
        </dt>
        <dd>
            <p>
                The timestamp of the last change to the message. See also the message's activity log, which records each event during the message's lifecycle.
            </p>
        </dd>
        <dt>
            Replied
        </dt>
        <dd>
            <p>
                The timestamp of the last time a reply was sent to this message, if any.
            </p>
            <p>
                The replies appear in the activity log of the message.
            </p>
        </dd>
        <dt>
            Source
        </dt>
        <dd>
            <p>
                The message source (for example, an SMS gateway) from which the message originated.
            </p>
        </dd>
        <dt>
            External&nbsp;ID
        </dt>
        <dd>
            <p>
                Some message sources allocate a unique ID for each message. This is the external ID, if any, that the source provided when it offered the message to the Message Manager.
            </p>
        </dd>
        <dt>
            Tag
        </dt>
        <dd>
            <p>See 
            <?php echo $this->Html->link('tags', array('action' => 'tags')); ?> for details.
            </p>
            <p>
                The tag, if any, that was found in the incoming message text. This is typically used to identify areas or jurisdictions that the messages relate to, and is the first word in the message.
            </p>
            <p>
                The accepted tags (which are not case dependent) are a system-wide setting. Current values are:
            </p>
            <dl>
                <?php $tags = Configure::read('tags'); 
                                foreach ($tags as $tag => $full) { ?>
                <dt>
                    <?php echo h($tag); ?>
                </dt>
                <dd>
                    <?php echo h($full); ?>
                </dd><?php } ?>
            </dl>
            <p>
                The Message Manager may strip tags out of the message text automatically, depending on the system-wide configuration setting <code>remove_tags_when_matched</code> (which is currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
            </p>
        </dd>
        <dt>
            History/activity
        </dt>
        <dd>
            <p>See 
            <?php echo $this->Html->link('activity (logs)', array('action' => 'activity')); ?> for details.
            </p>
            <p>
                Most events that cause a change in a message's status or data are recorded and added to the message's activities. These are useful if you need to know what happened to a message after it was accepted from the message source, and which users processed it.
            </p>
            <p>
                The text of replies sent through the Message Manager are stored here.
            </p>
        </dd>
    </dl>
</div>
