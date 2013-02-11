<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: assigning_fms -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Hiding'), array('action' => 'hiding'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Editing'), array('action' => 'editing'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Assigning a message to an FMS id
    </h2>
    <p>
        Within FMS, assigning an FMS id is handled automatically (in fact, it's initiated by an AJAX call when the problem report is created).
    </p>
    <p>
        However, you can also allocate an FMS ID manually within the Message Manger. Navigate directly to the message, enter the FMS ID and press <strong>Assign FMS</strong>. Note that no check is made to ensure that this is a valid ID. If you want to check that it's the message you intended, 
        click on the link to the FMS record that is shown when you
        <?php echo $this->Html->link('view the message', array('action' => 'viewing')); ?>.
        
    </p>
    <p>
        The FixMyStreet instance on which this ID applies is implied by the
        config setting <code>fms_site_url</code> 
        (which is currently set to <code><?php echo Configure::read('fms_site_url'); ?></code>).
    </p>
</div>
