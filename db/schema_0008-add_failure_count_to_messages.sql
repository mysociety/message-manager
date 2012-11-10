ALTER TABLE  messages ADD  send_fail_count INTEGER NULL DEFAULT '0';
ALTER TABLE  messages ADD  send_fail_reason TEXT NULL DEFAULT NULL;
ALTER TABLE  messages ADD  send_failed_at TIMESTAMP NULL DEFAULT NULL;

INSERT INTO statuses VALUES(7, 'sent_pending', 'delivered to SMS gateway and pending');
INSERT INTO statuses VALUES(8, 'sent_ok',      'delivered to SMS gateway and sent OK');
INSERT INTO statuses VALUES(9, 'sent_fail',    'delivered to SMS gateway and send failed');
