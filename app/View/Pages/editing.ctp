<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: editing -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Assigning FMS'), array('action' => 'assigning_fms'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Replying'), array('action' => 'replying'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Editing messages
    </h2>
    <p>
        If you're logged in with an admin or manager account, you can edit parts of a message.
    </p>
    <p>
        To edit a message, first <?php echo $this->Html->link(__('view the message'), array('action' => 'viewing')); ?>
        and then click on <strong>Edit</strong>.
    </p>
    <p>
        The only fields of the message that you can edit are its <strong>tag</strong> and <strong>parent</strong>.        
        Admin users can edit the <strong>message&nbsp;text</strong> depending on the config setting
        <code>allow_message_text_edits</code>, which is currently set to 
        <?php echo Configure::read('allow_message_text_edits')?"allow":"disallow"; ?>
        such edits.
    </p>
    <p>
        Currently Message Manager does not support users editing any other fields directly.
    </p>
    <h3>
        Changing the tag
    </h3>
    <p>
        It's probably common for incoming messages to have either the wrong tag, or no tag at all. 
        You can change this here by simply changing the tag. The rest of the message will be unaffected.
        Tags 
        <?php echo $this->Html->link(__('affect which users see the message'), array('action' => 'tags')); ?>
        in FixMyStreet, so if you're an admin or manager then
        you may need to fix messages in this way from time to time.
    </p>
    <h3>
        Changing the parent
    </h3>
    <p>
        The Message Manager tries to automatically assign incoming messages to the right message thread, that is,
        it tries to autodetect if a message is a reply and add it to the right parent message.
        Sometimes it will get this wrong (typically because the user has sent a separate message whilst also
        having a reply thread off another message, or because a long time elapsed since the actual parent message
        was sent).
    </p>
    <p>
        If a message has been incorrectly marked as a reply when it is not one -- that is, it's a new incoming 
        message, plain and simple -- delete the parent id shown here. Messages that are not replies should have no
        parent id.
    </p>
    <p>
        Alternatively, if a message has incorrectly been marked as <em>not</em> a reply, or has been allocated as
        a reply to the <em>wrong</em> parent message, you can fix it by entering the correct id for the parent.
        To determine the id to use, 
        <?php echo $this->Html->link(__('view the parent message'), array('action' => 'viewing')); ?> and note the number
        in the URL &mdash; for example, the page <code>messages/view/932</code> will be showing the message with id
        932.
    </p>
</div>
