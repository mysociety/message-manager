
-- Essential auth data for Message Manager
-- You must add these to your database before Message Manager will run.

-- AROs map to user groups (Message Manager only uses group-level AROs)
-- ACOs map to things that a user can try to do
-- the ARO-ACO mapping controls who is allowed to do those things, and who is not

-- ========================================================================
-- Don't try to edit these by hand! 
-- No, really!
-- But if you need to change them:
--
--         ***                                            ***
--         *** see documentation/authorisation_how_to.md  ***
--         ***                                            ***
-- 
-- ...for essential information on how to rebuild them
-- -----------------------------------------------------------------------


INSERT INTO aros VALUES(1, NULL, 'Group', 1, NULL, 1, 2);
INSERT INTO aros VALUES(2, NULL, 'Group', 2, NULL, 3, 4);
INSERT INTO aros VALUES(3, NULL, 'Group', 3, NULL, 5, 6);
INSERT INTO aros VALUES(4, NULL, 'Group', 4, NULL, 7, 8);

SELECT pg_catalog.setval(pg_get_serial_sequence('aros', 'id'), (SELECT MAX(id) FROM aros)+1);

INSERT INTO acos VALUES (102, 97, NULL, NULL, 'edit', 69, 70);
INSERT INTO acos VALUES (69, 68, NULL, NULL, 'index', 3, 4);
INSERT INTO acos VALUES (103, 97, NULL, NULL, 'reply', 71, 72);
INSERT INTO acos VALUES (70, 68, NULL, NULL, 'view', 5, 6);
INSERT INTO acos VALUES (127, 121, NULL, NULL, 'edit', 119, 120);
INSERT INTO acos VALUES (71, 68, NULL, NULL, 'delete', 7, 8);
INSERT INTO acos VALUES (104, 97, NULL, NULL, 'lock_unique', 73, 74);
INSERT INTO acos VALUES (72, 68, NULL, NULL, 'add', 9, 10);
INSERT INTO acos VALUES (68, 67, NULL, NULL, 'Actions', 2, 13);
INSERT INTO acos VALUES (73, 68, NULL, NULL, 'mm_json_response', 11, 12);
INSERT INTO acos VALUES (105, 97, NULL, NULL, 'unlock', 75, 76);
INSERT INTO acos VALUES (75, 74, NULL, NULL, 'index', 15, 16);
INSERT INTO acos VALUES (128, 121, NULL, NULL, 'change_password', 121, 122);
INSERT INTO acos VALUES (106, 97, NULL, NULL, 'unlock_all', 77, 78);
INSERT INTO acos VALUES (76, 74, NULL, NULL, 'edit', 17, 18);
INSERT INTO acos VALUES (77, 74, NULL, NULL, 'delete', 19, 20);
INSERT INTO acos VALUES (107, 97, NULL, NULL, 'assign_fms_id', 79, 80);
INSERT INTO acos VALUES (78, 74, NULL, NULL, 'add', 21, 22);
INSERT INTO acos VALUES (74, 67, NULL, NULL, 'BoilerplateStrings', 14, 25);
INSERT INTO acos VALUES (79, 74, NULL, NULL, 'mm_json_response', 23, 24);
INSERT INTO acos VALUES (129, 121, NULL, NULL, 'delete', 123, 124);
INSERT INTO acos VALUES (108, 97, NULL, NULL, 'unassign_fms_id', 81, 82);
INSERT INTO acos VALUES (81, 80, NULL, NULL, 'index', 27, 28);
INSERT INTO acos VALUES (82, 80, NULL, NULL, 'view', 29, 30);
INSERT INTO acos VALUES (109, 97, NULL, NULL, 'hide', 83, 84);
INSERT INTO acos VALUES (83, 80, NULL, NULL, 'add', 31, 32);
INSERT INTO acos VALUES (130, 121, NULL, NULL, 'initDB', 125, 126);
INSERT INTO acos VALUES (84, 80, NULL, NULL, 'edit', 33, 34);
INSERT INTO acos VALUES (110, 97, NULL, NULL, 'unhide', 85, 86);
INSERT INTO acos VALUES (85, 80, NULL, NULL, 'delete', 35, 36);
INSERT INTO acos VALUES (80, 67, NULL, NULL, 'Groups', 26, 39);
INSERT INTO acos VALUES (86, 80, NULL, NULL, 'mm_json_response', 37, 38);
INSERT INTO acos VALUES (111, 97, NULL, NULL, 'mark_as_not_a_reply', 87, 88);
INSERT INTO acos VALUES (121, 67, NULL, NULL, 'Users', 108, 129);
INSERT INTO acos VALUES (88, 87, NULL, NULL, 'index', 41, 42);
INSERT INTO acos VALUES (131, 121, NULL, NULL, 'mm_json_response', 127, 128);
INSERT INTO acos VALUES (112, 97, NULL, NULL, 'delete', 89, 90);
INSERT INTO acos VALUES (89, 87, NULL, NULL, 'view', 43, 44);
INSERT INTO acos VALUES (90, 87, NULL, NULL, 'delete', 45, 46);
INSERT INTO acos VALUES (67, NULL, NULL, NULL, 'controllers', 1, 132);
INSERT INTO acos VALUES (113, 97, NULL, NULL, 'add', 91, 92);
INSERT INTO acos VALUES (91, 87, NULL, NULL, 'edit', 47, 48);
INSERT INTO acos VALUES (132, 67, NULL, NULL, 'AclExtras', 130, 131);
INSERT INTO acos VALUES (92, 87, NULL, NULL, 'add', 49, 50);
INSERT INTO acos VALUES (114, 97, NULL, NULL, 'incoming', 93, 94);
INSERT INTO acos VALUES (93, 87, NULL, NULL, 'client', 51, 52);
INSERT INTO acos VALUES (94, 87, NULL, NULL, 'gateway_test', 53, 54);
INSERT INTO acos VALUES (115, 97, NULL, NULL, 'search', 95, 96);
INSERT INTO acos VALUES (95, 87, NULL, NULL, 'gateway_logs', 55, 56);
INSERT INTO acos VALUES (87, 67, NULL, NULL, 'MessageSources', 40, 59);
INSERT INTO acos VALUES (96, 87, NULL, NULL, 'mm_json_response', 57, 58);
INSERT INTO acos VALUES (116, 97, NULL, NULL, 'purge_locks', 97, 98);
INSERT INTO acos VALUES (98, 97, NULL, NULL, 'index', 61, 62);
INSERT INTO acos VALUES (97, 67, NULL, NULL, 'Messages', 60, 101);
INSERT INTO acos VALUES (99, 97, NULL, NULL, 'available', 63, 64);
INSERT INTO acos VALUES (117, 97, NULL, NULL, 'mm_json_response', 99, 100);
INSERT INTO acos VALUES (100, 97, NULL, NULL, 'view', 65, 66);
INSERT INTO acos VALUES (101, 97, NULL, NULL, 'lock', 67, 68);
INSERT INTO acos VALUES (119, 118, NULL, NULL, 'display', 103, 104);
INSERT INTO acos VALUES (118, 67, NULL, NULL, 'Pages', 102, 107);
INSERT INTO acos VALUES (120, 118, NULL, NULL, 'mm_json_response', 105, 106);
INSERT INTO acos VALUES (122, 121, NULL, NULL, 'login', 109, 110);
INSERT INTO acos VALUES (123, 121, NULL, NULL, 'logout', 111, 112);
INSERT INTO acos VALUES (124, 121, NULL, NULL, 'index', 113, 114);
INSERT INTO acos VALUES (125, 121, NULL, NULL, 'view', 115, 116);
INSERT INTO acos VALUES (126, 121, NULL, NULL, 'add', 117, 118);

SELECT pg_catalog.setval(pg_get_serial_sequence('acos', 'id'), (SELECT MAX(id) FROM acos)+1);

INSERT INTO aros_acos VALUES (39, 1, 67, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (40, 1, 127, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (41, 2, 67, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (42, 2, 72, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (43, 2, 69, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (44, 2, 70, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (45, 2, 74, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (46, 2, 81, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (47, 2, 82, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (48, 2, 97, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (49, 2, 88, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (50, 2, 91, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (51, 2, 89, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (52, 2, 93, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (53, 2, 95, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (54, 2, 94, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (55, 2, 118, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (56, 2, 128, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (57, 2, 123, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (58, 3, 67, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (59, 3, 75, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (60, 3, 107, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (61, 3, 99, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (62, 3, 109, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (63, 3, 101, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (64, 3, 104, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (65, 3, 111, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (66, 3, 103, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (67, 3, 105, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (68, 3, 106, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (69, 3, 93, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (70, 3, 118, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (71, 3, 128, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (72, 3, 123, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (73, 4, 67, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (74, 4, 114, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (75, 4, 123, '1', '1', '1', '1');
INSERT INTO aros_acos VALUES (76, 4, 93, '1', '1', '1', '1');

SELECT pg_catalog.setval(pg_get_serial_sequence('aros_acos', 'id'), (SELECT MAX(id) FROM aros_acos)+1);

