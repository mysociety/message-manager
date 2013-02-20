<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: hiding -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Locks'), array('action' => 'locks'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Assigning FMS'), array('action' => 'assigning_fms'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Hiding messages
    </h2>
    <p>
        Hiding a message means it will never be included in the response to 
        an <code>available</code> API call. This effectively means it does not
        appear in FixMyStreet.
    </p>
    <p>
        Messages can be hidden by an API-user from within FixMyStreet. Alternatively, an admin or manager user can directly hide a message within
        Message Manger by
        <?php echo $this->Html->link('viewing the message', array('action' => 'viewing')); ?>
        and clicking <strong>Hide</strong>.
    </p>
    <h3>Revealing (un-hiding)</h3>
    <p>
        There's no way to un-hide a message from within the FMS interface &mdash; this is to be expected because hidden messages basically never appear there. However, and admin or manager account can log into Message Manager 
        directly, <?php echo $this->Html->link('view the message', array('action' => 'viewing')); ?>
        and click <strong>Reveal/un-hide</strong>.
    </p>
</div>
