/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-12-02 14:37:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for backend_log
-- ----------------------------
DROP TABLE IF EXISTS `backend_log`;
CREATE TABLE `backend_log` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT 'type(insert, update, delete)',
  `table` varchar(255) NOT NULL DEFAULT '' COMMENT 'table name',
  `content` text NOT NULL COMMENT 'SQL',
  `buid` int(11) NOT NULL COMMENT 'backend_user primary key',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : valid 0 : invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for backend_user
-- ----------------------------
DROP TABLE IF EXISTS `backend_user`;
CREATE TABLE `backend_user` (
  `buid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `salt` char(64) NOT NULL COMMENT 'password salt',
  `role` int(11) unsigned NOT NULL COMMENT 'user role',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1:valid, 0: invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`buid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bad_history
-- ----------------------------
DROP TABLE IF EXISTS `bad_history`;
CREATE TABLE `bad_history` (
  `bhid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `happen_date` date NOT NULL,
  `count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bhid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dream_history
-- ----------------------------
DROP TABLE IF EXISTS `dream_history`;
CREATE TABLE `dream_history` (
  `dhid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `happen_date` date NOT NULL,
  `count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dhid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_category
-- ----------------------------
DROP TABLE IF EXISTS `finance_category`;
CREATE TABLE `finance_category` (
  `fcid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `weight` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_payment
-- ----------------------------
DROP TABLE IF EXISTS `finance_payment`;
CREATE TABLE `finance_payment` (
  `fpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment` float(9,1) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `detail` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fpid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_payment_map
-- ----------------------------
DROP TABLE IF EXISTS `finance_payment_map`;
CREATE TABLE `finance_payment_map` (
  `fpmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fpid` int(10) unsigned NOT NULL COMMENT 'finance payment primary key',
  `fcid` int(10) unsigned NOT NULL COMMENT 'finance category primary key',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fpmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for grain_recycle_history
-- ----------------------------
DROP TABLE IF EXISTS `grain_recycle_history`;
CREATE TABLE `grain_recycle_history` (
  `grhid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `happen_date` date NOT NULL,
  `count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`grhid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
