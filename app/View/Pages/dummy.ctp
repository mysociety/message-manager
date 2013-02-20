<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: dummy -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Activity (logs)'), array('action' => 'activity'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('FAQ'), array('action' => 'faq'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        The dummy client
    </h2>
    <p>
        The dummy client should be disabled in production installations.
        This is controlled by the config setting <code>enable_dummy_client</code>, which
        is currently <code><?php echo(Configure::read('enable_dummy_client')?"true":"false"); ?></code>.
        If the dummy client is enabled, you can access it by clicking on <strong>Dummy client</strong>
        in the navigation menu.
    </p>
    <p>
    </p>
    <h3>
        Technical details
    </h3>
    <p>
        The dummy client is a feature in development/staging installations that mimics
        a client such as FixMyStreet, but runs on the Message Manager itself. The
        advantage of this is that it allows development or testing of the Message Manager
        functionality without needing a remote client. It also obviates the need for any CORS
        negotiation (because the AJAX calls are going to the same domain as the web page).
    </p>
    <p>
        Furthermore, if the dummy client is enabled, then users in the <code>message-sources</code>
        group can inject incoming messages directly into the Message Manager via a web form 
        available on the dummy client page (without needing an SMS gateway, for example).
    </p>
</div>
