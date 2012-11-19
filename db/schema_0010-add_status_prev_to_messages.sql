alter table  messages add status_prev smallint not null default '0';

update messages set status_prev = status where status != 3;