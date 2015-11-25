/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-11-25 14:13:32
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
  `bu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bu_name` varchar(128) NOT NULL DEFAULT '',
  `bu_password` varchar(64) NOT NULL DEFAULT '',
  `bu_salt` char(64) NOT NULL COMMENT 'password salt',
  `bu_role` int(11) unsigned NOT NULL COMMENT 'user role',
  `bu_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1:valid, 0: invalid',
  `bu_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bu_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bad_history
-- ----------------------------
DROP TABLE IF EXISTS `bad_history`;
CREATE TABLE `bad_history` (
  `bh_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bh_happen_date` date NOT NULL,
  `bh_count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bh_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `bh_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dream_history
-- ----------------------------
DROP TABLE IF EXISTS `dream_history`;
CREATE TABLE `dream_history` (
  `dh_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dh_happen_date` date NOT NULL,
  `dh_count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `dh_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `dh_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dh_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_category
-- ----------------------------
DROP TABLE IF EXISTS `finance_category`;
CREATE TABLE `finance_category` (
  `fc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fc_name` varchar(255) NOT NULL DEFAULT '',
  `fc_parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fc_weight` int(10) unsigned NOT NULL DEFAULT '0',
  `fc_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fc_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fc_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_payment
-- ----------------------------
DROP TABLE IF EXISTS `finance_payment`;
CREATE TABLE `finance_payment` (
  `fp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fp_payment` float(9,1) unsigned NOT NULL,
  `fp_payment_date` date NOT NULL,
  `fp_detail` varchar(255) NOT NULL DEFAULT '',
  `fp_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fp_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fp_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finance_payment_map
-- ----------------------------
DROP TABLE IF EXISTS `finance_payment_map`;
CREATE TABLE `finance_payment_map` (
  `fpmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fp_id` int(10) unsigned NOT NULL COMMENT 'finance payment primary key',
  `fc_id` int(10) unsigned NOT NULL COMMENT 'finance category primary key',
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
