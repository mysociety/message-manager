
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `messages`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--
-- actions log and provide an audit trail on the history
-- of messages

CREATE TABLE `actions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created` datetime,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `message_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `note` text
  KEY `created` (`created`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `action_types`
--

CREATE TABLE `action_types` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `action_types`
--

INSERT INTO `action_types` VALUES(0, 'unknown',  'unknown');
INSERT INTO `action_types` VALUES(1, 'lock',     'claimed lock on this message');
INSERT INTO `action_types` VALUES(2, 'unlock',   'lock relinquished on this message');
INSERT INTO `action_types` VALUES(3, 'assign',   'assigned message to a report');
INSERT INTO `action_types` VALUES(4, 'unassign', 'message was unassigned from a report');
INSERT INTO `action_types` VALUES(5, 'hide',     'hid message');
INSERT INTO `action_types` VALUES(6, 'unhide',   'revealed message by unhiding it');
INSERT INTO `action_types` VALUES(7, 'note',     'note');
INSERT INTO `action_types` VALUES(8, 'reply',    'replied to message');


-- --------------------------------------------------------

--
-- Table structure for table `messages`
--
--
CREATE TABLE `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `external_id`  varchar(32) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `msisdn` varchar(255) DEFAULT NULL,
  `sender_token` varchar(255) DEFAULT NULL,
  `message` text,
  `created` datetime,
  `received` datetime NULL DEFAULT NULL,
  `modified` datetime,
  `replied` datetime,
  `assigned` datetime NULL DEFAULT NULL,
  `lock_expires` datetime NULL DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `owner_id` int(11) DEFAULT NULL,
  `session_key` varchar(255) NULL,
  `fms_id` int(11) DEFAULT NULL,
  `tag` varchar(64) DEFAULT NULL
  KEY `external_id` (`external_id`,`msisdn`,`status`,`owner`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` VALUES(1, NULL, 1, '55512345678', '41fdc1d415ab820375a905629d1c1442', 'Welcome to Message Manager', '2012-05-25 01:02:00', '2012-05-25 01:02:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL,  NULL);

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--
-- lookup table for the different statuses a message can have
-- Note that "locked" is not a status, and is implied by the
-- lock_expires date being in the future

CREATE TABLE `statuses` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` VALUES(0, 'unknown',   'unknown');
INSERT INTO `statuses` VALUES(1, 'available', 'available for activity');
INSERT INTO `statuses` VALUES(2, 'assigned',  'has been assigned to a FMS report');
INSERT INTO `statuses` VALUES(3, 'hidden',    'has been hidden (deleted)');


CREATE TABLE `message_sources` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `description` text,
  `url` varchar(255),
  `ip_addresses` text,
  `user_id` INT(11) NOT NULL,
  `created` datetime,
  `modified` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `message_sources` VALUES(6, 'mock-gateway', 'This is a mock message source which could be an SMS gateway, configured to hit /messages/incoming to enter new messages. It is associated with a user in the \"message-sources\" user group, which it will use to authenticate that call.', 'www.example.com', '12.12.12.12', '4', '2012-06-01 00:00:00', '2012-06-01 00:00:00');

-- -----------------------------------------------------------
-- User auth for Cake

CREATE TABLE groups (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created DATETIME,
    modified DATETIME
);
--
-- Dumping data for table `groups`
--

INSERT INTO `groups` VALUES(1, 'administrators', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `groups` VALUES(2, 'managers', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `groups` VALUES(3, 'api-users', '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `groups` VALUES(4, 'message-sources', '2012-05-25 00:00:00', '2012-05-25 00:00:00');


CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password CHAR(40) NOT NULL,
    group_id INT(11) NOT NULL,
    allowed_tags VARCHAR(255),
    can_reply INT(1) NOT NULL DEFAULT '0',
    created DATETIME,
    modified DATETIME
);

INSERT INTO `users` VALUES(1, 'admin', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 1, NULL, 0, '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `users` VALUES(2, 'manager', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 2, NULL, 0, '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `users` VALUES(3, 'user', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 3, NULL, 0, '2012-05-25 00:00:00', '2012-05-25 00:00:00');
INSERT INTO `users` VALUES(4, 'source', '78ff58c353c9b6d1c60ac48b3e37536e7e8b07e1', 4, NULL, 0, '2012-05-25 00:00:00', '2012-05-25 00:00:00');

