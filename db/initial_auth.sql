
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

INSERT INTO acos VALUES(1, NULL, NULL, NULL, 'controllers', 1, 126);
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
INSERT INTO acos VALUES(21, 1, NULL, NULL, 'MessageSources', 40, 57);
INSERT INTO acos VALUES(22, 21, NULL, NULL, 'index', 41, 42);
INSERT INTO acos VALUES(23, 21, NULL, NULL, 'view', 43, 44);
INSERT INTO acos VALUES(24, 21, NULL, NULL, 'delete', 45, 46);
INSERT INTO acos VALUES(25, 21, NULL, NULL, 'edit', 47, 48);
INSERT INTO acos VALUES(26, 21, NULL, NULL, 'add', 49, 50);
INSERT INTO acos VALUES(27, 21, NULL, NULL, 'client', 51, 52);
INSERT INTO acos VALUES(28, 21, NULL, NULL, 'gateway_test', 53, 54);
INSERT INTO acos VALUES(29, 21, NULL, NULL, 'mm_json_response', 55, 56);
INSERT INTO acos VALUES(30, 1, NULL, NULL, 'Messages', 58, 95);
INSERT INTO acos VALUES(31, 30, NULL, NULL, 'index', 59, 60);
INSERT INTO acos VALUES(32, 30, NULL, NULL, 'available', 61, 62);
INSERT INTO acos VALUES(33, 30, NULL, NULL, 'view', 63, 64);
INSERT INTO acos VALUES(34, 30, NULL, NULL, 'lock', 65, 66);
INSERT INTO acos VALUES(35, 30, NULL, NULL, 'edit', 67, 68);
INSERT INTO acos VALUES(36, 30, NULL, NULL, 'reply', 69, 70);
INSERT INTO acos VALUES(37, 30, NULL, NULL, 'lock_unique', 71, 72);
INSERT INTO acos VALUES(38, 30, NULL, NULL, 'unlock', 73, 74);
INSERT INTO acos VALUES(39, 30, NULL, NULL, 'unlock_all', 75, 76);
INSERT INTO acos VALUES(40, 30, NULL, NULL, 'assign_fms_id', 77, 78);
INSERT INTO acos VALUES(41, 30, NULL, NULL, 'unassign_fms_id', 79, 80);
INSERT INTO acos VALUES(42, 30, NULL, NULL, 'hide', 81, 82);
INSERT INTO acos VALUES(43, 30, NULL, NULL, 'unhide', 83, 84);
INSERT INTO acos VALUES(44, 30, NULL, NULL, 'delete', 85, 86);
INSERT INTO acos VALUES(45, 30, NULL, NULL, 'add', 87, 88);
INSERT INTO acos VALUES(46, 30, NULL, NULL, 'incoming', 89, 90);
INSERT INTO acos VALUES(47, 30, NULL, NULL, 'purge_locks', 91, 92);
INSERT INTO acos VALUES(48, 30, NULL, NULL, 'mm_json_response', 93, 94);
INSERT INTO acos VALUES(49, 1, NULL, NULL, 'Pages', 96, 101);
INSERT INTO acos VALUES(50, 49, NULL, NULL, 'display', 97, 98);
INSERT INTO acos VALUES(51, 49, NULL, NULL, 'mm_json_response', 99, 100);
INSERT INTO acos VALUES(52, 1, NULL, NULL, 'Users', 102, 123);
INSERT INTO acos VALUES(53, 52, NULL, NULL, 'login', 103, 104);
INSERT INTO acos VALUES(54, 52, NULL, NULL, 'logout', 105, 106);
INSERT INTO acos VALUES(55, 52, NULL, NULL, 'index', 107, 108);
INSERT INTO acos VALUES(56, 52, NULL, NULL, 'view', 109, 110);
INSERT INTO acos VALUES(57, 52, NULL, NULL, 'add', 111, 112);
INSERT INTO acos VALUES(58, 52, NULL, NULL, 'edit', 113, 114);
INSERT INTO acos VALUES(59, 52, NULL, NULL, 'change_password', 115, 116);
INSERT INTO acos VALUES(60, 52, NULL, NULL, 'delete', 117, 118);
INSERT INTO acos VALUES(61, 52, NULL, NULL, 'initDB', 119, 120);
INSERT INTO acos VALUES(62, 52, NULL, NULL, 'mm_json_response', 121, 122);
INSERT INTO acos VALUES(63, 1, NULL, NULL, 'AclExtras', 124, 125);

SELECT pg_catalog.setval(pg_get_serial_sequence('acos', 'id'), (SELECT MAX(id) FROM acos)+1);

INSERT INTO aros_acos VALUES(1, 1, 1, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(2, 1, 58, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(3, 2, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(4, 2, 6, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(5, 2, 3, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(6, 2, 4, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(7, 2, 8, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(8, 2, 15, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(9, 2, 16, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(10, 2, 30, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(11, 2, 22, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(12, 2, 25, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(13, 2, 23, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(14, 2, 27, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(15, 2, 49, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(16, 2, 59, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(17, 2, 54, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(18, 3, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(19, 3, 9, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(20, 3, 40, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(21, 3, 32, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(22, 3, 34, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(23, 3, 37, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(24, 3, 36, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(25, 3, 38, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(26, 3, 39, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(27, 3, 27, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(28, 3, 49, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(29, 3, 59, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(30, 3, 54, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(31, 4, 1, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(32, 4, 46, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(33, 4, 54, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(34, 4, 27, '1', '1', '1', '1');

SELECT pg_catalog.setval(pg_get_serial_sequence('aros_acos', 'id'), (SELECT MAX(id) FROM aros_acos)+1);

