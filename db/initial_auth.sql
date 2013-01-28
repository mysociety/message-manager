
-- Essential auth data for Message Manager
-- You must add these to your database before Message Manager will run.

-- AROs map to user groups (Message Manager only uses group-level AROs)
-- ACOs map to things that a user can try to do
-- the ARO-ACO mapping controls who is allowed to do those things, and who is not

-- ========================================================================
-- Don't try to edit these by hand! No, really!
-- But if you need to rebuid them:
-- -----------------------------------------------------------------------
-- If you create a new group *within the Message Manager admin* the ARO
-- table will automatically add it.
--
-- But if you add a new action to a controller, you'll need to add it to
-- the ACOS table. Add it to the apprpriate user groups in 
-- apps/Controllers/User.php's initDB action. 
-- Then the safest approach is probably to rebuilt it all:
--
-- first delete all records from aco and aros_acos then...
-- ...rebuild the aco table with:
-- Console/cake AclExtras.AclExtras aco_sync
-- ...and then (you'll need to edit the PHP to remove the redirect that
--    prevents this usually running) in browser hit:
-- /users/initdb
-- ...to repopulate the ARO-ACO mappings
-- When that's done, remember to edit apps/Controllers/User.php and
-- set initDB's redirect back on since for now that's the simplest way
-- to stop anyone running this when they don't mean to.
-- -----------------------------------------------------------------------


INSERT INTO aros VALUES(1, NULL, 'Group', 1, NULL, 1, 2);
INSERT INTO aros VALUES(2, NULL, 'Group', 2, NULL, 3, 4);
INSERT INTO aros VALUES(3, NULL, 'Group', 3, NULL, 5, 6);
INSERT INTO aros VALUES(4, NULL, 'Group', 4, NULL, 7, 8);

SELECT pg_catalog.setval(pg_get_serial_sequence('aros', 'id'), (SELECT MAX(id) FROM aros)+1);

INSERT INTO acos VALUES(1, NULL, NULL, NULL, 'controllers', 1, 130);
INSERT INTO acos VALUES(2, 1, NULL, NULL, 'Actions', 2, 13);
INSERT INTO acos VALUES(3, 2, NULL, NULL, 'index', 3, 4);
INSERT INTO acos VALUES(4, 2, NULL, NULL, 'view', 5, 6);
INSERT INTO acos VALUES(5, 2, NULL, NULL, 'delete', 7, 8);
INSERT INTO acos VALUES(6, 2, NULL, NULL, 'add', 9, 10);
INSERT INTO acos VALUES(7, 2, NULL, NULL, 'mm_json_response', 11, 12);
INSERT INTO acos VALUES(8, 1, NULL, NULL, 'BoilerplateStrings', 14, 25);
INSERT INTO acos VALUES(9, 8, NULL, NULL, 'index', 15, 16);
INSERT INTO acos VALUES(10, 8, NULL, NULL, 'edit', 17, 18);
INSERT INTO acos VALUES(11, 8, NULL, NULL, 'delete', 19, 20);
INSERT INTO acos VALUES(12, 8, NULL, NULL, 'add', 21, 22);
INSERT INTO acos VALUES(13, 8, NULL, NULL, 'mm_json_response', 23, 24);
INSERT INTO acos VALUES(14, 1, NULL, NULL, 'Groups', 26, 39);
INSERT INTO acos VALUES(15, 14, NULL, NULL, 'index', 27, 28);
INSERT INTO acos VALUES(16, 14, NULL, NULL, 'view', 29, 30);
INSERT INTO acos VALUES(17, 14, NULL, NULL, 'add', 31, 32);
INSERT INTO acos VALUES(18, 14, NULL, NULL, 'edit', 33, 34);
INSERT INTO acos VALUES(19, 14, NULL, NULL, 'delete', 35, 36);
INSERT INTO acos VALUES(20, 14, NULL, NULL, 'mm_json_response', 37, 38);
INSERT INTO acos VALUES(21, 1, NULL, NULL, 'MessageSources', 40, 59);
INSERT INTO acos VALUES(22, 21, NULL, NULL, 'index', 41, 42);
INSERT INTO acos VALUES(23, 21, NULL, NULL, 'view', 43, 44);
INSERT INTO acos VALUES(24, 21, NULL, NULL, 'delete', 45, 46);
INSERT INTO acos VALUES(25, 21, NULL, NULL, 'edit', 47, 48);
INSERT INTO acos VALUES(26, 21, NULL, NULL, 'add', 49, 50);
INSERT INTO acos VALUES(27, 21, NULL, NULL, 'client', 51, 52);
INSERT INTO acos VALUES(28, 21, NULL, NULL, 'gateway_test', 53, 54);
INSERT INTO acos VALUES(29, 21, NULL, NULL, 'gateway_logs', 55, 56);
INSERT INTO acos VALUES(30, 21, NULL, NULL, 'mm_json_response', 57, 58);
INSERT INTO acos VALUES(31, 1, NULL, NULL, 'Messages', 60, 99);
INSERT INTO acos VALUES(32, 31, NULL, NULL, 'index', 61, 62);
INSERT INTO acos VALUES(33, 31, NULL, NULL, 'available', 63, 64);
INSERT INTO acos VALUES(34, 31, NULL, NULL, 'view', 65, 66);
INSERT INTO acos VALUES(35, 31, NULL, NULL, 'lock', 67, 68);
INSERT INTO acos VALUES(36, 31, NULL, NULL, 'edit', 69, 70);
INSERT INTO acos VALUES(37, 31, NULL, NULL, 'reply', 71, 72);
INSERT INTO acos VALUES(38, 31, NULL, NULL, 'lock_unique', 73, 74);
INSERT INTO acos VALUES(39, 31, NULL, NULL, 'unlock', 75, 76);
INSERT INTO acos VALUES(40, 31, NULL, NULL, 'unlock_all', 77, 78);
INSERT INTO acos VALUES(41, 31, NULL, NULL, 'assign_fms_id', 79, 80);
INSERT INTO acos VALUES(42, 31, NULL, NULL, 'unassign_fms_id', 81, 82);
INSERT INTO acos VALUES(43, 31, NULL, NULL, 'hide', 83, 84);
INSERT INTO acos VALUES(44, 31, NULL, NULL, 'unhide', 85, 86);
INSERT INTO acos VALUES(45, 31, NULL, NULL, 'mark_as_not_a_reply', 87, 88);
INSERT INTO acos VALUES(46, 31, NULL, NULL, 'delete', 89, 90);
INSERT INTO acos VALUES(47, 31, NULL, NULL, 'add', 91, 92);
INSERT INTO acos VALUES(48, 31, NULL, NULL, 'incoming', 93, 94);
INSERT INTO acos VALUES(49, 31, NULL, NULL, 'purge_locks', 95, 96);
INSERT INTO acos VALUES(50, 31, NULL, NULL, 'mm_json_response', 97, 98);
INSERT INTO acos VALUES(51, 1, NULL, NULL, 'Pages', 100, 105);
INSERT INTO acos VALUES(52, 51, NULL, NULL, 'display', 101, 102);
INSERT INTO acos VALUES(53, 51, NULL, NULL, 'mm_json_response', 103, 104);
INSERT INTO acos VALUES(54, 1, NULL, NULL, 'Users', 106, 127);
INSERT INTO acos VALUES(55, 54, NULL, NULL, 'login', 107, 108);
INSERT INTO acos VALUES(56, 54, NULL, NULL, 'logout', 109, 110);
INSERT INTO acos VALUES(57, 54, NULL, NULL, 'index', 111, 112);
INSERT INTO acos VALUES(58, 54, NULL, NULL, 'view', 113, 114);
INSERT INTO acos VALUES(59, 54, NULL, NULL, 'add', 115, 116);
INSERT INTO acos VALUES(60, 54, NULL, NULL, 'edit', 117, 118);
INSERT INTO acos VALUES(61, 54, NULL, NULL, 'change_password', 119, 120);
INSERT INTO acos VALUES(62, 54, NULL, NULL, 'delete', 121, 122);
INSERT INTO acos VALUES(63, 54, NULL, NULL, 'initDB', 123, 124);
INSERT INTO acos VALUES(64, 54, NULL, NULL, 'mm_json_response', 125, 126);
INSERT INTO acos VALUES(65, 1, NULL, NULL, 'AclExtras', 128, 129);

SELECT pg_catalog.setval(pg_get_serial_sequence('acos', 'id'), (SELECT MAX(id) FROM acos)+1);

INSERT INTO aros_acos VALUES(1, 1, 1, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(2, 1, 60, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(3, 2, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(4, 2, 6, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(5, 2, 3, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(6, 2, 4, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(7, 2, 8, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(8, 2, 15, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(9, 2, 16, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(10, 2, 31, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(11, 2, 22, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(12, 2, 25, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(13, 2, 23, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(14, 2, 27, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(15, 2, 29, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(16, 2, 28, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(17, 2, 51, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(18, 2, 61, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(19, 2, 56, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(20, 3, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(21, 3, 9, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(22, 3, 41, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(23, 3, 33, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(24, 3, 43, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(25, 3, 35, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(26, 3, 38, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(27, 3, 45, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(28, 3, 37, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(29, 3, 39, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(30, 3, 40, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(31, 3, 27, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(32, 3, 51, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(33, 3, 61, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(34, 3, 56, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(35, 4, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(36, 4, 48, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(37, 4, 56, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(38, 4, 27, '1', '1', '1', '1');

SELECT pg_catalog.setval(pg_get_serial_sequence('aros_acos', 'id'), (SELECT MAX(id) FROM aros_acos)+1);

