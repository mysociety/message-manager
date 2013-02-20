<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: reply_threads -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Deleting'), array('action' => 'deleting'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Message sources'), array('action' => 'sources'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Reply threads
    </h2>
    <p>
        Reply threads consist of an incoming message and all the replies &mdash; and replies to replies, and replies to
        those replies, and so on.
    </p>
    <p>
        The Message Manager autodetects incoming replies by comparing the sender's number, and if that's a number to 
        which an <em>outgoing</em> message was recently sent, then it may allocate it as a reply.
        Sometimes it gets this wrong (unlike email, SMS messages, for example, don't have helpful subject lines to
        help with this).
    </p>
    <h3>
        Fixing an incorrectly marked reply
    </h3>
    <p>
        If a message has been attached to the wrong reply thread, or has been marked as a reply where it should
        not be one (or vice versa), you can fix it.
        You need to identify the id of the parent message, that is, the message to which the reply really
        belongs (alternatively, if it's not a reply at all, you fix this with no id). Then follow
        the instructions for 
        <?php echo $this->Html->link(__('changing the parent'), array('action' => 'editing')); ?>.
    </p>
    <h3>
        Fixing broken message threads
    </h3>
    <p>
        Sometimes Message Manager might get a little confused about message threads, especially if
        you've been editing parent ids. Go to the 
        <?php echo $this->Html->link(__('home page'), array('action' => 'home')); ?>
        and click on <strong>tree recovery</strong>, found near the bottom of the page.
    </p>
    <p>
        This rebuilds all the message threads by analysing and tidying the parent ids in each message record.
    </p>
</div>
