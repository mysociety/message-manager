# Incoming Messages

Example of an *incoming message*: a text message received by an SMS gateway. 

# Incoming Messages: polling the gateway

The FixMyBarangay polls the gateway for new messages. This mechanism is custom: see the
Netcast shell in `Console/Command`
.
# Incoming Message: POSTing new messages

The default way to receive incoming messages is to allow the gatway to POST to a URL on the
Message Manager.

The Message Manager won't accept an incoming message without authentication, which is enforced
by login (username and password). So in order to allow a message source (for example,
an SMS gateway) to be able to submit new messages, you must create both a new *user* and a new
*message source* for it. The user, which should be in the `message-sources` user group,
 provides authentication when messages are submitted. The message source identifies the source (in case 
 your Message Manager is receiving messages from more than one source, for example) and is uniquely 
 associated with its single user.

Message sources can deliver incoming messages by POSTing to `/messages/incoming`.
Note that it is anticipated that each message source may send different parameters, so this is
currently the most "lazy" format, and it's likely that custom URLs will be needed on a case-by-case
basis.


<dt>address</dt>
<dd >
`/messages/incoming`
</dd>
<dt>params</dt>
<dd>
<strong>data[Message][source_id]</strong> <br/>
The ID of the message source.

<strong>data[Message][external_id]</strong> <br/>
If the message source has a unique ID for this message, it should provide it here.
If an external ID is provided, and a message already exists from this source with 
this ID, then the Message Manager will reject the incoming message. That is, if you
provide an external ID, then it _must_ be unique. It is recommended that
you do provide external IDs because this mechanism prevents duplicate submissions.

<strong>data[Message][from_address]</strong><br/>
The phone number or other address of the sender.

<strong>data[Message][message]</strong><br/>
The message text (which may start with a tag).

</dd>
<dt>method</dt>
<dd>POST</dd>
<dt>operation</dt>
<dd>
The incoming message is accepted and stored in the Message Manager &mdash; and consequently
given a status of `available`.

If the message starts with a recognised tag (keyword), it may be removed and stored separately 
(this behaviour depends on the system-wide configuration setting `remove_tags_when_matched`.

</dd>
<dt>returns</dt>
<dd>
The default response is a `text/plain` message. The HTTP status code of 200 does _not_
imply the message was accepted: check for `OK` on the first line of the response.

If the message is rejected because of validation errors, these are returned as text.

Other status codes may be returned if the message is rejected (for example, 403 Forbidden if the user is not
authorised to submit incoming messages).

</dd>
<dt>example</dt>
<dd>
A message that is successfully saved will generate a response like this:

<pre>
OK
Saved message id=1382
</pre>
A message that is rejected causes a response like the following &mdash; the response does <strong>not</strong>
have `OK` on the first line (but note that it is nonetheless sent with an HTTP 
status code of 200):

<pre>
Failed
the incoming message had validation errors:

error: A message from this source with this external ID already exists
</pre>
