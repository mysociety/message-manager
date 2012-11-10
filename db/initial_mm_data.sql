
INSERT INTO action_types VALUES(0, 'unknown',  'unknown');
INSERT INTO action_types VALUES(1, 'lock',     'claimed lock on this message');
INSERT INTO action_types VALUES(2, 'unlock',   'lock relinquished on this message');
INSERT INTO action_types VALUES(3, 'assign',   'assigned message to a report');
INSERT INTO action_types VALUES(4, 'unassign', 'message was unassigned from a report');
INSERT INTO action_types VALUES(5, 'hide',     'hid message');
INSERT INTO action_types VALUES(6, 'unhide',   'revealed message by unhiding it');
INSERT INTO action_types VALUES(7, 'note',     'note');
INSERT INTO action_types VALUES(8, 'reply',    'replied to message');
INSERT INTO action_types VALUES(9, 'gateway',    'activity with the SMS gateway');

INSERT INTO statuses VALUES(0, 'unknown',      'unknown');
INSERT INTO statuses VALUES(1, 'available',    'available for activity');
INSERT INTO statuses VALUES(2, 'assigned',     'has been assigned to a FMS report');
INSERT INTO statuses VALUES(3, 'hidden',       'has been hidden (deleted)');
INSERT INTO statuses VALUES(4, 'pending',      'outbound message waiting to be sent');
INSERT INTO statuses VALUES(5, 'sent',         'outbound message has been sent');
INSERT INTO statuses VALUES(6, 'error',        'message send failed');
INSERT INTO statuses VALUES(7, 'sent_pending', 'delivered to SMS gateway, pending');
INSERT INTO statuses VALUES(8, 'sent_ok',      'delivered to SMS gateway, sent OK');
INSERT INTO statuses VALUES(9, 'sent_fail',    'delivered to SMS gateway, send failed');

INSERT INTO groups VALUES(1, 'administrators', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO groups VALUES(2, 'managers', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO groups VALUES(3, 'api-users', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO groups VALUES(4, 'message-sources', '2012-05-25 00:00:00', '2012-05-25 00:00:00');

SELECT pg_catalog.setval(pg_get_serial_sequence('groups', 'id'), (SELECT MAX(id) FROM groups)+1);

INSERT INTO users VALUES(1, 'admin', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 1, NULL, '0', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO users VALUES(2, 'manager', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 2, NULL, '0', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO users VALUES(3, 'user', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 3, NULL, '0', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO users VALUES(4, 'source', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 4, NULL, '0', '2012-05-25 00:00:00', '2012-05-25 00:00:00');

SELECT pg_catalog.setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users)+1);

INSERT INTO messages VALUES(1, NULL, 1, '55512345678', NULL, '41fdc1d415ab820375a905629d1c1442', 'Welcome to Message Manager', '2012-05-25 01:02:00', '2012-05-25 01:02:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '0');

SELECT pg_catalog.setval(pg_get_serial_sequence('messages', 'id'), (SELECT MAX(id) FROM messages)+1);

INSERT INTO message_sources VALUES(1, 'mock-gateway', 'This is a mock message source which could be an SMS gateway, configured to hit /messages/incoming to enter new messages. It is associated with a user in the \"message-sources\" user group, which it will use to authenticate that call.', 'www.example.com', '12.12.12.12', '4', '2012-06-01 00:00:00', '2012-06-01 00:00:00');

SELECT pg_catalog.setval(pg_get_serial_sequence('message_sources', 'id'), (SELECT MAX(id) FROM message_sources)+1);
