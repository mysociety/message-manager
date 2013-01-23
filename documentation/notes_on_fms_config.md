# Checklist for configuring Message Manager to run with FMS

The following notes describe what needs to be set up in order to run a version of FixMyStreet that uses the Message Manager. At the time of writing, this specifically means the FixMyBarangay project. Hopefully the information will be useful for future integrations.

## Deploy the MM on a webserver

Installing Message Manager is described in the README.md document. You'll need to put it on a webserver (there's a very strong case for it running over https), and you'll need it to connect to a database (we use postgres).

The Message Manager installation includes a couple of SQL files for populating the database with initial data (see the files in the `db` directory). Some of the steps below assume you've done this. Note that initially this means your Message Manager is accessible with default passwords, so you should follow the installation instructions in the README for changing the passwords.

## If running MM over https, set env(HTTP)

CakePHP rewrites the login URL and it likes to default to `http:` when it does so.

One way to prevent this is to make sure the environment variable `HTTPS` is set to some true value, because Cake uses this
to determine the protocol. See `app/Congif/httpd.conf-example` for an example of setting this in your Apache configuration file.

## Check for dependencies: nusoap

In addition to the webserver, PHP, and database, you also need `nusaop` (Web Services Toolkit for PHP), which you can get from http://sourceforge.net/projects/nusoap/

The connection to the Netcast gateway (that's the one that the FixMyBarangay project uses) needs `nusoap` to make its calls. If your system doesn't already have it on the path, you can drop a local copy into the `message-manager/lib` directory. Note for Debian users: there's a `libnusoap-php` package.

## Deploy FMS on a webserver and make sure it's got MM stuff in it

Deploy a FixMyStreet site -- this is described in detail at code.fixmystreet.com

The key thing for the initial deployment is to make sure you've configured it to use a cobrand which uses the Message Manager. There are lots of other things, of course, but they are not specific to Message Manager.

Currently the `fixmybarangay` cobrand is the only one that uses Message Manager.

To set the cobrand, add something like this to `conf/general`

ALLOWED_COBRANDS:
  - fixmybarangay: subdomain-name
  - fixmybarangay

Specifically, fixmybarangay uses Message Manager in the "report a problem" section; this is probably what you'd expect since the messages are used to create new reports. The cobrand includes the Message Manager like this:

   * the templates (around report creation) explicitly include the HTML and the JavaScript for Message Manager
   * the cobrand contains the external resources such as CSS, images (e.g., for the message-manager spinner), and js files that the template and javascript in the templates uses

You can see this at work by looking at the `message_manager_*` files amongst the cobrand's web assets in 
`web/cobrands/fixmybarangay`, but also see`templates/web/fixmybarangay/report/_message_manager.html` to this being applied.

## Add the FMS site to MM config

Add values for the following to the config: we generally recommend putting values in `app/Config/general.yml`. because that
keeps you config settings out of the source code repo.

FMS_SITE_URL:
add the URL of the client (no trailing slash)

FMS_REPORT_PATH: '/report/%s'
this is the path to specific reports -- %s is the placeholder for the report's ID

CORS_ALLOWED:
Provide the one and only (currently) URL that is acceptable for making the AJAX connection with this message manager.
Specifically, this is the URL from which requests from the Message Manager *within* the FMS page will be accepted.
You must be precise, and if you're running your FMS on https be sure to specify the protocol correctly too.



