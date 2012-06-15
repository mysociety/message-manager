
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
-- Then delete all records from aco and aros_acos then...
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

--
-- Dumping data for table aros
--

INSERT INTO aros VALUES(1, NULL, 'Group', 1, NULL, 1, 2);
INSERT INTO aros VALUES(2, NULL, 'Group', 2, NULL, 3, 4);
INSERT INTO aros VALUES(3, NULL, 'Group', 3, NULL, 5, 6);
INSERT INTO aros VALUES(4, NULL, 'Group', 4, NULL, 7, 8);

--
-- Dumping data for table acos
--

INSERT INTO acos VALUES(158, NULL, NULL, NULL, 'controllers', 1, 106);
INSERT INTO acos VALUES(159, 158, NULL, NULL, 'Actions', 2, 11);
INSERT INTO acos VALUES(160, 159, NULL, NULL, 'index', 3, 4);
INSERT INTO acos VALUES(161, 159, NULL, NULL, 'view', 5, 6);
INSERT INTO acos VALUES(162, 159, NULL, NULL, 'delete', 7, 8);
INSERT INTO acos VALUES(163, 159, NULL, NULL, 'mm_json_response', 9, 10);
INSERT INTO acos VALUES(164, 158, NULL, NULL, 'Groups', 12, 25);
INSERT INTO acos VALUES(165, 164, NULL, NULL, 'index', 13, 14);
INSERT INTO acos VALUES(166, 164, NULL, NULL, 'view', 15, 16);
INSERT INTO acos VALUES(167, 164, NULL, NULL, 'add', 17, 18);
INSERT INTO acos VALUES(168, 164, NULL, NULL, 'edit', 19, 20);
INSERT INTO acos VALUES(169, 164, NULL, NULL, 'delete', 21, 22);
INSERT INTO acos VALUES(170, 164, NULL, NULL, 'mm_json_response', 23, 24);
INSERT INTO acos VALUES(171, 158, NULL, NULL, 'MessageSources', 26, 41);
INSERT INTO acos VALUES(172, 171, NULL, NULL, 'index', 27, 28);
INSERT INTO acos VALUES(173, 171, NULL, NULL, 'view', 29, 30);
INSERT INTO acos VALUES(174, 171, NULL, NULL, 'delete', 31, 32);
INSERT INTO acos VALUES(175, 171, NULL, NULL, 'edit', 33, 34);
INSERT INTO acos VALUES(176, 171, NULL, NULL, 'add', 35, 36);
INSERT INTO acos VALUES(177, 171, NULL, NULL, 'client', 37, 38);
INSERT INTO acos VALUES(178, 171, NULL, NULL, 'mm_json_response', 39, 40);
INSERT INTO acos VALUES(179, 158, NULL, NULL, 'Messages', 42, 77);
INSERT INTO acos VALUES(180, 179, NULL, NULL, 'index', 43, 44);
INSERT INTO acos VALUES(181, 179, NULL, NULL, 'available', 45, 46);
INSERT INTO acos VALUES(182, 179, NULL, NULL, 'view', 47, 48);
INSERT INTO acos VALUES(183, 179, NULL, NULL, 'lock', 49, 50);
INSERT INTO acos VALUES(184, 179, NULL, NULL, 'reply', 51, 52);
INSERT INTO acos VALUES(185, 179, NULL, NULL, 'lock_unique', 53, 54);
INSERT INTO acos VALUES(186, 179, NULL, NULL, 'unlock', 55, 56);
INSERT INTO acos VALUES(187, 179, NULL, NULL, 'unlock_all', 57, 58);
INSERT INTO acos VALUES(188, 179, NULL, NULL, 'assign_fms_id', 59, 60);
INSERT INTO acos VALUES(189, 179, NULL, NULL, 'unassign_fms_id', 61, 62);
INSERT INTO acos VALUES(190, 179, NULL, NULL, 'hide', 63, 64);
INSERT INTO acos VALUES(191, 179, NULL, NULL, 'unhide', 65, 66);
INSERT INTO acos VALUES(192, 179, NULL, NULL, 'delete', 67, 68);
INSERT INTO acos VALUES(193, 179, NULL, NULL, 'add', 69, 70);
INSERT INTO acos VALUES(194, 179, NULL, NULL, 'incoming', 71, 72);
INSERT INTO acos VALUES(195, 179, NULL, NULL, 'purge_locks', 73, 74);
INSERT INTO acos VALUES(196, 179, NULL, NULL, 'mm_json_response', 75, 76);
INSERT INTO acos VALUES(197, 158, NULL, NULL, 'Pages', 78, 83);
INSERT INTO acos VALUES(198, 197, NULL, NULL, 'display', 79, 80);
INSERT INTO acos VALUES(199, 197, NULL, NULL, 'mm_json_response', 81, 82);
INSERT INTO acos VALUES(200, 158, NULL, NULL, 'Users', 84, 103);
INSERT INTO acos VALUES(201, 200, NULL, NULL, 'login', 85, 86);
INSERT INTO acos VALUES(202, 200, NULL, NULL, 'logout', 87, 88);
INSERT INTO acos VALUES(203, 200, NULL, NULL, 'index', 89, 90);
INSERT INTO acos VALUES(204, 200, NULL, NULL, 'view', 91, 92);
INSERT INTO acos VALUES(205, 200, NULL, NULL, 'add', 93, 94);
INSERT INTO acos VALUES(206, 200, NULL, NULL, 'edit', 95, 96);
INSERT INTO acos VALUES(207, 200, NULL, NULL, 'delete', 97, 98);
INSERT INTO acos VALUES(208, 200, NULL, NULL, 'initDB', 99, 100);
INSERT INTO acos VALUES(209, 200, NULL, NULL, 'mm_json_response', 101, 102);
INSERT INTO acos VALUES(210, 158, NULL, NULL, 'AclExtras', 104, 105);


--
-- Dumping data for table aros_acos
--

INSERT INTO aros_acos VALUES(140, 1, 158, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(141, 2, 158, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(142, 2, 160, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(143, 2, 161, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(144, 2, 165, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(145, 2, 166, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(146, 2, 179, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(147, 2, 172, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(148, 2, 173, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(149, 2, 177, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(150, 2, 197, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(151, 2, 202, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(152, 3, 158, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(153, 3, 188, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(154, 3, 181, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(155, 3, 183, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(156, 3, 185, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(157, 3, 184, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(158, 3, 186, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(159, 3, 187, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(160, 3, 177, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(161, 3, 202, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(162, 4, 158, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES(163, 4, 194, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(164, 4, 202, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES(165, 4, 177, '1', '1', '1', '1');

