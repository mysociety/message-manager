<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: users -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Message sources'), array('action' => 'sources'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('User groups'), array('action' => 'groups'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Users
    </h2>
    <p>
        Each user account has a username and (optionally) a unique email address. Either can be used to log into the Message Manager, although the
        current implementation of the FixMyStreet client (using HTTP auth)
        only allows username.
    </p>
    <p>
        Users can belong to one of four 
        <?php echo $this->Html->link('user groups', array('action' => 'groups')); ?>
        which affect what they can access and which actions they can perform.
    </p>
    <p>
        Administrator users can create, edit or delete users &mdash; click on
        <strong>Users</strong> on the top menu bar.
    </p>
    <h3>
        Enabling users to send messages
    </h3>
    <p>
        A user can only send messages (that is, can only reply to incoming messages) if the account has 
        <em>Can send replies?</em> set. To change this setting, edit the user.
    </p>
</div>
