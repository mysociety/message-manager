<div class="mm-page">

          














<ul class="mm-help">
  <!-- nav for: sources -->
  <li class="mm-help-prev"><?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Reply threads'), array('action' => 'reply_threads'), array('escape' => false)); ?>
</li>
  <li class="mm-help-contents"><?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?></li>
  <li class="mm-help-next"><?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Users'), array('action' => 'users'), array('escape' => false)); ?>
</li>
</ul>

















	<h2>
        Message sources (SMS gateways, etc.) 
    </h2>
	<p>
		You must have at least one message source nominated for the Message Manager to be able to accept incoming
		messages. If you are accepting messages from multiple sources, this also lets you keep track of which 
		message came from which source.
	</p>
	<p>
		You'll need to associate each message source with a user who is in the <code>message-sources</code> group.
		This user's username and password will be needed by the source in order to authenticate the submission of
		incoming messages.
	</p>
</div>    






