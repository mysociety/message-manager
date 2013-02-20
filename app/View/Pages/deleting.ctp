<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: deleting -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Replying'), array('action' => 'replying'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Reply threads'), array('action' => 'reply_threads'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Deleting a message
    </h2>
    <p>
        If you're logged in with an admin or manager account, you can delete a message. Deleting is an 
        irreversible action, and under normal circumstances you don't need to do this. You should consider
        <?php echo $this->Html->link(__('hiding a message'), array('action' => 'hiding')); ?> rather than
        deleting it -- hiding a message effectively means you `api-user` users will never see it.
    </p>
    <p>
        To delete a message, first <?php echo $this->Html->link(__('view the message'), array('action' => 'viewing')); ?>
        and then click on <strong>Delete</strong>.
    </p>
</div>