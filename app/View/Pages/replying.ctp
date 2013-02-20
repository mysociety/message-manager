<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: replying -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Editing'), array('action' => 'editing'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Deleting'), array('action' => 'deleting'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Replying to messages
    </h2>
    <p>
        The Message Manager can send messages back in reply to incoming messages. Only users whose accounts are explicitly marked as
        <em>can reply</em> can send replies. To change this setting, you need to be logged in with an admin account. Then  
        <?php echo $this->Html->link(__('edit the user'), array('action' => 'users')); ?>
        and check the <em>Can send replies?</em> checkbox.
    </p>
    <p>
        You can do this from within FixMyStreet (for example, this is how users with `api-users` accounts will do it), or,
        if you have an admin or manager account, from within Message Manager once you've logged in.
    </p>
    <p>
        Note that when you send a reply, actually the reply does not go immediately &mdash; the Message Manager queues messages and
        sends them (via a <?php echo $this->Html->link(__('message source'), array('action' => 'sources')); ?> such as an 
        SMS gateway) in a batch. How frequently this happens depends on your system administrator. See the 
        <code>sent_*</code> <?php echo $this->Html->link(__('statuses'), array('action' => 'statuses')); ?> to
        understand the progress of your outbound message.
    </p>
</div>
