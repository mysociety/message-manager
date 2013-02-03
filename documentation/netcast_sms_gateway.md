# How to connect to the SMS gateway

Currently we connect only to the Netcast gateway, but this of course serves as
a likely model for other connections.

As admin, create a *source* (since the SMS gateway is a message source) and
add the API key.

Message Manager anticipates new messages being received as incoming POST
requests (this is how the message-sources user group is set up) -- however
this isn't how the Netcast gateway works, so instead we poll the gateway for
new messages.

This means we're ignoring the "IP addresses" bit in the source data (since we
don't need to check for any incoming requests), and using a custom script to
make the various calls (both receiving and sending).

## See what gateways there are

Go into the application folder and run the `netcast` shell:

    cd app
    Console/cake netcast gateway_list

This will show you all the gateways your installation currently knows about.
You need to use the name or the id (e.g., '1') for other gateway commands.

## Test the connection

Run a connection test: this simply attempts to connect to the gateway, and
*does nothing else* -- this means you can use it to test that the API key
works and the server is not rejecting you (for example, Netcast have enabled
access only from our specific severs' IP addresses).

    Console/cake netcast gateway_test netcast-gateway

## Other functions

Run 

    Console/cake netcast

to see the other things you can do (usage details available with `-h` or `--help`).

Be a little bit careful. These generally poll or send messages so do affect
the real, live data in your database, but are generally safe to do since
duplicates are safely handled (after all, these are the tasks that should be
running as cron-jobs anyway).

## Behaviour of the Netcast API: caution!

We've noticed the `GETSMART` and `GETINCOMING` API calls behave differently
(this is done by running `netcast get_incoming` with different values for the
`--command` option). `GETSMART` deletes the incoming messages from the server
when they are pulled in this way, and `GETINCOMING` does not.




