# mySociety's Message Manager

## In a nutshell

The Message Manager sits between a message source (such as an SMS gateway) and
a [FixMyStreet-like application][1]. It accepts incoming messages, and makes
them available to nominated users on the FMS system.

FixMyStreet is mySociety's award-winning problem-reporting software -- it runs
the UK's most-used problem reporting site at [www.fixmystreet.com][2] but the
platform is available as Open Source to power similar projects worldwide.

[1]: http://code.fixmystreet.com/  "the FixMyStreet platform"
[2]: http://www.fixmystreet.com/  "FixMyStreet.com running in the UK"

## Don't panic

There's a collection of most-likely problems and fixes in documentation/TROUBLESHOOTING.md

## Required technology

The Message Manager is a PHP application in the Cake framework. It needs to
run under a webserver (Apache is ideal) and connect to a database (such as
mySql or Postgres).

If you're using the Netcast SMS gateway (as mySociety does for the FixMyStreet
project), you'll need the PHP `nusoap` library.

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

There's an `.htaccess` file in `app/webroot` which is ready-to-use, but if
you're *not* running under under CGI/fastCGI, you may want to comment out the
Authorization rewrite rule.

You should be able see a Cake diagnostics page at
`http://your_domain/pages/cake_diagnostics`


## Setting up your database

Database config is in `app/Config/database.php`.

You can edit this file directly if you want, or you can drop your config in to
`app/Config/general.yml` as a YAML file. (This particular mechanism is in
place because mySociety's own environment favours YAML configuration files --
see the notes below about `general.yml`, which you may prefer because it means
all your config is in a single file, outside of the git repository).


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

Don't delete any of the existing user groups, or make any new ones. Make as
many new users as you like, but not new user groups!

The rest of this little section only affects you if you're a developer
modifying the code, so skip it if that's not you.

If you do delete or create user groups, you will almost certainly run into
problems until you rebuild the aros_acos table. See `UsersController/initdb`
(in the code: it's disabled/commented) for details for rebuilding it but,
really, don't change the groups :-)

Similarly, if you add any new actions to existing controllers, you'll need to
add them to the `acos` table, and then rebuild the `aros_acos` table to have
them included in Message Manager's authorisation policy. Remember, this only
affects you if you are a developer adding new code. The thing to bear in mind
is that AROS (requestor objects) are user groups, which you'll probably not
need to ever change, and ACOS (the actions being requested) are your new
actions, which will need to be both added and mapped to the AROS who are
allowed to use them. See the comments at the top of `db/initial_auth.sql` for
instructions how to rebuild the authorisation tables.

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
to the phone numbers of incoming messages.


## Configuring the Message Manager's system-wide settings

There are some basic settings that you need to edit. If you're running a local
installation and are happy to edit `/app/Config/MessageManager.php` then you
can add config directly there (and, as mentioned above,
`/app/Config/Database.php`).

However, Message Manager will also look inside `app/Config/general.yml` for
overriding configuration settings. This mechanism is in place because
mySociety's internal deployment mechanism uses YAML config files. If you don't
want to use this mechanism, set

    might_use_general_yml = 0

to prevent Message Manager from attempting to read the file at all.

*Note that (if `might_use_general_yml` is set to `1`), any settings in
`general.yml` will override the values in `/MessageManager.php` and
`Database.php`. That is: if you're using the `general.yml` file, its values
really are used!*

### Note about prefixes in general.yml

If you put config settings into `general.yml`, they'll be read as you'd
expect. However there's also a little bit of magic going on that mySociety's
internal mechanism uses: the prefix `MESSAGE_MANAGER_` will be stripped from
any config names, and furthermore those with `MESSAGE_MANAGER_DB_` or `db_`
prefixes are mapped explicitly to Cake's database config settings. There's a
even more magic mapping between mySociety's internal defaults and Cake's, but
if you use the values Cake expects, you'll be fine. For example, to set your
database settings from within `general.yml` use this:

    db_datasource: 'Database/Mysql'
    db_persistent: false
    db_host: 'localhost'
    db_login: 'user'
    db_password: 'password'
    db_database: 'your_database_name'

Remember that these are just overriding the values in Cake's default and
database php files anyway, so if you want to be terse you only need to declare
values that are different.


### config setting: `tags`

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

### config setting: `remove_tags_when_matched`

Set to a true value (`1`) if you want matched tags to be removed from incoming
messages (recommended).

### config settings: `fms_site_url` and `fms_report_path`

Provide the base URL of your FMS-like site and the path to reports. 

    'fms_site_url'    => 'http://www.fixmystreet.com',
    'fms_report_path' => '/report/%s',

It's probably best to not have a trailing slash on the site URL, and start the
path with one. The `%s` will be replaced with the problem report's ID.

### config setting: `enable_dummy_client`

Set this to `0` to disable the dummy client before going live. You can see the
dummy client at `/dummy` and you can use it to fake incoming messages and
client interaction with the database... but you *must* disable it before
running a production system.

### config setting: `lock_expiry_seconds`

The lock expiry controls how long a message "belongs" to the user who last
claimed it (locking is the mechanism used to stop two FMS users creating a
report from the same message at the same time). You can normally leave this at
the default value of 360 seconds.

### config setting: `cors_allowed`

The CORS allowed is a comma-separated list of URLs that are used to indicate
to the (browser) clients which domains the Message Manager regards as
trustworthy when receiving incoming AJAX requests from domains other than its
own. This is part of the mechanism used if you're making Message Manager calls
from within another website's pages (such as FixMyStreet).

### config setting: `autodetect_reply_period`

Message Manager tries to detect when an incoming message might actually be a reply 
(rather than a new report) by seeing if there were any outbound messages
sent to this number within a certain period. By default this is `1 week` but
if you find that's not long enough you can change it here (e.g., `2 days`, 
`2 weeks`, `1 year`).

## Smoke-test: log in as `admin`

Once you've set up your config, you should be able to log into the Message
Manager with the default admin user.

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
Similarly, you can reply to and hide messages (replies won't get sent unless
you've actually enabled/configured the SMS sending, so it's probably safe to
play). This demo implements the JSON API with `message_manager_client.js`,
which is intended to serve as a working example if you're attempting an
implementation of your own. See the code in
`app/View/MessageSources/client.ctp` to see how this is used (in particular
the javascript at the bottom of that file which is applying the methods from
`message_manager_client.js`). You should be able to drop the `.js` file into
your own client without needing to modify it.

If you're logged in as an administrator (or a message-source) you'll also see
a form for submitting incoming messages "as if" they were coming in from an
SMS gateway.

By default this page is enabled but you _must_ disable it before going live:
see the section on configuration above (`enable_dummy_client`).

## Setting up FixMyStreet

In order to communicate with FixMyStreet (or a similar application) you need
to enable the Message Manager functionality for specified accounts in that
application (note: June-2012 *not yet implemented*).

If you are implementing your own code, see the API documentation (log into
Message Manager and go to `/api`) and also have a look at
`message_manager_client.js`, which implements the concept by way of a
demonstration -- see "Dummy client" above.

# Running Message Manager

Once installation is complete, you're ready to go!

These are the things you'll probably need to do. This is a very rough list and
includes some of the configuration described above.

   * configure the tags you need (if you're using tags) to auto-allocate
     incoming messages to the right people

   * create at least one message source

   * create users

      * managers for the team members who can have access to the message data
        (including users' phone numbers)

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

mySociety's Message Manager was created as an addition to the FixMyStreet
platform as part of a project funded by the World Bank. See
code.fixmystreet.com for more information about the platform.


## Timeline

Note that this is, as of June 2012, a work in progress! See the 
[github-repo][3] for code and outstanding issues.

[3]: http://github.com/mysociety/message-manager

## Licensing

mySociety's Message Manager is released under the GNU Affero General Public
License. See the accompanying LICENSE file.


## About Cake

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




