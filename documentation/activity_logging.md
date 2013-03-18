# Logging activity withint Message Manager

Most activity in the Message Manager is logged in the `Activity` table in the
database. You can see activity by logging into the Message Manager with an
administrator's login and cicking on *Activity* on the main menu.

There is also a console shell, `ActivityShell`, which dumps activity records
out to STDOUT. Do:

    cd app
    Console/cake activity dump --help

for details. 

Example:
	
	Console/cake activity dump -v --type "hide unhide" --after-date '2012-12-31' -b '2013-02-01'
	
dumps all activity hiding or unhiding activity in January 2013.

Acticity records include message id and item id values. The message id refers
to the primary message to which this action relates (if any). The meaning of
the item id will vary depending on the action type (for example, for assign
actions, it's the FixMyStreet id to which the message was assigned).
