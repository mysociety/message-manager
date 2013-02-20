<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: faq -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Dummy client'), array('action' => 'dummy'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">&nbsp;
        </li>
    </ul>
    <h2>
        FAQ
    </h2>
    <h3>
        How do I fix a reply that's been stuck on the wrong message thread?
    </h3>
    <p>
        You need to identify the id of the parent message, that is, the message to which the reply really
        belongs (alternatively, if it's not a reply at all, you fix this with no id). Then follow
        the instructions for 
        <?php echo $this->Html->link(__('changing the parent'), array('action' => 'editing')); ?>.
    </p>
    <h3>
        Something's broken in the message threads &mdash; the replies seem wrong. Can I fix it?
    </h3>
    <p>
        Sometimes Message Manager might get a little confused about message threads, especially if
        you've been editing parent ids. Go to the 
        <?php echo $this->Html->link(__('home page'), array('action' => 'home')); ?>
        and click on <strong>tree recovery</strong>, found near the bottom of the page.
    </p>
     <h3>
         Can I change the text of a message?
    </h3>
    <p>
        No, currently no users &mdash; not even admin users &mdash; can edit the text of a message that
        has been received. You can
        <?php echo $this->Html->link(__('edit the message'), array('action' => 'editing')); ?> but that
        will only allow you to change its tag or parent (that is, to mark it as a reply).
        Note that when the text of the message is used to create a report on FixMyStreet, at that point
        it can be freely edited within the report.
    </p>
     <h3>
         Can I send an SMS to any number?
    </h3>
    <p>
        No, you can't enter a number to which to send an SMS. 
        The Message Manager can only send messages in reply to numbers from which incoming messages have
        been received.
    </p>
</div>
