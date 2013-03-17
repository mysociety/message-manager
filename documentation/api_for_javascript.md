# Message Manager JSON API

FixMyStreet communicates with the Message Manager with AJAX calls sending
JSON.

The Message Manager doesn't make all its data available over the API. For
example, FMS users don't normally need phone numbers and activity details, so
those are not sent. If your users do need to access that kind of detail, then
grant them login access to the Message Manager admin (probably as a user in
the `managers` group).

## Full working example and `message_manager_client.js`

This Message Manager includes a dummy client (which by default is running at
`/client` -- although that might be disabled if you're looking at this on a
production server) which uses this API to do all the things that a FixMyStreet
integration needs. In the codebase, look inside
`/js/message_manager_client.js` to see the API being used. The dummy client
makes its (custom) calls from `dummy_client.js`

In fact, the `message_manager_client.js` file currently is *identical* to the
one used in the FixMyStreet application -- or, if it is isn't, it should be
:-) So if you want to build your own code for talking to the Message Manager
you'll probably want to drop that file into your own application as it is, and
make your JavaScript calls via the `message_manager` object it creates.

Note that the functions on that object allow you to specific options,
including callback functions to run after the calls have been completed, so
you should be able to write custom code quickly. If you need to change the
behaviour of `message_manager_client.js`'s `message_manager`, please let us
know because it may be something that we can add back to the codebase for
everyone's benefit.

The `message_manager_client.js` also makes some pretty stern decisions about
the HTML it produces and expects to find. This is not properly documented
(hopefully it will be later), but be aware that currently the code does make
strong assumptions about the HTML it's working with (especially the id's and
classes used... but there's also an implicit dependency on FancyBox too, hmm).

> Summary: the rest of this document describes the JSON API, but it's
> probably much easier to use `message_manager_client.js` to create a
> `message_manager` object (which has the API calls in it), and call the
> equivalent methods on that. The list of those methods is returned at
> the bottom of the .js file, as the revealed public methods.
		
## API Summary

* GET `/boilerplate_strings/index/[string-type]`
* GET `/messages/available`  with optional `fms_id=FMS-id`
* POST `/messages/hide/msg-id` with optional `reason_text=reason_text`
* POST `/messages/lock/msg-id`
* POST `/messages/lock_unique/msg-id`
* POST `/messages/mark_as_not_a_reply/msg-id`
* POST `/messages/unlock/msg-id`
* POST `/messages/unlock_all`
* POST `/messages/reply/msg-id` with `reply_text=reply text`
* POST `/messages/assign_fms_id/msg-id` with `fms_id=FMS-id`

## Authorisation credentials

Access to the API is either by login (user session) or HTTP Basic Auth by
supplying credentials on a per-call basis.

## 404 errors for message not found

Calls with a message id in the URL which cannot be found return HTTP error
code 404, rather than `success=false`. If you're implementing responses,
remember to check the returned error code first!

## Message data

The calls that return message data do so with the following structure. Note
the `children` entry which contains more messages (children are messages
received as direct replies to this, the parent message). Because replies can
have replies, the children may themselves have non-empty `children`.

*   **Message**: 

    the message data. Most fields are self-explanatory, but for clarification:
    
    *   **sender_token**

        The `sender_token` will be a value that is unique for a given user (so
        two messages with identical tokens will have been sent by the same
        user). For incoming messages, these are unique hashes not actual
        addresses because the JSPN API doesn't expose the actual from-address
        (i.e., the senders' phone numbers/MSIDNs). However, note that for
        outgoing messages (that is, where `is_outbound=='1'`), the
        `sender_token` is the Message Manager username of the staff member who
        sent the reply.

    *   **parent_id**, **lft**, **rght**

        The `parent_id` is the ID of the message (if any) to which *this*
        message is a reply. Since the tree-like structure of the messages and
        their replies is represented by the nested `children` entry, you
        probably don't need to use this. Similarly, the `lft` and `rght`
        entries are part of the tree structure and can usually be ignored.

*   **Source**: 

    the source which provided this message (such as the SMS gateway it came
    from)

*   **Status**: 

    the name of the status of this message (although this will often be status
    `available`, other values are possible as replies or archived messages --
    in fact the only status you'll never get is `hidden`). The name of each
    status is unique, as you'd expect, but the returned data does also send
    its underlying `id`.

*   **Lockkeeper**: 

    the username and id of the current owner of the record lock (which may
    often be null, if there is no lock). Technically a username *could* change
    (if edited by an administrator), so the underlying `owner_id` may be
    better to use programmatically.

*   **children**: 

    Messages that are direct replies to this one. Since these may also have
    replies, this is how a message thread is represented.


Example:

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
	
## Operations

### Get boilerplate strings

#### Method & address

GET from `/boilerplate_strings/index/[string-type]`

#### Parameters

None -- but note *[string-type]* may currently be either `reply` or
`hide-reason` (or nothing at all, for all strings).

The types available depend on the strings in the database (that is, this is
not a restriction within the code). Currently:

   * **reply**: boilerplate strings for use as outgoing replies to incoming
     messages

   * **hide-reason**: boilerplate strings for use as reasons why a message is
     being hidden

#### Operation

The boilerplate strings are provided as a handy way for staff to populate
common strings. See this being used by `message_manager_client.js` to populate
the drop-down menu for replying.

#### Return value

The `boilerplate_strings/index` call returns `success`, together with `data`
that groups the messages by language code. The messages themselves are keyed
by unique ID (Dev note: probably should just be an array (in display order) of
strings since the ID is not used; this may change in the future). For
convenience, the language codes for the languages returned are provided as an
array in `langs`.

Be sure to check you're using the string type you intend. If you ask for a
string type that Message Manager doesn't know about, you'll get a `success`
response with no data in it (that is `data` will contain `langs:[]`). This is
not an error -- you're really asking for strings keyed on that value. This is
the same behaviour as asking for valid key which has no strings associated
with it.

The call for boilerplate strings returns an object containing the following:

  * **success**: which is `true` or `false`

  * **data** an array keyed on language code, each of which contains an object
    which is itself a list of strings keyed on string ID. .
    One key is special: if (and only if) there are any language codes in the
    data, `langs` will itself contain an array of the language codes used as
    keys.

  * **error** (only on failure): a message describing the fault


### Example

    {
       "success": true,
       "data": {
         "en": {
           "6": "... Thank you.",
           "3": "Sorry, outside the scope of this service."
         },
         "de": {
           "8": "Entschuldigung: außerhalb des Umfangs dieser Dienstleistung."
         },
         "langs": ["de","en"]
       },
       "username":"6"
    }


### Get available messages

#### Method & address

GET from `/messages/available`

#### Parameters

`fms_id=`id of the current FMS report (see below) (optional)

#### Operation

Get list of available messages for populating selection list: this *only*
includes messages which are candidates for assignment (so, message that are
hidden or which have already been assigned to an FMS report are not included).

Furthermore, only messages with a tag which matches one of the user's
*allowed_tags* will be returned.

Note that the from-address (e.g., the sender's phone number) of incoming
messages is not included in this data (but an MD5 hash of it is).

In addition, this call will also return all the (non-hidden) messages that are
associated with the FMS report (if `fms_id` was provided). Typically this may
be a single message (i.e., the one that was used to generate the report in the
first place), and it almost certainly will *not* be of status `available`
(since it's been assigned to this report). It's possible that there are more
than one such message, and that they have replies too. There is a case for
making this a separate API call, but to minimise HTTP requests from the
client, it's been rolled into a feature of `/messages/available`.

If no `fms_id` param is provided, or it is invalid, or there are no message
associated with that FMS id, then `messages_for_this_report` will be `false`.


#### Return value

The available call returns the available `messages`, the current user's
`username`, and any `messages_for_this_report`. See the description above for
the structure of message objects.

#### Example

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


### Hide message

#### Method & address

POST to `/messages/hide/id`

#### Parameters

`reason_text=`string explaining why the message was hidden (optional)

#### Operation

Hides a message by setting its status to *hidden*. Hidden messages are not
included in the messages returned by a call to `/available` so hiding a
message effectively removes it from the pool of available messages (and their
replies).

The optional parameter `reason_text` may contain a string that explains why
the message was hidden.

Hidden messages are not actually deleted, but remain in the Message Manager
database. They can be inspected (and potentially unhidden) by a manager or
admin user within the Message Manager application.

Currently, the reverse of this operation, `unhide` is not implemented as a
JSON call because clients generally don't have the ID of a hidden message with
which to make it.


#### Return value

The `hide` call returns an array of one or two objects:

*   **success**: 
    which is `true` or `false`

* **error** (only on failure):
    a message describing the fault


### Lock message
	
#### Method & address

POST to `/messages/lock/id`

#### Parameters

*none*

#### Operation

Grants a lock on the message with id=id. The lock is needed in order to assign
it to an FMS report.

See also `/message/lock_unique/` below, which is the preferred way to acquire
a lock.


#### Return value

Identical to `/message/lock_unique/` below, which is the preferred way to
acquire a lock.


### Lock message and relinquish all other locks
	
#### Method & address

POST to `/messages/lock_unique/id`

#### Parameters

*none*

#### Operation

Grants a lock on the message with id=id. The lock is needed in order to assign
it to an FMS report.

This call is identical to the `/messages/lock/` operation, except that all
other locks currently owned by this user will be relinquished.

This is the recommended way to acquire locks.


#### Return value

The `lock_unique` call returns an array of three objects:

*   **success**: 
    which is `true` or `false`

* **data**: 
    currently, successful locks also return the message data, which is a
    `message` object with the same fields as an entry from the `available`
    JSON call, above.

* **error** (only on failure):
    a message describing the fault


#### Example

If the lock is granted, `success==true`, and the data is also returned:

    {
      "success":     true,
      "data":{
        "Message":   {...},
        "Source":    {...},
        "Status":    {...},
        "Lockkeeper":{...}
      }
    }

If the lock was not granted, `success==false` and the response will provide an
`error` message. Currently, the message's data is *not* returned on failure,
as shown here:

    {
      "success":  false,
      "data":     null,
      "error":    "Lock not granted (locked by another user)"
    }

	
	
### Relinquish lock on message
	
#### Method & address

POST to `/messages/unlock/id`

#### Parameters

*none*

#### Operation

Relinquishes a lock on the message with id=id.

See also `/message/unlock_all/` below, which releases *all* locks held by this
user.

Calling `unlock` on a message which is not locked, or which is not owned by
the user, is not an error: it succeeds with no effect upon the message.

#### Return value

The `unlock` call returns an array of two objects:

*   **success**: 
    which is `true` or `false`

*   **data**: 
	which is null


#### Example

If the lock is relinquished, `success==true`

    {
      "success":     true,
      "data":        null 
    }

Because attempting to unlock a message that was not locked, or that is locked
by another user, is not reported as failure, a `false` response does not
occur. However, other failures return an explicit HTTP response code (such as
404 for message not found) .

You cannot use the result of `unlock` to determine whether or not a message is
now unlocked.


### Relinquish lock on all messages
	
#### Method & address

POST to `/messages/unlock_all`

#### Parameters

*none*

#### Operation

This is the same as `messages/unlock` except that it applies to all messages
with a lock owned by this user. Like `unlock` described above, this fails
silently for unchanged locks. Specifically, if there are no locks, the call
still succeeds.

Nonetheless, check the returned HTTP status code to be sure the operation
succeeded.


### Reply to  message
	
#### Method & address

POST to `/messages/reply/id`

#### Parameters

`reply_text=`*reply message to be sent to the message's sender'*

#### Operation

Sends a reply, via the message source, to the original sender.

Note that the user must have the *can_send* privilege in order to do this,
otherwise the reply will fail.

Furthermore, `success=true` does not imply that the reply has been sent
through to the message source (although it might, this is not guaranteed).
Instead, success indicates that the reply has been queued for delivery.

You cannot inspect the progress of replies with the JSON API. A user in the
managers or administrators groups can log into the Message Manager and view
replies explicitly (they are listed as activity under the appropriate
message).


#### Return value

The available call returns an array of three objects:

*   **success**: 
    which is `true` or `false`

* **data**: 
    currently, successful locks also return the message data, which is a
    `message` object with the same fields as an entry from the `available`
    JSON call, above.

* **error** (only on failure):
    the name of the status of this message (currently only `available`
    messages are provided). This is the pretty name for the `status` value
    provided in `Message`.


#### Example

If the reply is accepted and has been queued for delivery, `success==true`

    {
      "success":     true,
      "data":        null
    }

If the assignment failed `success==false` and will provide an `error` message.

    {
      "success":  false,
      "data":     null,
      "error":    "failed: user does not have can_send privilege"
    }



### Assign FMS ID
	
#### Method & address

POST to `/messages/assign_fms_id/id`

#### Parameters

`fms_id=`*id of FMS problem report*

#### Operation

Assigns the FMS ID to this the message with id=`id`.

If successful, the message's status will change from `available` to
`assigned`.

#### Return value

The available call returns an array of three objects:

*   **success**: 
    which is `true` or `false`

*   **data**: 
    currently, no data is returned.

*   **error** (only on failure):
    if the FMS ID could not be assigned, a message describing the problem is
    returned.


#### Example

If the FMS ID is assigned, `success==true`:

    {
      "success":     true,
      "data":        null
    }

If the assignment failed `success==false` and will provide an `error` message.
Currently, the message's data is not returned on failure:

    {
      "success":  false,
      "data":     null,
      "error":    "failed: Not assigned: locked by another user"
    }


### Mark as not-a-reply (detach message from thread)
	
#### Method & address

POST to `/messages/mark_as_not_a_reply/id`

#### Parameters

None.

#### Operation

Detaches the message with id=`id` from its parent message (if any). If it is
currently shown as a reply to a message, after making this call it will no
longer be a reply. Instead it will be a standalone "new" message.

Note that any replies to *this* message (and indeed all subsequent reply
threads, that is, replies to those replies, and so on) will remain attached to
this message. All this call does is detach the message from its parent.

If the message is not currently marked as a reply, the call will fail.

This call is exposed as an API call because it's likely that the Message
Manager's autodetection of replies will sometimes be wrong (most commonly if a
user who is in one reply thread then submits a new message). It's currently
not possible to reassign a parent (that is, to attach a message to a
*different* parent message) through the API: you'll have to log into Message
Manager directly to manipulate messages (using **Edit message**).

Note that you almost certainly only want to do this to *incoming* messages,
because Message Manager doesn't expect message threads to begin with an
outgoing message.

#### Return value

The `mark_as_not_a_reply` call returns the following 

*   **success**: 
    which is `true` or `false`

*   **data**: 
    currently, no data is returned.

*   **error** (only on failure):
    if the message could not be detached, a message describing the problem is
    returned.


#### Examples

If the message was detached, `success==true`:

    {
      "success":     true,
      "data":        null
    }

If the message was not detached because it does not currently have a parent
message:

    {
      "success":     false,
      "data":        "No action taken: message wasn't marked as a reply anyway"
    }


