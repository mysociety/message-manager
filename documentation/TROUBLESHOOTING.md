# Troubleshooting

## Can't access any pages?

Message Manager uses ACO/ARO tables to decide who can look at what. Make sure
you've populated the database with the contents of `db/initial_auth.sql` as
well as `intial_mm_data.sql`

## Caching issues

If you're having problems with Cake's APC cache, you can disable it and use
the file cacheing instead. Note that you'll need to ensure everything within
`app/tmp/cache/` is writeable by your webserver process.

## Can't access Message Manager from within FixMyStreet?

We're using [CORS](http://enable-cors.org/), with its "pre-flight checks", to
make this work in our installation. So if you see error messages that say,
"Origin http://xxx is not allowed by Access-Control-Allow-Origin." do check
the domain (and, importantly, the sub-domain(s)) from which you're making the
AJAX call does match what's been configured in the CORS setup __exactly__. In
particular, check those subdomains. CORS is (correctly) unforgiving if you are
not precise.

## AJAX/CORS failing because of a 302 redirect

If there's anything wrong with the ACO/ARO tables then you can get a "302:
Found" response from Cake, which might not be what you're expecting. The
`Location` returned *is* the referrer, which doesn't help. So if you get this
behaviour, delete everything from the `aros`, `acos` and `aros_acos` tables
and re-populate them with the contents of `db/initial_auth.sql`.

## Reply message (SMS) threads not working? Try rebuilding the tree.

The threads (i.e., which message is a reply to which message) is implemented
using Cake's TreeBehaviour. This uses the `parent_id`, `lft`, and `rght`
columns in the database, which take care of themselves provided all access is
done through ActiveRecord. This means it can go wrong if, for example, you
change something directly within the database. Fortunately, Cake knows how to
rebuild the structure (just by inference using `parent_id`), so Message
Manager exposes this function for you in case you need it. You can manually
(and non-destructively; it's safe!) trigger this recovery simply by hitting
`/messages/?recover_tree=1`

There's currently a link to this at the bottom of the Message Manager home
page (if you're logged in).

## Warning about 'available' message status 

Be aware that FixMyStreet FMS only lets users create problem reports from
incoming messages which have `available` status provided they are __not
replies__. This is why you'll see more `available` statuses in Message Manager
than FMS IDs, or, to put it another way, it's why the `/messages/available`
call might show *no messages* even though an SQL count on `status='available'`
returns more than 0. In future, maybe replies should not be given status
`available`... but for now don't get tricked by this.

A reply is a message which has a non-empty `parent_id` and FixMyStreet
currently won't let you make a new report from such a message.

## Help/more information

There are Help pages within the application -- once you've installed it, click
on *help* (the source text is in `app/Views/Pages`).

### Work-in-Progress

(TODO! add more helpful solutions to common gotchas and hmmms)