<div class="mm-page">
	<h2>APIs</h2>
	<p>
		There are two ways to interact with the Message Manager from outside: submit incoming messages,
		or manipulate messages with the JSON API.
	</p>
	<ul>
		<li><a href="#incoming">Incoming Messages</a></li>
		<li><a href="#json_api">JSON API</a></li>
	</ul>
	<a name="incoming"></a>
	<h2 style="margin-top: 2.5em;">Incoming Messages</h2>
	<p>
		Example of an <b>incoming message</b>: a text message received by an SMS gateway. 
	</p>
	<p>
		The Message Manager won't accept an incoming message without authentication, which is enforced
		by login (username and password). So in order to allow a message source (for example,
		an SMS gateway) to be able to submit new messages, you must create both a new 
		<?php echo $this->Html->link(__('user'), array('controller'=>"Users", 'action'=>'index'));?> and a new
		<?php echo $this->Html->link(__('message source'), array('controller'=>"MessageSources", 'action'=>'index'));?>
		 for it. The user, which should be in the <code>message-sources</code> user group,
		 provides authentication when messages are submitted. The message source identifies the source (in case 
		 your Message Manager is receiving messages from more than one source, for example) and is uniquely 
		 associated with its single user.
	</p>
	<p>
		Message sources can deliver incoming messages by POSTing to <code>/messages/incoming</code>.
		Note that it is anticipated that each message source may send different parameters, so this is
		currently the most "lazy" format, and it's likely that custom URLs will be needed on a case-by-case
		basis.
	</p>
	<dl>
		<dt>address</dt>
		<dd >
			<code>/messages/incoming</code>
		</dd>
		<dt>params</dt>
		<dd>
			<p>
				<strong>data[Message][source_id]</strong> <br/>
				The ID of the message source.
			</p>
			<p>
				<strong>data[Message][external_id]</strong> <br/>
				If the message source has a unique ID for this message, it should provide it here.
				If an external ID is provided, and a message already exists from this source with 
				this ID, then the Message Manager will reject the incoming message. That is, if you
				provide an external ID, then it <strong>must</strong> be unique. It is recommended that
				you do provide external IDs because this mechanism prevents duplicate submissions.
			</p>
			<p>
				<strong>data[Message][from_address]</strong><br/>
				The phone number or other address of the sender.
			</p>
			<p>
				<strong>data[Message][message]</strong><br/>
				The message text (which may start with a tag).
			</p>
		</dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				The incoming message is accepted and stored in the Message Manager &mdash; and consequently
				given a status of <code>available</code>.
			</p>
			<p>
				If the message starts with a recognised tag (keyword), it may be removed and stored separately 
				(this behaviour depends on the system-wide configuration setting <code>remove_tags_when_matched</code>,
				which is currently set to <code><?php echo Configure::read('remove_tags_when_matched'); ?></code>).
			</p>
		</dd>
		<dt>returns</dt>
		<dd>
			<p> 
				The default response is a <code>text/plain</code> message. The HTTP status code of 200 does <strong>not</strong>
				imply the message was accepted: check for <code>OK</code> on the first line of the response.
			</p>
			<p>
				If the message is rejected because of validation errors, these are returned as text.
			</p>
			<p>
				Other status codes may be returned if the message is rejected (for example, 403 Forbidden if the user is not
				authorised to submit incoming messages).
			</p>
		</dd>
		<dt>example</dt>
		<dd>
			<p>
				A message that is successfully saved will generate a response like this:
			</p>
<pre>
OK
Saved message id=1382
</pre>
			<p>
				A message that is rejected causes a response like the following &mdash; the response does <strong>not</strong>
				have <code>OK</code> on the first line (but note that it is nonetheless sent with an HTTP 
				status code of 200):
			</p>
<pre>
Failed
the incoming message had validation errors:

error: A message from this source with this external ID already exists
</pre>
		</dd>
	</dl>

	<a name="json_api"></a>
	<h2 style="margin-top: 4em;">Message Manager JSON API</h2>
	<p>
		FixMyStreet communicates with the Message Manager with AJAX calls sending JSON.
	</p>
	<p>
		The Message Manager doesn't make all its data available over the API. For example,
		FMS users don't normally need phone numbers and activity details, so those
		are not sent. If your users do need to access that kind of detail, then grant them 
		login access to the Message Manager admin (probably as a 
		<?php echo $this->Html->link(__('user'), array('controller'=>"Users", 'action'=>'index'));?> 
		in the <code>managers</code> group).
	</p>
	<h3>API Summary</h3>
	<ul>
		<li>GET <code>/messages/available</code></li>
		<li>POST <code>/messages/lock/<em>msg-id</em></code></li>
		<li>POST <code>/messages/lock_unique/<em>msg-id</em></code></li>
		<li>POST <code>/messages/unlock/<em>msg-id</em></code></li>
		<li>POST <code>/messages/unlock_all</code></li>
		<li>POST <code>/messages/reply/<em>msg-id</em></code> with <code>reply_text=<em>reply text</em></code></li>
		<li>POST <code>/messages/assign_fms_id/<em>msg-id</em></code> with <code>fms_id=<em>FMS-id</em></code></li>
	</ul>
	<h3>Authorisation credentials</h3>
	<p>
		Access to the API is either by login (user session) or HTTP Basic Auth by supplying credentials on a per-call basis.
	</p>
	<h3>404 errors for message not found</h3>
	<p>
		Calls with a message id in the URL which cannot be found return HTTP error code 404, rather than
		<code>success=false</code>. If you're implementing responses, remember to check the returned
		error code first!
	</p>
	<h3>Message data</h3>
	<p> 
            The calls that return message data do so with the following structure. Note the <code>children</code> entry 
            which contains more messages (children are messages received as direct replies to this, the parent message).
            Because replies can have replies, the children may themselves have non-empty <code>children</code>. </p>
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
  "children": {
    [...]
  }
}
	</pre>
	
	<h3>Operations</h3>
	
	<h4>Get available messages</h4>
	<dl>
		<dt>address</dt>
		<dd >
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
		<dd >
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
		<dd >
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
		<dd>Identical to <code>/message/lock_unique/</code> below, which is the preferred way to acquire a lock.</dd>
	</dl>

	<h4>Lock message and relinquish all other locks</h4>
	<dl>
		<dt>address</dt>
		<dd >
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
			<p>If the lock is granted, <code>success==true</code>, and the data is also returned:</p>
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
				Currently, the message's data is <em>not</em> returned on failure, as shown here:
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
	
	<h4>Relinquish lock on message</h4>
	<dl>
		<dt>address</dt>
		<dd >
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
				Because attempting to unlock a message that was not locked, or that is locked by another
				user, is not reported as failure, a <code>false</code> response does not occur. However, 
				other failures return an explicit HTTP response code (such as 404 for message not found) .
			</p>
			<p>
				You cannot use the result of <code>unlock</code> to determine whether or not a message is now unlocked.
			</p>
		</dd>
	</dl>
	<h4>Relinquish lock on all messages</h4>
	<dl>
		<dt>address</dt>
		<dd >
			<code>/messages/unlock_all</code>
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			<p>
				This is the same as <code>messages/unlock</code> except that it applies to all messages with a
				lock owned by this user. Like <code>unlock</code> described above, this fails silently for
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
		<dd >
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
				or administrators groups can log into the Message Manager and view replies explicitly
				(they are listed as activity under the appropriate message).
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
		<dd >
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
			<p>If the FMS ID is assigned, <code>success==true</code>:</p>
<pre>
{
  "success":     true,
  "data":        null
}
</pre>
		<p>
			If the assignment failed <code>success==false</code> and will provide an <code>error</code> message.
			Currently, the message's data is not returned on failure:
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

