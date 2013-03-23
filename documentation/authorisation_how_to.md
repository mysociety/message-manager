# About authorisation within Message Manager

This information only applies if you're a developer adding new actions to the Message Manager.

If you're just having problems accessing things in MM, it might be that you've
not got the most recent auth data in the database: see TROUBLESHOOTING.md
which (amongst other things) suggests you check that.

MM uses Cake's ACO/ARO mechanism for determining which users (in fact, which
user *groups* are allowed to perform which actions). This works fine when
you're running normally, but if you add a new action, or need to change the
authorisation of the existing groups, you'll need to rebuild the ARO/ACO
tables to reflect the latest changes. This can be a wee bit fiddly.

## Adding a new user

If you add a new user *within the Message Manager admin*... everything should
be OK (of course -- since this is an admin task that users in the `admin`
group can do anyway).

## Adding a new user group

If you create a new group *within the Message Manager admin* the ARO table
will automatically add it (although this won't automatically have any auth
access -- you'll need to update the mappings in in `apps/Controllers/User.php`
at the `initDB` action as described below).

> **IMPORTANT** this presupposes that you're running with the user
> groups in the `initial_mm_data.sql` file -- specifically this means
> user groups running with the same primary keys as in that file.

## Adding a new action 

> Note: this presumably also applies if you're adding a new Controller

If you add a new action to a controller, you'll need to add it to the ACOS
table. Then decide who can use this action by adding it to the appropriate
user groups -- see how this is done with the list of existing actions in
`apps/Controllers/User.php` at the `initDB` action.

Then the safest approach is probably to rebuilt most of it like this:

1. First, delete all records from `aco` and `aros_acos`.

    (You don't need to change the `aro` table because the users and groups
    haven't changed from having just added an action, so that table can remain
    unchanged).

2. Rebuild the aco table with: `Console/cake AclExtras.AclExtras aco_sync`

3. Repopulate the ARO-ACO mappings (i.e., *who* can do *what*):

   * First, you'll need to edit the PHP to edit the line in
     `app/Controller/UsersController.php` that says 
     `$ENABLE_INIT_DB = false` which prevents this usually running)

   * Then, in the browser hit `/users/initdb` (if it works, you'll see a
     plain text "all done" response and nothing else)

   * When that's done, remember to set `$ENABLE_INIT_DB` back to `false`
     since for now that's the simplest way to stop anyone running this when
     they don't mean to.

4. **important!** Remember to dump the data from the ACO and AROS_ACOS tables
    and update `db/intial_auth.sql` so it contains the latest values.
    To help you, the dump command will be something like this:
    
```bash
pg_dump --inserts --data-only --table=aros_acos --table=acos -U username databasename > tmp/mm-auth.sql
```

That's it. In future we might move the `/users/initdb` call into a
command-line shell, which is where it should be!

