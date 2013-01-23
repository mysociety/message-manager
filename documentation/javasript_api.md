# Message Manager JSON API

FixMyStreet communicates with the Message Manager with AJAX calls sending JSON.

The Message Manager doesn't make all its data available over the API. For example,
FMS users don't normally need phone numbers and activity details, so those
are not sent. If your users do need to access that kind of detail, then grant them 
login access to the Message Manager admin (probably as a 
<?php echo $this->Html->link(__('user'), array('controller'=>"Users", 'action'=>'index'));?> 
in the `managers` group).

## Full working example and `message_manager_client.js`

This Message Manager includes a dummy client (which by default is running at 
<?php echo $this->Html->link(__('/client'), array('controller'=>"MessageSources", 'action'=>'client'));?> 
&mdash; although that might be disabled if you're looking at this on a production server) which uses this API to do all the things
that a FixMyStreet integration needs. In the codebase, look inside 
`<a href="/js/message_manager_client.js">message_manager_client.js</a>` to see the API being used.
The dummy client makes its (custom) calls from 
`<a href="/js/dummy_client.js">dummy_client.js</a>`

In fact, the `message_manager_client.js` file is currently <i>identical</i> to the one used in the FixMyStreet
application and if you want to build your own code for talking to the Message Manager you'll probably want to drop that
file into your own application as it is, and make your JavaScript calls via the `message_manager` object it 
creates. Note that the functions on that object allow you to specific options, including callback functions to run after the
calls have been completed, so you should be able to write custom code quickly. If you need to change the behaviour of
`message_manager_client.js`'s `message_manager`, please let us know because it may be something
that we can add back to the codebase for everyone's benefit.

		Summary: the rest of this document describes the JSON API, but it's probably much easier to use
		`<a href="/js/message_manager_client.js">message_manager_client.js</a>` to create a 
		`message_manager` object (which has the API calls in it),  and call the equivalent methods on that. See 
		
## API Summary

		<li>GET `/messages/available`  with optional `fms_id=<em>FMS-id</em>`</li>
		<li>POST `/messages/hide/<em>msg-id</em>` with optional `reason_text=<em>reason_text</em>`</li>
		<li>POST `/messages/lock/<em>msg-id</em>`</li>
		<li>POST `/messages/lock_unique/<em>msg-id</em>`</li>
		<li>POST `/messages/unlock/<em>msg-id</em>`</li>
		<li>POST `/messages/unlock_all`</li>
		<li>POST `/messages/reply/<em>msg-id</em>` with `reply_text=<em>reply text</em>`</li>
		<li>POST `/messages/assign_fms_id/<em>msg-id</em>` with `fms_id=<em>FMS-id</em>`</li>

## Authorisation credentials

		Access to the API is either by login (user session) or HTTP Basic Auth by supplying credentials on a per-call basis.

## 404 errors for message not found

Calls with a message id in the URL which cannot be found return HTTP error code 404, rather than
`success=false`. If you're implementing responses, remember to check the returned
error code first!

## Message data
	 
The calls that return message data do so with the following structure. Note the `children` entry 
which contains more messages (children are messages received as direct replies to this, the parent message).
Because replies can have replies, the children may themselves have non-empty `children`. 

	<ul>
		<li><strong>Message</strong>: 
			the message data
		</li>
		<li><strong>Source</strong>: 
			the source which provided this message (such as the SMS gateway it came from)
		</li>
		<li><strong>Status</strong>: 
			the name of the status of this message (although this will often be status `available`, other values
			are possible as replies or archived messages &mdash; in fact the only status you'll never get is `hidden`). 
			The name of each status is unique, as you'd expect, but the returned data does also send its underlying `id`.
		</li>
		<li><strong>Lockkeeper</strong>: 
			the username and id of the current owner of the record lock (which may often be null, if there is no lock).
			Technically a username  <em>could</em> change (if edited by an administrator), so the underlying `owner_id` 
			may be better to use programmatically.
		</li>
		<li><strong>children</strong>: 
			Messages that are direct replies to this one. Since these may also have replies, this is how a message thread is
			represented.
		</li>
	</ul>

	The `sender_token` will be a value that is unique for a given user (so two messages with identical
	tokens will have been sent by the same user). For incoming messages, these are unique
	hashes not actual addresses because the JSPN API doesn't expose the actual from-address 
	(i.e., the senders' phone numbers/MSIDNs). However, note that for outgoing messages (that is, 
	where `is_outbound=='1'`), the `sender_token` is the Message Manager username 
	of the staff member who sent the reply. 

	The `parent_id` is the ID of the message (if any) to which <em>this</em> message is a reply. Since the
	tree-like structure of the messages and their replies is represented by the nested `children` entry,
	you probably don't need to use this. Similarly, the `lft` and `rght` entries are part of 
	the tree structure and can be ignored.

	<pre>
{
  "Message": {
    "id":           "1062",
    "external_id":  null,
    "sender_token": "8b1a9953c4611296a827abf8c47804d7",
    "message":      "This is the message text",
    "created":      "2012-05-25 01:02:00",
    "received":     "2012-06-11 02:38:29",
    "replied":     null,
    "lock_expires": "2012-06-11 21:30:48",
    "fms_id":       null,
    "tag":          "LUZ",
    "is_outbound":  "0",
    "lft":          "0",
    "rght":         "0",
    "parent_id":    "0",
  },
  "Source": {
    "id":           12,
    "name":         "Hobbiton SMS Gateway"
  },
  "Status": {
    "id":           1,
    "name":         "available"
  },
  "Lockkeeper": {
    "id":           16,
    "username":     "bilbo"
  }
  "children": {
    [...]
  }
}
	</pre>
	
## Operations
	
### Get available messages

	<dl>
		<dt>address</dt>
		<dd >
			`/messages/available`
		</dd>
		<dt>params</dt>
		<dd>`fms_id=`<em>id of the current FMS report (see below)</em> (optional)</dd>
		<dt>method</dt>
		<dd>GET</dd>
		<dt>operation</dt>
		<dd>
			Get list of available messages for populating selection list: this <em>only</em>
			includes messages which are candidates for assignment (so, message that are 
			hidden or which have already been assigned to an FMS report are not included).

			Furthermore, only messages with a tag which matches one of the user's <em>allowed_tags</em>
			will be returned.

			Note that the from-address (e.g., the sender's phone number) of incoming messages is not included in this data
			(but an MD5 hash of it is).

			In addition, this call will also return all the (non-hidden) messages that are associated with
			the FMS report (if `fms_id` was provided). Typically this may be a single message (i.e.,
			the one that was used to generate the report in the first place), and it almost certainly will
			<em>not</em> be of status `available` (since it's been assigned to this report). It's
			possible that there are more than one such message, and that they have replies too. There is a
			case for making this a separate API call, but to minimise HTTP requests from the client, it's been
			rolled into a feature of `/messages/available`.

			If no `fms_id` param is provided, or it is invalid, or there are no message associated with
			that FMS id, then `messages_for_this_report` will be `false`.

		</dd>
		<dt>returns</dt>
		<dd>

			The available call returns the available `messages`, the current user's `username`, 
			and any `messages_for_this_report`. See the description above for the structure of message objects.

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
  ],
  "messages_for_this_report":
  [
    {
      "Message":   {...},
      "Source":    {...},
      "Status":    {...},
      "Lockkeeper":{...}
     },
  ...
  ],
  "username": 6
}
</pre>
		</dd>
	</dl>

### Hide message
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/hide/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd>`reason_text=`<em>string explaining why the message was hidden</em> (optional)</dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Hides a message by setting its status to <em>hidden</em>. Hidden messages are not included in the 
			messages returned by a call to `/available` so hiding a message
			effectively removes it from the pool of available messages (and their replies).

			The optional parameter `reason_text` may contain a string that explains why
			the message was hidden.

			Hidden messages are not actually deleted, but remain in the Message Manager database.
			They can be inspected (and potentially unhidden) by a manager or admin
			user within the Message Manager application.

			Currently, the reverse of this operation, `unhide` is not implemented as a JSON
			call because clients generally don't have the ID of a hidden message with which to make it.

		</dd>
		<dt>returns</dt>
		<dd>
			The `hide` call returns an array of one or two objects:

			<ul>
				<li><strong>success</strong>: 
					which is `true` or `false`
				</li>
				<li><strong>error</strong> (only on failure): 
					a message describing the fault
				</li>
			</ul>
		</dd>
	</dl>

	
### Lock message
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/lock/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Grants a lock on the message with id=<em>id</em>. The lock is needed in order to assign it to an FMS report.

			See also `/message/lock_unique/` below, which is the preferred way to acquire a lock.

		</dd>
		<dt>returns</dt>
		<dd>Identical to `/message/lock_unique/` below, which is the preferred way to acquire a lock.</dd>
	</dl>

### Lock message and relinquish all other locks
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/lock_unique/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Grants a lock on the message with id=<em>id</em>. The lock is needed in order to assign it to an FMS report.

			This call is identical to the `/messages/lock/` operation, except that all other locks currently
			owned by this user will be relinquished.

			This is the recommended way to acquire locks.

		</dd>
		<dt>returns</dt>
		<dd>
			The `lock_unique` call returns an array of three objects:

			<ul>
				<li><strong>success</strong>: 
					which is `true` or `false`
				</li>
				<li><strong>data</strong>: 
					currently, successful locks also return the message data, which is a `message` object with the same fields
					as an entry from the `available` JSON call, above.
				</li>
				<li><strong>error</strong> (only on failure): 
					a message describing the fault
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			If the lock is granted, `success==true`, and the data is also returned:

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

If the lock was not granted, `success==false` and the response will provide an `error` message.
Currently, the message's data is <em>not</em> returned on failure, as shown here:

<pre>
{
  "success":  false,
  "data":     null,
  "error":    "Lock not granted (locked by another user)"
}
</pre>
		</dd>
	</dl>
	
### Relinquish lock on message
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/unlock/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Relinquishes a lock on the message with id=<em>id</em>.

			See also `/message/unlock_all/` below, which releases <em>all</em> locks held by this user.

			Calling `unlock` on a message which is not locked, or which is not owned by the user,
			is not an error: it succeeds with no effect upon the message.

		</dd>
		<dt>returns</dt>
		<dd>
			The `unlock` call returns an array of two objects:

			<ul>
				<li><strong>success</strong>: 
					which is `true` or `false`
				</li>
				<li><strong>data</strong>: which is null.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			If the lock is relinquished, `success==true`

	<pre>
{
  "success":     true,
  "data":        null 
}
</pre>
Because attempting to unlock a message that was not locked, or that is locked by another
user, is not reported as failure, a `false` response does not occur. However, 
other failures return an explicit HTTP response code (such as 404 for message not found) .

You cannot use the result of `unlock` to determine whether or not a message is now unlocked.

		</dd>
	</dl>
### Relinquish lock on all messages
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/unlock_all`
		</dd>
		<dt>params</dt>
		<dd><em>none</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			This is the same as `messages/unlock` except that it applies to all messages with a
			lock owned by this user. Like `unlock` described above, this fails silently for
			unchanged locks. Specifically, if there are no locks, the call still succeeeds.

			Nonetheless, check the returned HTTP status code to be sure the operation succeeded.

		</dd>
	</dl>
	
### Reply to  message
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/reply/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd>`reply_text=`<em>reply message to be sent to the message's sender'</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Sends a reply, via the message source, to the original sender.

			Note that the user must have the <em>can_send</em> privilege in order to do this, 
			otherwise the reply will fail.

			Furthermore, `success=true` does not imply that the reply has been sent
			through to the message source (although it might, this is not guaranteed). Instead,
			success indicates that the reply has been queued for delivery.

			You cannot inspect the progress of replies with the JSON API. A user in the managers
			or administrators groups can log into the Message Manager and view replies explicitly
			(they are listed as activity under the appropriate message).

		</dd>
		<dt>returns</dt>
		<dd>
			The available call returns an array of three objects:

			<ul>
				<li><strong>success</strong>: 
					which is `true` or `false`
				</li>
				<li><strong>data</strong>: 
					currently, successful locks also return the message data, which is a `message` object with the same fields
					as an entry from the `available` JSON call, above.
				</li>
				<li><strong>error</strong> (only on failure): 
					the name of the status of this message (currently only `available` messages are provided). This is the pretty name
					for the `status` value provided in `Message`.
				</li>
			</ul>
		</dd>
		<dt>example</dt>
		<dd>
			If the reply is accepted and has been queued for delivery, `success==true`

<pre>
{
  "success":     true,
  "data":        null
}
</pre>
If the assignment failed `success==false` and will provide an `error` message.

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

### Assign FMS ID
	<dl>
		<dt>address</dt>
		<dd >
			`/messages/assign_fms_id/<em>id</em>`
		</dd>
		<dt>params</dt>
		<dd>`fms_id=`<em>id of FMS problem report</em></dd>
		<dt>method</dt>
		<dd>POST</dd>
		<dt>operation</dt>
		<dd>
			Assigns the FMS ID to this the message with id=`<em>id</em>`.

			If successful, the message's status will change from `available` to `assigned`.

		</dd>
		<dt>returns</dt>
		<dd>
			The available call returns an array of three objects:

			<ul>
				<li><strong>success</strong>: 
					which is `true` or `false`
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
			If the FMS ID is assigned, `success==true`:

<pre>
{
  "success":     true,
  "data":        null
}
</pre>
If the assignment failed `success==false` and will provide an `error` message.
Currently, the message's data is not returned on failure:

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

