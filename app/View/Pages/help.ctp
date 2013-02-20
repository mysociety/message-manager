<div class="mm-page">
	<h2 style="margin-bottom:1em;">Message Manager Help</h2>
    <div style="float:left; margin: 0em 5em 0em 1em;">
        <ul>
            <li><?php echo $this->Html->link(__('Overview'), array('action' => 'overview')); ?></li>
            <li><?php echo $this->Html->link(__('Messages'), array('action' => 'messages')); ?>
                <ul>
                    <li><?php echo $this->Html->link(__('Viewing'), array('action' => 'viewing')); ?></li>
                    <li><?php echo $this->Html->link(__('Statuses'), array('action' => 'statuses')); ?></li>
                    <li><?php echo $this->Html->link(__('Tags'), array('action' => 'tags')); ?></li>
                    <li><?php echo $this->Html->link(__('Locks'), array('action' => 'locks')); ?></li>
                    <li><?php echo $this->Html->link(__('Hiding'), array('action' => 'hiding')); ?></li>
                    <li><?php echo $this->Html->link(__('Assigning FMS'), array('action' => 'assigning_fms')); ?></li>
                    <li><?php echo $this->Html->link(__('Editing'), array('action' => 'editing')); ?></li>
                    <li><?php echo $this->Html->link(__('Replying'), array('action' => 'replying')); ?></li>
                    <li><?php echo $this->Html->link(__('Deleting'), array('action' => 'deleting')); ?></li>
                    <li><?php echo $this->Html->link(__('Reply threads'), array('action' => 'reply_threads')); ?></li>
                </ul>
            </li>
        </ul>
    </div>
    <div style="float:left; margin: 0em 5em 0em 1em;">
        <ul>
            <li><?php echo $this->Html->link(__('Message sources'), array('action' => 'sources')); ?></li>
            <li><?php echo $this->Html->link(__('Users'), array('action' => 'users')); ?>
                <ul>
                    <li><?php echo $this->Html->link(__('User groups'), array('action' => 'groups')); ?></li>
                    <li><?php echo $this->Html->link(__('Changing password'), array('action' => 'password')); ?></li>
                </ul>
            </li>
            <li><?php echo $this->Html->link(__('Boilerplate strings'), array('action' => 'strings')); ?></li>
            <li><?php echo $this->Html->link(__('Activity (logs)'), array('action' => 'activity')); ?></li>
            <li><?php echo $this->Html->link(__('Dummy client'), array('action' => 'dummy')); ?></li>
            <li><?php echo $this->Html->link(__('FAQ'), array('action' => 'faq')); ?></li>
        </ul>
    </div>
    <p style="clear:both">
		The Message Manager accepts incoming messages from one or more sources (such as SMS gateways) and
		makes those messages available to authorised FixMyStreet (FMS) users. From within FMS, messages
		can be assigned to FMS problem reports. The Message Manager handles all this and acts as a 
		conduit between the message sources and the FMS system, or its equivalent.
	</p>
</div>
