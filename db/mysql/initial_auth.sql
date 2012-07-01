
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

--
-- Table structure for table `aros`
--

DROP TABLE IF EXISTS `aros`;
CREATE TABLE `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` VALUES(1, NULL, 'Group', 1, NULL, 1, 2);
INSERT INTO `aros` VALUES(2, NULL, 'Group', 2, NULL, 3, 4);
INSERT INTO `aros` VALUES(3, NULL, 'Group', 3, NULL, 5, 6);
INSERT INTO `aros` VALUES(4, NULL, 'Group', 4, NULL, 7, 8);




--
-- Table structure for table `acos`
--
DROP TABLE IF EXISTS `acos`;
CREATE TABLE `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=211 ;

--
-- Dumping data for table `acos`
--
INSERT INTO `acos` VALUES(211, NULL, NULL, NULL, 'controllers', 1, 110);
INSERT INTO `acos` VALUES(212, 211, NULL, NULL, 'Actions', 2, 13);
INSERT INTO `acos` VALUES(213, 212, NULL, NULL, 'index', 3, 4);
INSERT INTO `acos` VALUES(214, 212, NULL, NULL, 'view', 5, 6);
INSERT INTO `acos` VALUES(215, 212, NULL, NULL, 'delete', 7, 8);
INSERT INTO `acos` VALUES(216, 212, NULL, NULL, 'add', 9, 10);
INSERT INTO `acos` VALUES(217, 212, NULL, NULL, 'mm_json_response', 11, 12);
INSERT INTO `acos` VALUES(218, 211, NULL, NULL, 'Groups', 14, 27);
INSERT INTO `acos` VALUES(219, 218, NULL, NULL, 'index', 15, 16);
INSERT INTO `acos` VALUES(220, 218, NULL, NULL, 'view', 17, 18);
INSERT INTO `acos` VALUES(221, 218, NULL, NULL, 'add', 19, 20);
INSERT INTO `acos` VALUES(222, 218, NULL, NULL, 'edit', 21, 22);
INSERT INTO `acos` VALUES(223, 218, NULL, NULL, 'delete', 23, 24);
INSERT INTO `acos` VALUES(224, 218, NULL, NULL, 'mm_json_response', 25, 26);
INSERT INTO `acos` VALUES(225, 211, NULL, NULL, 'MessageSources', 28, 43);
INSERT INTO `acos` VALUES(226, 225, NULL, NULL, 'index', 29, 30);
INSERT INTO `acos` VALUES(227, 225, NULL, NULL, 'view', 31, 32);
INSERT INTO `acos` VALUES(228, 225, NULL, NULL, 'delete', 33, 34);
INSERT INTO `acos` VALUES(229, 225, NULL, NULL, 'edit', 35, 36);
INSERT INTO `acos` VALUES(230, 225, NULL, NULL, 'add', 37, 38);
INSERT INTO `acos` VALUES(231, 225, NULL, NULL, 'client', 39, 40);
INSERT INTO `acos` VALUES(232, 225, NULL, NULL, 'mm_json_response', 41, 42);
INSERT INTO `acos` VALUES(233, 211, NULL, NULL, 'Messages', 44, 81);
INSERT INTO `acos` VALUES(234, 233, NULL, NULL, 'index', 45, 46);
INSERT INTO `acos` VALUES(235, 233, NULL, NULL, 'available', 47, 48);
INSERT INTO `acos` VALUES(236, 233, NULL, NULL, 'view', 49, 50);
INSERT INTO `acos` VALUES(237, 233, NULL, NULL, 'lock', 51, 52);
INSERT INTO `acos` VALUES(238, 233, NULL, NULL, 'edit', 53, 54);
INSERT INTO `acos` VALUES(239, 233, NULL, NULL, 'reply', 55, 56);
INSERT INTO `acos` VALUES(240, 233, NULL, NULL, 'lock_unique', 57, 58);
INSERT INTO `acos` VALUES(241, 233, NULL, NULL, 'unlock', 59, 60);
INSERT INTO `acos` VALUES(242, 233, NULL, NULL, 'unlock_all', 61, 62);
INSERT INTO `acos` VALUES(243, 233, NULL, NULL, 'assign_fms_id', 63, 64);
INSERT INTO `acos` VALUES(244, 233, NULL, NULL, 'unassign_fms_id', 65, 66);
INSERT INTO `acos` VALUES(245, 233, NULL, NULL, 'hide', 67, 68);
INSERT INTO `acos` VALUES(246, 233, NULL, NULL, 'unhide', 69, 70);
INSERT INTO `acos` VALUES(247, 233, NULL, NULL, 'delete', 71, 72);
INSERT INTO `acos` VALUES(248, 233, NULL, NULL, 'add', 73, 74);
INSERT INTO `acos` VALUES(249, 233, NULL, NULL, 'incoming', 75, 76);
INSERT INTO `acos` VALUES(250, 233, NULL, NULL, 'purge_locks', 77, 78);
INSERT INTO `acos` VALUES(251, 233, NULL, NULL, 'mm_json_response', 79, 80);
INSERT INTO `acos` VALUES(252, 211, NULL, NULL, 'Pages', 82, 87);
INSERT INTO `acos` VALUES(253, 252, NULL, NULL, 'display', 83, 84);
INSERT INTO `acos` VALUES(254, 252, NULL, NULL, 'mm_json_response', 85, 86);
INSERT INTO `acos` VALUES(255, 211, NULL, NULL, 'Users', 88, 107);
INSERT INTO `acos` VALUES(256, 255, NULL, NULL, 'login', 89, 90);
INSERT INTO `acos` VALUES(257, 255, NULL, NULL, 'logout', 91, 92);
INSERT INTO `acos` VALUES(258, 255, NULL, NULL, 'index', 93, 94);
INSERT INTO `acos` VALUES(259, 255, NULL, NULL, 'view', 95, 96);
INSERT INTO `acos` VALUES(260, 255, NULL, NULL, 'add', 97, 98);
INSERT INTO `acos` VALUES(261, 255, NULL, NULL, 'edit', 99, 100);
INSERT INTO `acos` VALUES(262, 255, NULL, NULL, 'delete', 101, 102);
INSERT INTO `acos` VALUES(263, 255, NULL, NULL, 'initDB', 103, 104);
INSERT INTO `acos` VALUES(264, 255, NULL, NULL, 'mm_json_response', 105, 106);
INSERT INTO `acos` VALUES(265, 211, NULL, NULL, 'AclExtras', 108, 109);

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL DEFAULT '0',
  `_read` varchar(2) NOT NULL DEFAULT '0',
  `_update` varchar(2) NOT NULL DEFAULT '0',
  `_delete` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=165 ;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` VALUES(165, 1, 211, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(166, 2, 211, '-1', '-1', '-1', '-1');
INSERT INTO `aros_acos` VALUES(167, 2, 216, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(168, 2, 213, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(169, 2, 214, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(170, 2, 219, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(171, 2, 220, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(172, 2, 233, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(173, 2, 226, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(174, 2, 229, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(175, 2, 227, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(176, 2, 231, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(177, 2, 252, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(178, 2, 257, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(179, 3, 211, '-1', '-1', '-1', '-1');
INSERT INTO `aros_acos` VALUES(180, 3, 243, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(181, 3, 235, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(182, 3, 237, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(183, 3, 240, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(184, 3, 239, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(185, 3, 241, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(186, 3, 242, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(187, 3, 231, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(188, 3, 252, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(189, 3, 257, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(190, 4, 211, '-1', '-1', '-1', '-1');
INSERT INTO `aros_acos` VALUES(191, 4, 249, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(192, 4, 257, '1', '1', '1', '1');
INSERT INTO `aros_acos` VALUES(193, 4, 231, '1', '1', '1', '1');



