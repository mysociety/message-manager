<div class="mm-page">
	<h2>Message Manager Help</h2>
	<p>
		See also: 
		<?php echo $this->Html->link(__('about'), "/about"); ?>
		|
		<?php echo $this->Html->link(__('JSON API'), array('action' => 'api')); ?>
	</p>
	<p>
		The Message Manager accepts incoming messages from one or more sources (such as SMS gateways) and
		makes those messages available to authorised FixMyStreet (FMS) users. From within FMS, messages
		can be assigned to FMS problem reports. The Message Manager handles all this and acts as a 
		conduit between the message sources and the FMS system, or its equivalent.
	</p>
	<h3> Messages </h3>
	<p>
		Messages are received from <em>message sources</em> &mdash; for a simple set-up this might be a single SMS gateway.
		Message have the following fields:
	</p>
	<dl>
		<dt>
			MSISDN
		</dd>
		<dd>
			<p>
				The address (typically a phone number) of the sender of the message.
			</p>
		</dd>
		<dt>
			Sender token
		</dd>
		<dd>
			<p>
				A token that is unique for the MSISDN (actually it's an MD5 hash of the MSISDN).
				You can use the token to determine when two messages have come from the same
				sender without knowing the MSISDN (this is useful because the JSON API doesn't 
				share MSISDNs).
			</p>
		</dd>
		<dt>
			Message
		</dd>
		<dd>
			<p>
				The message text itself. This may have had the <em>tag</em> stripped off it, depending on the 
				system-wide configuration setting <code>remove_tags_when_matched</code>
				(which is currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
			</p>
		</dd>
		<dt>
			Status
		</dd>
		<dd>
			<p>
				The current status of the message, which may be one of the following:
			</p>
			<table style="width:auto;" class="mm-messages">
				<tr>
					<td class="status-available">available</td>
					<td>
						Messages that have not yet been assigned to an FMS problem report are available. 
						These are the only messages that are provided over the JSON API and the only
						messages that an FMS user can lock. A lock is required before an FMS ID can be
						assigned.
					</td>
				</tr>
				<tr>
					<td class="status-assigned">assigned</td>
					<td>
						Messages that have been assigned to an FMS problem report will also have
						an FMS ID. They are not visible over the JSON API.
					</td>
				</tr>
				<tr>
					<td class="status-hidden">hidden</td>
					<td>
						A manager or administrator can mark a message as <em>hidden</em> in which case
						it is no longer shown.
					</td>
				</tr>
			</table>
		</dd>
		<dt>
			Lock
		</dd>
		<dd>
			<p>
				A message is locked by an FMS user when they select it before using it to create or
				update an FMS problem report. The Message Manager shows the owner (in the API, this
				user is called the Lockkeeper) and the expiry time.
			</p>
			<p>
				When a lock is granted, it remains in place for 
				<?php echo Configure::read('lock_expiry_seconds'); ?> seconds
				(this is the system-wide setting <code>lock_expiry_seconds</code>), 
				unless a manager or administrator unlocks the message explicitly.
		</dd>
		<dt>
		    Note
		</dt>
		<dd>
		    <p>
		        You can add a note to any message when you are viewing it (there's an <em>Add note</em>
		        button above its history/activity data). Notes can be seen by anyone who has access to
		        the message's view page. Notes also appear as items in the message's history.
		</dd>
		<dt>
			FMS ID
		</dd>
		<dd>
			<p>
				This is the ID of the report to which the message has been assigned, if any.
			</p>
		</dd>
		<dt>
			Received
		</dd>
		<dd>
			<p>
				When the message source received the message (if known).
			</p>
		</dd>
		<dt>
			Created
		</dd>
		<dd>
			<p>
				When the message was first accepted in the Message Manager.
			</p>
		</dd>
		<dt>
			Modified
		</dd>
		<dd>
			<p>
				The timestamp of the last change to the message. See also the message's activity log,
				which records each event during the message's lifecycle.
			</p>
		</dd>
		<dt>
			Replied
		</dd>
		<dd>
			<p>
				The timestamp of the last time a reply was sent to this message, if any.
			</p>
			<p>
				The replies appear in the activity log of the message.
			</p>
		</dd>
		<dt>
			Source
		</dd>
		<dd>
			<p>
				The message source (for example, an SMS gateway) from which the message originated.
			</p>
		</dd>
		<dt>
			External&nbsp;ID
		</dd>
		<dd>
			<p>
				Some message sources allocate a unique ID for each message. This is the external ID, if
				any, that the source provided when it offered the message to the Message Manager.
			</p>
		</dd>
		<dt>
			Tag
		</dd>
		<dd>
			<p>
				The tag, if any, that was found in the incoming message text. This is typically used
				to identify areas or jurisdictions that the messages relate to, and is the first word
				in the message.
			</p>
			<p>
				The accepted tags (which are not case dependent) are a system-wide setting. Current values are:
			</p>
			<dl>
				<?php $tags = Configure::read('tags'); 
				foreach ($tags as $tag => $full) { ?>
					<dt><?php echo h($tag); ?></dt>
					<dd><?php echo h($full); ?></dd>
				<?php } ?>
			</dl>
			<p>
				The Message Manager may strip tags out of the message text automatically,
				depending on the system-wide configuration setting <code>remove_tags_when_matched</code>
				(which is currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
			</p>
		</dd>
		<dt>
			History/activity
		</dd>
		<dd>
			<p>
				Most events that cause a change in a message's status or data are recorded and added to the 
				message's activities. These are useful if you need to know what happened to a message after
				it was accepted from the message source, and which users processed it.
			</p>
			<p>
				The text of replies sent through the Message Manager are stored here.
			</p>
		</dd>
	</dl>
	<h3>About message locks</h3>
	<p>
		Users on FMS generally don't need to know that locking is going on, but it's useful if managers and
		administrators are aware of how to use Message Manager to manipulate messages directly. A lock is
		always associated with a user, and lasts for <?php echo Configure::read('lock_expiry_seconds'); ?> seconds
		(this is a system-wide setting).
	</p>
	<p>
		Most users won't be aware of locks until they attempt to assign an FMS ID or send a reply to a message
		which another user has already locked.
	</p>
	<h4>Unlocking</h4>
	<p>
		A user cannot assign a message to an FMS ID without first securing a lock. If you are using the 
		Message Manager directly, you can remove a lock from any locked message (thereby unlocking it).
		Click on <strong>View</strong> to inspect the message, then click on <strong>Unlock</strong>.
	</p>
	<p>
		The JSON API provides and <code>unlock</code> and <code>unlock_all</code> methods too.
		Given that locks expire automatically, and the recommended use of <code>unlock_unique</code> to
		acquire locks, you probably don't need to use <code>unlock</code>. A well-behaved
		implementation should call <code>unlock_all</code> when a user explicitly logs out.
	</p>
	<h4>Locking from within the admin</h4>
	<p>
		Locks are usually granted from FMS using the Message Manager's JSON API but you can also claim a
		lock explicitly from within the Message Manager itself. Inspect the message and click on
		<strong>Lock</strong> button. This will acquire the lock under your username for 
		<?php echo Configure::read('lock_expiry_seconds'); ?> seconds (this is a system-wide setting).
	</p>
	<p>
		Note that it's recommended that you use the <strong>Lock unique</strong> method when you can (see below).
	</p>
	<h4>Unique locking</h4>
	<p>
		Since a user can only allocate one FMS ID to one message at a time, typically it only makes sense to
		allow that user to hold a lock on one message at a time. Use <strong>Lock unique</strong>
		instead &mdash; this behaves in exactly the same way as <strong>Lock</strong>, except that it 
		<em>also</em> releases any other locks currently held by this user.
	</p>
	<p>
		This is nearly always what you want, and is the default behaviour for FMS users. If you are a
		developer implementing the JSON API yourself, make sure you're using <code>messages/lock_unique</code>
		rather than <code>message/lock</code> unless you know your particular situation is different.
		Note that the Message Manager currently does not support session-based locking, so if you know
		a user may be running more than one session, you can try running without unique locking. 
	</p>
	<h4>Purging locks</h4>
	<p>
		If you inspect a record with <strong>View message</strong>, the Message Manager provides a 
		<strong>Purge lock</strong> button if that message has a lock that has expired. 
		Furthermore you can click on <strong>Purge all locks</strong>
		to remove all expired locks from the system. Expired locks are never critical, so this
		is a safe operation. If you remove a lock on a message that a user <em>was</em> still 
		interacting with, they can always claim the lock again.
	</p>
	<p>
		 It may possible to purge expired locks automatically on a regular basis using a cron job.	
	</p>
		<h3> Message sources (SMS gateways, etc.) </h3>
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
	<h3> Users and Groups </h3>
	<p>
		Users belong to one of four groups. Most FixMyStreet users will be api-users, that is, they won't need
		login access to the Message Manager website &mdash; they will only be logging in via FixMyStreet in order
		to use the JSON API.
	</p>
	<h4>administrators</h4>
	<p>
		Administrators have full access to this Message Manager. 
	<p>
	<h4>managers</h4>
	<p>
		Managers have access to the Message Manager in order to view and manipulate message data 
		(for example, hiding messages, or adding/changing incorrect tags) but they cannot create or delete users. 
		They also have access to the JSON API, so can use their login from within FMS too.
	</p>
	<h4>api-users</h4>
		API users have limited access to the Message Manager but can use the JSON API.
	</p>
	<h4>message-sources</h4>
		This group is used to allocate username/password authorisation credentials to message sources 
		(such as SMS gateways), which can then connect to the <code>/messages/incoming/</code>
		URL to submit incoming messages. Users in this group have no other access to Message Manager.
	</p>
</div>
