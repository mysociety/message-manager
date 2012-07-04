# Troubleshooting

## Can't access any pages?

Message Manager uses ACO/ARO tables to decide who can look at what. Make sure you've populated the
database with the contents of `db/initial_auth.sql` as well as `intial_mm_data.sql`

## Caching issues

If you're having problems with Cake's APC cache, you can disable it and use the file cacheing instead.
Note that you'll need to ensure everything within
`app/tmp/cache/`
is writeable by your webserver process.


### Work-in-Progress

(TODO! add more helpful solutions to common gotchas and hmmms)