# Incoming Messages

An *incoming message* is a text message received by a message source, such as an SMS gateway, coming into the Message Manager.

## Incoming Messages: polling the gateway

The FixMyBarangay polls the gateway for new messages. This mechanism is custom: see the
Netcast shell in `app/Console/Command/NetcastShell.php`.

To get helpful usage information (and without actually running anything, so it's safe!) do:

    cd app
    Console/cake netcast

Alternatively, try `Console/cake netcast gateway_list` to see a list of the gateways (sources)
currently in your database. You'll need to specify one of these sources (by name or id) in most
of the subcommands you issue to this `netcast` shell.



## Incoming Message: POSTing new messages

The default way to receive incoming messages is to allow the gateway to POST to a URL on the
Message Manager.

> Note: because FixMyBarangay polls the gateway, it does *not* use this `incoming` action in
> `MessageController` (although the dummy client does, in development vhosts).

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


### Method & address

POST to `/messages/incoming`

### Parameters

    source_id
    external_id
    from_address
    message

#### `data[Message][source_id]`

The ID of the message source.

### `data[Message][external_id]`

If the message source has a unique ID for this message, it should provide it here.
If an external ID is provided, and a message already exists from this source with 
this ID, then the Message Manager will reject the incoming message. That is, if you
provide an external ID, then it _must_ be unique. It is recommended that
you do provide external IDs because this mechanism prevents duplicate submissions.

### `data[Message][from_address]`

The phone number or other address of the sender.

### `data[Message][message]`

The message text (which may start with a tag).

### Operation

The incoming message is accepted and stored in the Message Manager -- and consequently
given a status of `available`.

If the message starts with a recognised tag (keyword), it may be removed and stored separately 
(this behaviour depends on the system-wide configuration setting `remove_tags_when_matched`.


### Return value

The default response is a `text/plain` message. The HTTP status code of 200 does _not_
imply the message was accepted: check for `OK` on the first line of the response.

If the message is rejected because of validation errors, these are returned as text.

Other status codes may be returned if the message is rejected (for example, 403 Forbidden if the user is not
authorised to submit incoming messages).


### Example

A message that is successfully saved will generate a response like this:

    OK
    Saved message id=1382

A message that is rejected causes a response like the following -- the response does **not**
have `OK` on the first line (but note that it is nonetheless sent with an HTTP 
status code of 200):

    Failed
    the incoming message had validation errors:
    
    error: A message from this source with this external ID already exists

