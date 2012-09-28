# Troubleshooting

## Can't access any pages?

Message Manager uses ACO/ARO tables to decide who can look at what. Make sure you've populated the
database with the contents of `db/initial_auth.sql` as well as `intial_mm_data.sql`

## Caching issues

If you're having problems with Cake's APC cache, you can disable it and use the file cacheing instead.
Note that you'll need to ensure everything within
`app/tmp/cache/`
is writeable by your webserver process.

## Can't access Message Manager from within FixMyStreet?

We're using [CORS](http://enable-cors.org/), with its "pre-flight checks", 
to make this work in our installation. Note thought that we've experienced
some behaviour in the Safari browser that reports errors coming back from
AJAX calls from Message Manager as problems with this, when in fact they are
not. So if you see error messages that say, "Origin http://xxx is not allowed
by Access-Control-Allow-Origin." do check the return error code because that
might be the real error, and the Access-Control-Allow-Origin message is a
trick.

### Work-in-Progress

(TODO! add more helpful solutions to common gotchas and hmmms)