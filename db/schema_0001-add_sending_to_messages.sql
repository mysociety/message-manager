
ALTER TABLE messages
  ADD COLUMN is_outbound boolean not null default '0';

ALTER TABLE messages
  ADD COLUMN parent_id integer;

ALTER TABLE messages
 RENAME COLUMN msisdn to from_address;

ALTER TABLE messages
  ADD COLUMN to_address varchar(128);

INSERT INTO statuses 
  (id, name, description) VALUES (4, 'pending', 'outbound message waiting to be sent');
INSERT INTO statuses 
  (id, name, description) VALUES (5, 'sent', 'outbound message has been sent');
INSERT INTO statuses 
  (id, name, description) VALUES (6, 'error', 'message send failed');

ALTER TABLE users
  ADD COLUMN can_reply boolean not null default '0';
