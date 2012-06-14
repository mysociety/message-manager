SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE acos (
  id int(10) NOT NULL AUTO_INCREMENT,
  parent_id int(10) DEFAULT NULL,
  model varchar(255) DEFAULT NULL,
  foreign_key int(10) DEFAULT NULL,
  alias varchar(255) DEFAULT NULL,
  lft int(10) DEFAULT NULL,
  rght int(10) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=211 ;

CREATE TABLE actions (
  id int(11) NOT NULL AUTO_INCREMENT,
  created datetime DEFAULT NULL,
  type_id int(11) NOT NULL DEFAULT '0',
  user_id int(11) DEFAULT NULL,
  message_id int(11) NOT NULL,
  item_id int(11) DEFAULT NULL,
  note text,
  PRIMARY KEY (id),
  KEY created_at (created,user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=602 ;

CREATE TABLE action_types (
  id int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  description text,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE aros (
  id int(10) NOT NULL AUTO_INCREMENT,
  parent_id int(10) DEFAULT NULL,
  model varchar(255) DEFAULT NULL,
  foreign_key int(10) DEFAULT NULL,
  alias varchar(255) DEFAULT NULL,
  lft int(10) DEFAULT NULL,
  rght int(10) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE aros_acos (
  id int(10) NOT NULL AUTO_INCREMENT,
  aro_id int(10) NOT NULL,
  aco_id int(10) NOT NULL,
  _create varchar(2) NOT NULL DEFAULT '0',
  _read varchar(2) NOT NULL DEFAULT '0',
  _update varchar(2) NOT NULL DEFAULT '0',
  _delete varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY ARO_ACO_KEY (aro_id,aco_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=166 ;

CREATE TABLE groups (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

CREATE TABLE messages (
  id int(11) NOT NULL AUTO_INCREMENT,
  source_id int(11) DEFAULT NULL,
  external_id varchar(32) DEFAULT NULL,
  msisdn varchar(24) DEFAULT NULL,
  sender_token varchar(255) DEFAULT NULL,
  message text,
  created datetime DEFAULT NULL,
  received datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  replied datetime DEFAULT NULL,
  assigned datetime DEFAULT NULL,
  lock_expires datetime DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  owner_id int(11) DEFAULT NULL,
  session_key varchar(255) DEFAULT NULL,
  fms_id int(11) DEFAULT NULL,
  tag varchar(64) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY external_id (external_id,msisdn,`status`,owner_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2023 ;

CREATE TABLE message_sources (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  description text,
  url varchar(255) DEFAULT NULL,
  ip_addresses text,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE statuses (
  id int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  description text,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  `password` char(40) NOT NULL,
  group_id int(11) NOT NULL,
  allowed_tags varchar(255) DEFAULT NULL,
  can_reply int(1) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;
