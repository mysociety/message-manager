<div class="mm-page">
    <ul class="mm-help">
    <!-- nav for: locks -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Tags'), array('action' => 'tags'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Hiding'), array('action' => 'hiding'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Message locks
    </h2>

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
</div>