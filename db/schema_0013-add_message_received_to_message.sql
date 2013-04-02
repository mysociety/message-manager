ALTER TABLE  messages ADD  message_received TEXT NULL DEFAULT NULL;

-- not quite correct, but probably helpful: assume legacy messages have not been edited (although tags may have been stripped)
UPDATE messages SET message_received = message;
