<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: activity -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Boilerplate strings'), array('action' => 'strings'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Dummy client'), array('action' => 'dummy'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Activity (logs)
    </h2>
    <p>
        The activity logs record key activity affecting each message. These records are stored in the database and are 
        displayed when you <?php echo $this->Html->link(__('view a message'), array('action' => 'viewing')); ?>.
    </p>
    <p>
        You can also see the activity records directly by clicking on the <strong>Activity</strong> in the 
        navigation menu.
    </p>
    <h3>
        Action types currently supported
    </h3>
    <dl style="margin:1em;">
    <?php
        foreach ($action_types as $name => $desc) {
		    echo "<dt>$name</dt><dd>$desc</dd>\n";
	    }
    ?>
    </dl>
    <p>
        Because lock activity is so common, <code>lock</code> and <code>unlock</code> 
        actions are only logged if the config setting
        <code>log_lock_actions</code> is set. Currently this setting is
        <code><?php echo(Configure::read('log_lock_actions')?"true":"false"); ?></code>.
    </p>
    
</div>
