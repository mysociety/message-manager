# mySociety's Message Manager

## In a nutshell

The Message Manager sits between a message source (such as an SMS gateway) and
a [FixMyStreet-like application][1]. It accepts incoming messages, and makes
them available to nominated users on the FMS system.

FixMyStreet is mySociety's award-winning problem-reporting software -- it runs
the UK's most-used problem reporting site at [www.fixmystreet.com][2] but the
platform is available as Open Source to power similar projects worldwide.

[1]: http://code.fixmystreet.com/  "the FixMyStreet platform"
[2]: http://code.fixmystreet.com/  "FixMyStreet.com running in the UK"


## Required technology

The Message Manager is a PHP application in the Cake framework. It needs to
run under a webserver (Apache is ideal) and connect to a database (such as
mySql or Postgres).

The example implementation of the JSON API (see Dummy Client, below) uses
jQuery.

# Installation

Installation should be easy. If it isn't, please let us know so we can add any
gotchas you experience to this document. Contact details are at the bottom of
this file.

* install the files
* point your webserver at `app/webroot`
* create a database
* add database settings to `app/Config/Database.php`
* configure system-wide settings in Message Manager
* log in as `admin`
* add Message Sources
* add users
* configure the FMS 

The rest of this document goes into more detail about this process.

## Install files and point webserver at `app/webroot`

Specifically, if you don't change the structure of the delivered Message
Manager files at all, you should be able to set your webserver document root
to `app/webroot` and things will work. If you do change the structure, you'll
need to update `app/webroot/index.php` to find the Cake system installation.

You should be able see a Cake diagnostics page at
`http://your_domain/pages/diagnostics`


## Setting up your database

Database config is in `app/Config/database.php`.

You can edit this file directly if you want, or you can drop your config in to
`app/Config/general.yml` as a YAML file. (This particular mechanism is in
place because mySociety's own environment favours YAML configuration files).


### Populate it with initial data (not optional!)

Populate your database using the SQL in `db/`. There are three files:

* __`schema.sql`__ -- The structure of tables: install this first.

* __`initial_mm_data.sql`__ -- This includes some dummy data (e.g., a sample
  message and message source) which you can delete --but it also has some
  non-optional data (such as user groups, action types, and statuses) that
  _must_ be present for Message Manager to be able to run.

* __`initial_auth.sql`__ -- The ACO/ARO data is required for the authorisation
  within Message Manager to work -- it sets up the mappings between actions
  and user groups. You _must_ have this data in your database.

The files are for postgreSQL. See `db/mysql/` for equivalent files for mySql.
Remember you'll need to change the `datasource` setting (see above) to
`Database/mySql`.

### Warning about user groups

Don't delete any of the existing user groups, or make any new ones.

If you do, you will almost certainly run into problems until you rebuild the
aros_acos table. See `UsersController/initdb` (in the code: it's
disabled/commented) for details for rebuilding it but, really, don't change
the groups :-)

Similarly, if you do add any actions to existing controllers, you'll need to
rebuild the `aros_acos` table to have them included in Message Manager's
authorisation policy. This only affects you if you are a developer adding new
code. See the comments at the top of `db/initial_auth.sql` for instructions
how to rebuild the authorisation tables.

The authorisation within Message Manager is handled on the group level (which
is why although you shouldn't delete the groups, deleting/adding users won't
be a problem -- so long as you always put them into an appropriate group).


## Default users

There are four example users in the data (one in each of the groups). 

To understand the different groups, see the help page at `/help` (or
app/View/Pages/help.ctp).

    Username  Group             Password   Access
    --------+-----------------+----------+----------------------------------
    admin     administrators    qqqqqqqq   full access
    manager   managers          qqqqqqqq   normal access
    user      api-users         qqqqqqqq   JSON API access only
    source    message-sources   qqqqqqqq   submitting incoming messages only

Log in as admin to use the Message Manager fully. The `user` and `source`
users are not suitable for logging into the Message Manager website, but need
username/password credentials to access the API and submit messages
respectively.

For obvious reasons you *must* either remove the default users or change their
passwords once you have installed your database, before going live.

If you do delete users, remember to keep at least one admin user in the
database, otherwise you won't be able to log in and create more users.

Note that users in the `administrators` and `managers` groups *do* have access
to the MSISDNs (phone numbers) of incoming messages.


## Configuring the Message Manager's system-wide settings

There are some basic settings that you need to edit in
`/app/Config/MessageManager.php`

Currently these values will not be read from `general.yml`, so be careful
if you are developing the code not to commit these config changes.

### `tags`

The tags are words that are used to identify and filter incoming messages.
Currently the Message Manager checks for a tag at the start of incoming
messages, marks the incoming message as such and optionally removes the tag
from the message text.

Message Manager works fine without tags: just make the array empty if you
don't want to use them.

Use tags that the message-senders will be comfortable using (keep them short
and meaningful!) and associate it with the full meaning. For example:

    'tags' => array(
        'LUZ' => "Barangay Luz",
        'BSN' => "Barangay Basak San Nicolas"
    )

Tag recognition is case-insensitive.

### `remove_tags_when_matched`

Set to a true value (`1`) if you want matched tags to be removed from incoming
messages (recommended).

### FMS URLs

Provide the base URL of your FMS-like site and the path to reports. 

    'fms_site_url'    => 'http://www.fixmystreet.com',
    'fms_report_path' => '/report/%s',

It's probably best to not have a trailing slash on the site URL, and start the
path with one. The `%s` will be replaced with the problem report's ID.

### Other settings

Other config settings are `enable_dummy_client` which you should set to a
false value before going live, and `lock_expiry_seconds` which can normally be
left at the default value of 360 seconds.

## Smoke-test: log in as `admin`

You should be able to log into the Message Manager with the default admin
user.

## Add message sources

Incoming messages come from message sources (such as an SMS gateway). The
default set up includes an example one, but if you are setting up a live
system you will either want to delete that one and add a new one, or else edit
it to fit your purposes. Similarly, you'll need to either change the password
of the `source` user, or make a new one (in the group `message-sources`) which
the source can use when submitting new messages.

You'll need at least one message source -- without one you can't accept any
messages.

## Add users

You can set up new users too -- and delete the example users before you go live.

Normal FMS account holders who will be using the Message Manager facility of
FMS (normally this means, for example, council employees) will probably only
need to be in `api-users` group.

If you're using tags, you can specify which tags the user is associated with.
API users cannot retreive messages that don't have a tag which matches the
user's tag.

## Dummy client & Incoming Message

The Message Manager has a dummy client (that is, a page for testing/playing
without needing to really integrate with an external service, that effectively
behaves as a page on FixMyStreet would). You can find it by logging into the
Message Manager and going to `/dummy`. Click "available messages" (at any
time) to load the list of messages: clicking on them locks them, and clicking
on "Assign FMS ID" will assign it -- if this operation succeeds, the message
is removed from the list because it is no longer available for assignment.
This demo implements the JSON API with `message_manager_client.js`, which is
intended to serve as a working example if you're attempting an implementation
of your own. See the code in `app/View/MessageSources/client.ctp` to see how
this is used (in particular the javascript at the bottom of that file which is
applying the methods from `message_manager_client.js`). You should be able to
drop the `.js` file into your own client without needing to modify it.

If you're logged in as an administrator (or a message-source) you'll also see
a form for submitting incoming messages "as if" they were coming in from an
SMS gateway.

By default this page is enabled but you _must_ disable it before going live:
see `/app/Config/MessageManager.php`.

## Setting up FixMyStreet

In order to communicate with FixMyStreet (or a similar application) you need
to enable the Message Manager functionality for specified accounts in that
application (note: June-2012 *not yet implemented*).

If you are implementing your own code, see the API documentation (log into
Message Manager and go to `/api`) and also have a look at `client.js`, which
implements the concept by way of a demonstration -- see "Dummy client" above.

# Running Message Manager

Once installation is complete, you're ready to go!

These are the things you'll probably need to do. This is a very rough list and
includes some of the configuration described above.

   * configure the tags you need (if you're using tags) to auto-allocate
     incoming messages to the right people

   * create at least one message source

   * create users

      * managers for the team members who can have access to the message data
        (including MSISDNs)

      * api-users for FMS users who simply want to be able to allocate
        messages to the FMS reports they are creating. Remember to add the
        tags of the messages you want them to be able to see

      * you'll need at least one message-source user that is mapped to the
        message source, so that it can send incoming messages

   * configure that message source (be it SMS gateway or twitter feed) to use
     the /incoming URL to submit messages

   * configure FixMyStreet so it has the URL of your Message Manager (to be
     implemented!)


# About the project

mySociety's Message Manager was created as an addition to the FixMyStreet platform as part of a
project funded by the World Bank. See code.fixmystreet.com for more information about the 
platform. 


## Timeline

Note that this is, as of June 2012, a work in progress! See the 
[github-repo][3] for code and outstanding issues.

[3]: http://github.com/mysociety/message-manager

## Licensing

mySociety's Message Manager is released under the GNU Affero General Public
License. See the accompanying LICENSE file.


##About Cake

[CakePHP][4] is a rapid development framework for PHP which uses commonly
known design patterns like Active Record, Association Data Mapping, Front
Controller and MVC. Our primary goal is to provide a structured framework that
enables PHP users at all levels to rapidly develop robust web applications,
without any loss to flexibility.

The Cake Software Foundation - promoting development related to CakePHP.

Licensed under the MIT license.

[4]: http://cakefoundation.org/


## About mySociety

[mySociety][5] is an e-democracy project of the UK-based registered charity
named UK Citizens Online Democracy. Our mission is to help people become more
powerful in the civic and democratic parts of their lives, through digital
means.

Email us at hello@mysociety.org or talk to us on [our irc server][6].

[5]: http://www.mysociety.org/contact/ "mySociety contact information"
[6]: http://www.irc.mysociety.org/ "irc channel #mysociety"




