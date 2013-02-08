<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: groups -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Users'), array('action' => 'users'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Changing password'), array('action' => 'password'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        User groups
    </h2>
    <p>
        Users belong to one of four groups. Most FixMyStreet users will be api-users, that is, they won't need login access to the Message Manager website — they will only be logging in via FixMyStreet in order to use the JSON API.
    </p>
    <h4>
        administrators
    </h4>
    <p>
        Administrators have full access to this Message Manager.
    </p>
    <h4>
        managers
    </h4>
    <p>
        Managers have access to the Message Manager in order to view and manipulate message data (for example, hiding messages, or adding/changing incorrect tags) but they cannot create or delete users. They also have access to the JSON API, so can use their login from within FMS too.
    </p>
    <h4>
        api-users
    </h4>API users have limited access to the Message Manager but can use the JSON API.
    <h4>
        message-sources
    </h4>This group is used to allocate username/password authorisation credentials to message sources (such as SMS gateways), which can then connect to the <code>/messages/incoming/</code> URL to submit incoming messages. Users in this group have no other access to Message Manager.
</div>
