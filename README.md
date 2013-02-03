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

## Installation

See `documentation/installation.md` for installation and set-up instructions.

## About the project

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




