<div class="mm-page">

    <ul class="mm-help">
        <li><?php echo $this->Html->link(__('Contents'), array('action' => 'help')); ?></li>
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






