
CREATE TABLE acos (
  id serial not null primary key,
  parent_id integer default null,
  model varchar(255) default null,
  foreign_key integer default null,
  alias varchar(255) default null,
  lft integer default null,
  rght integer default null
);

CREATE TABLE actions (
  id serial not null primary key,
  created timestamp default null,
  type_id integer not null default '0',
  user_id integer default null,
  message_id integer not null,
  item_id integer default null,
  note text
);
create index actions_idx on actions (created, user_id);

CREATE TABLE action_types (
  id integer not null primary key,
  name varchar(64) not null,
  description text
);

CREATE TABLE aros (
  id serial not null primary key,
  parent_id integer default null,
  model varchar(255) default null,
  foreign_key integer default null,
  alias varchar(255) default null,
  lft integer default null,
  rght integer default null
);

CREATE TABLE aros_acos (
  id serial not null primary key,
  aro_id integer not null,
  aco_id integer not null,
  _create varchar(2) not null default '0',
  _read varchar(2) not null default '0',
  _update varchar(2) not null default '0',
  _delete varchar(2) not null default '0'
);
create unique index aros_acos_unique_idx on aros_acos (aro_id,aco_id);

CREATE TABLE groups (
  id serial not null primary key,
  name varchar(100) not null,
  created timestamp default null,
  modified timestamp default null
);

CREATE TABLE messages (
  id serial not null primary key,
  source_id integer default null,
  external_id varchar(32) default null,
  from_address varchar(128) default null,
  to_address varchar(128) default null,
  sender_token varchar(255) default null,
  message text,
  created timestamp default null,
  received timestamp default null,
  modified timestamp default null,
  replied timestamp default null,
  assigned timestamp default null,
  lock_expires timestamp default null,
  status smallint not null default '0',
  owner_id integer default null,
  session_key varchar(255) default null,
  fms_id integer default null,
  tag varchar(64) default null,
  hide_reason text null default null,
  is_outbound boolean not null default '0',
  send_fail_count integer null default '0',
  send_fail_reason text null default null,
  send_failed_at timestamp null default null,
  lft integer,
  rght integer,
  parent_id integer
);
create index messages_external_id_idx on messages(external_id);
create index messages_from_address_idx on messages(from_address);
create index messages_to_address_idx on messages(to_address);
create index messages_status_idx on messages(status);
create index messages_owner_id_idx on messages(owner_id);
	
CREATE TABLE message_sources (
  id serial not null primary key,
  name varchar(64) not null,
  description text,
  url varchar(255) default null,
  ip_addresses text,
  user_id integer default null,
  created timestamp default null,
  modified timestamp default null
);

CREATE TABLE statuses (
  id integer not null primary key,
  name varchar(64) not null,
  description text
);

CREATE TABLE users (
  id serial not null primary key,
  username varchar(255) unique not null,
  password char(40) not null,
  group_id integer not null,
  allowed_tags varchar(255) default null,
  can_reply boolean not null default '0',
  created timestamp default null,
  modified timestamp default null,
  email varchar(132) DEFAULT null
);
create unique index users_username_idx on users(username);

create table boilerplate_strings (
  id serial not null primary key,
  lang varchar(16) default null,
  type varchar(16) default null,
  text_value text not null,
  sort_index integer not null default 0
);

create index boilerplate_strings_lang_idx on boilerplate_strings(lang);
create index boilerplate_strings_type_idx on boilerplate_strings(type);

