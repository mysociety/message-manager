<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: password -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('User groups'), array('action' => 'groups'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Boilerplate strings'), array('action' => 'strings'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Changing your password
    </h2>
    <p>
        You can change your own password by logging into the Message Manager
        and clicking <strong>password</strong> on the top right of the page.
    </p>
    <p>
        You need to enter your current password as well. If you've forgotten
        your password, ask an administrator to reset it for you.
    </p>
    <p>
        If you are logged in with an administrator's account, you can change
        any user's password by choosing <strong>users</strong> and then 
        <strong>Edit</strong>.
</div>

