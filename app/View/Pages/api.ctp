<div class="mm-page view">
	<h2>Message Manager JSON API</h2>
	<p>
		FMS communicates with the Message Manager with AJAX calls sending JSON.
	</p>
	<p>
		The Message Manager doesn't make all its data available over the API. For example,
		FMS users don't normally need MSISDN (phone numbers) and activity details, so those
		are not sent. If your users do need to access that kind of detail, then grant them 
		login access to the Message Manager admin (probably as a 
		<?php echo $this->Html->link(__('user'), array('controller'=>"Users", 'action'=>'index'));?> 
		in the <em>managers</em> group).
	</p>
	<h3>API Summary</h3>
	<ul>
		<li>GET <code>/messages/available</code></li>
		<li>POST <code>/messages/lock/<em>msg-id</em></code></li>
		<li>POST <code>/messages/lock_unique/<em>msg-id</em></code></li>
		<li>POST <code>/messages/unlock/<em>msg-id</em></code></li>
		<li>POST <code>/messages/unlock_all</code></li>
		<li>POST <code>/messages/reply/<em>msg-id</em></code> with <code>reply_text=<em>reply text</em></code></li>
		<li>POST <code>/messages/assign_fmd_is/<em>msg-id</em></code> with <code>fms_id=<em>FMS-id</em></code></li>
	</ul>
	<h3>Authorisation credentials</h3>
	<p>
		Access to the API is by login (user session) or TODO by supplying credentials on a per-call basis.
	</p>
	<h3>404 errors for message not found</h3>
	<p>
		Calls with a message id in the URL which cannot be found return HTTP error code 404, rather than
		a <code>success=false</code>. If you're implementing responses, remember to check the returned
		error code first!
	</p>
	<h3>Message data</h3>
	<p> The calls that return message data do so with the following structure: </p>
	<pre>
{
  "Message": {
    "id":           "1",
    "source_id":    12,
    "external_id":  null,
    "sender_token": "8b1a9953c4611296a827abf8c47804d7",
    "message":      "This is the message text",
    "created":      "2012-05-25 01:02:00",
    "received":     "2012-06-11 02:38:29",
    "replied":     null,
    "lock_expires": "2012-06-11 21:30:48",
    "status":       "1",
    "owner_id":     "3",
    "fms_id":       null,
    "tag":          "LUZ"
  },
  "Source": {
    "id":           12,
    "name":         "Hobbiton SMS Gateway"
  },
  "Status": {
    "name":         "available"
  },
  "Lockkeeper": {
    "username":     "bilbo"
  }
}
	</pre>
	
	<h3>Operations</h3>
	
	<h4>Get available messages</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/available</code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>GET</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Get list of available messages for populating selection list: this <em>only</em>
				includes messages which are candidates for assignment (so, message that are 
				hidden or which have already been assigned to an FMS report are not included).
			</p>
			<p>
				Furthermore, only messages with a tag which matches one of the user's <em>allowed_tags</em>
				will be returned.
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> The available call returns an array of objects, each of which contains:
			</p>
			<ul>
				<li><strong>Message</strong>: 
					the message data, including numerical values for status and owner_id which correspond to the equivalent string values (see below)
				</li>
				<li><strong>Source</strong>: 
					the source which provided this message (such as the SMS gateway it came from)
				</li>
				<li><strong>Status</strong>: 
					the name of the status of this message (currently only <code>available</code> messages are provided). This is the pretty name
					for the <code>status</code> value provided in <code>Message</code>.
				</li>
				<li><strong>Lockkeeper</strong>: 
					the username of the current owner of the record lock, who is represented by the 
					<code>owner_id</code> value provided in <code>Message</code>. Technically a username 
					<em>could</em> change (if edited by an administrator), so the underlying <code>owner_id</code> 
					may be better to use programmatically.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd class="mm-code">
<pre>
{"messages":
  [
    {
      "Message":   {...},
      "Source":    {...},
      "Status":    {...},
      "Lockkeeper":{...}
     },
  ...
  ]
}
</pre>
		</dd>
	</dl>
	
	<h4>Lock message</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/lock/<em>id</em></code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Grants a lock on the message with id=<em>id</em>. The lock is needed in order to assign it to an FMS report.
			</p>
			<p>
				See also <code>/message/lock_unique/</code> below, which is the preferred way to acquire a lock.
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> The available call returns an array of three objects:
			</p>
			<ul>
				<li><strong>success</strong>: 
					which is <code>true</code> or <code>false</code>
				</li>
				<li><strong>data</strong>: 
					currently, successful locks also return the message data, which is a <code>message</code> object with the same fields
					as an entry from the <code>available</code> JSON call, above.
				</li>
				<li><strong>error</strong> (only on failure): 
					a message describing the fault
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			<p>If the lock is granted, <code>success==true</code></p>
	<pre>
{
  "success":     true,
  "data":{
    "Message":   {...},
    "Source":    {...},
    "Status":    {...},
    "Lockkeeper":{...}
  }
}
</pre>
			<p>
				If the lock was not granted, <code>success==false</code> and the response will provide an <code>error</code> message.
				Currently, the message's data is <em>not</em> returned on failure.
			</p>
<pre>
{
  "success":  false,
  "data":     null,
  "error":    "Lock not granted (locked by another user)"
}
</pre>
		</dd>
	</dl>

	<h4>Lock message and relinquish all other locks</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/lock_unique/<em>id</em></code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Grants a lock on the message with id=<em>id</em>. The lock is needed in order to assign it to an FMS report.
			</p>
			<p>
				This call is identical to the <code>/messages/lock/</code> operation, except that all other locks currently
				owned by this user will be relinquished.
			</p>
			<p>
				This is the recommended way to acquire locks.
			</p>
		</dd>
	</dl>
	
	<h4>Relinquish lock on message</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/unlock/<em>id</em></code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Relinquishes a lock on the message with id=<em>id</em>.
			</p>
			<p>
				See also <code>/message/unlock_all/</code> below, which releases <em>all</em> locks held by this user.
			</p>
			<p>
				Calling <code>unlock</code> on a message which is not locked, or which is not owned by the user,
				is not an error: it succeeds with no effect upon the message.
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> The <code>unlock</code> call returns an array of two objects:
			</p>
			<ul>
				<li><strong>success</strong>: 
					which is <code>true</code> or <code>false</code>
				</li>
				<li><strong>data</strong>: which is null.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			<p>If the lock is relinquished, <code>success==true</code></p>
	<pre>
{
  "success":     true,
  "data":        null 
}
</pre>
			<p>
				Because there failure to relinquish a lock that was not locked, or that is locked by another
				user, is not reported as failure, a <code>false</code> response does not occur. However, 
				other failures (such as 404 for message not found) return an explicit HTTP response code.
			</p>
		</dd>
	</dl>
	<h4>Relinquish lock on all messages</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/unlock_all</code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				This is the same as <code>messages/unlock</em> except that it applies to all messages with a
				lock owned by this user. Like <code>unlock</code>, this won't report a fails silently for
				unchanged locks. Specifically, if there are no locks, the call still succeeeds.
			</p>
			<p>
				Nonetheless, check the returned HTTP status code to be sure the operation succeeded.
			</p>
		</dd>
	</dl>
	
	<h4>Reply to  message</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/reply/<em>id</em></code>
		</dd>
		<dt>params</dt>
		<dd><code>reply_text=</code><em>reply message to be sent to the message's sender'</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Sends a reply, via the message source, to the original sender.
			</p>
			<p>
				Note that the user must have the <em>can_send</em> privilege in order to do this, 
				otherwise the reply will fail.
			</p>
			<p>
				Furthermore, <code>success=true</code> does not imply that the reply has been sent
				through to the message source (although it might, this is not guaranteed). Instead,
				success indicates that the reply has been queued for delivery.
			</p>
			<p>
				You cannot inspect the progress of replies with the JSON API. A user in the managers
				or administrators groups can log into the Message Manager and view them explicitly.
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> The available call returns an array of three objects:
			</p>
			<ul>
				<li><strong>success</strong>: 
					which is <code>true</code> or <code>false</code>
				</li>
				<li><strong>data</strong>: 
					currently, successful locks also return the message data, which is a <code>message</code> object with the same fields
					as an entry from the <code>available</code> JSON call, above.
				</li>
				<li><strong>error</strong> (only on failure): 
					the name of the status of this message (currently only <code>available</code> messages are provided). This is the pretty name
					for the <code>status</code> value provided in <code>Message</code>.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			<p>If the reply is accepted and has been queued for delivery, <code>success==true</code></p>
<pre>
{
  "success":     true,
  "data":        null
}
</pre>
		<p>
			If the assignment failed <code>success==false</code> and will provide an <code>error</code> message.
		</p>
<pre>
{
  "success":  false,
  "data":     null,
  "error":    "failed: user does not have can_send privilege"
}
</pre>
		</dd>
	</dl>

	</dl>

	<h4>Assign FMS ID</h4>
	<dl>
		<dt>address</dt>
		<dd class="mm-code">
			<code>/messages/assign_fms_id/<em>id</em></code>
		</dd>
		<dt>params</dt>
		<dd><code>fms_id=</code><em>id of FMS problem report</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				Assigns the FMS ID to this the message with id=<code><em>id</em></code>.
			</p>
			<p>
				If successful, the message's status will change from <code>available</code> to <code>assigned</code>.
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> The available call returns an array of three objects:
			</p>
			<ul>
				<li><strong>success</strong>: 
					which is <code>true</code> or <code>false</code>
				</li>
				<li><strong>data</strong>: 
					currently, no data is returned.
				</li>
				<li><strong>error</strong> (only on failure): 
					if the FMS ID could not be assigned, a message describing the problem is returned.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			<p>If the FMS ID is assigned, <code>success==true</code></p>
<pre>
{
  "success":     true,
  "data":        null
}
</pre>
		<p>
			If the assignment failed <code>success==false</code> and will provide an <code>error</code> message.
			Currently, the message's data is not returned on failure.
		</p>
<pre>
{
  "success":  false,
  "data":     null,
  "error":    "failed: Not assigned: locked by another user"
}
</pre>
		</dd>
	</dl>

</div>
<?php echo $this->element('sidebar/pages'); ?>

