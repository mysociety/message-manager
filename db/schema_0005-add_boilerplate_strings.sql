create table boilerplate_strings (
  id serial not null primary key,
  lang varchar(16) default null,
  type varchar(16) default null,
  text_value text not null,
  sort_index integer not null default 0
);

create index boilerplate_strings_lang_idx on boilerplate_strings(lang);
create index boilerplate_strings_type_idx on boilerplate_strings(type);
