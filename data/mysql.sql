/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2016-11-15 17:10:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for backend_acl
-- ----------------------------
DROP TABLE IF EXISTS `backend_acl`;
CREATE TABLE `backend_acl` (
  `baid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'acl name',
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT 'module name',
  `controller` varchar(100) NOT NULL DEFAULT '' COMMENT 'controller name',
  `action` varchar(100) NOT NULL DEFAULT '' COMMENT 'action name',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT 'status: 1-valid, 0-invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`baid`),
  UNIQUE KEY `idx_m_c_a` (`module`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Table structure for backend_role
-- ----------------------------
DROP TABLE IF EXISTS `backend_role`;
CREATE TABLE `backend_role` (
  `brid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(100) NOT NULL DEFAULT '' COMMENT 'backend role name',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'status: 1-valid, 0-invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`brid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for backend_role_acl
-- ----------------------------
DROP TABLE IF EXISTS `backend_role_acl`;
CREATE TABLE `backend_role_acl` (
  `braid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brid` int(10) unsigned NOT NULL COMMENT 'backend role pkid',
  `baid` int(10) unsigned NOT NULL COMMENT 'backend_acl pkid',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'status: 1-valid, 0-invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`braid`)
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
  `brid` int(10) unsigned NOT NULL COMMENT 'backend role pkid',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT 'remark',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1:valid, 0: invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`buid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for eject_history
-- ----------------------------
DROP TABLE IF EXISTS `eject_history`;
CREATE TABLE `eject_history` (
  `ehid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `happen_date` date NOT NULL,
  `count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-dream, 2-bad',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'status',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ehid`),
  UNIQUE KEY `idx_happend_date_type` (`happen_date`,`type`)
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
  `payment` float(9,2) unsigned NOT NULL,
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
  PRIMARY KEY (`grhid`),
  UNIQUE KEY `idx_happen_date` (`happen_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for house_sale
-- ----------------------------
DROP TABLE IF EXISTS `house_sale`;
CREATE TABLE `house_sale` (
  `hsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `sales` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for toutiao
-- ----------------------------
DROP TABLE IF EXISTS `toutiao`;
CREATE TABLE `toutiao` (
  `ttid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ttid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for lagou_category
-- ----------------------------
DROP TABLE IF EXISTS `lagou_category`;
CREATE TABLE `lagou_category` (
  `caid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`caid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for lagou_city
-- ----------------------------
DROP TABLE IF EXISTS `lagou_city`;
CREATE TABLE `lagou_city` (
  `ctid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `letter` varchar(10) NOT NULL DEFAULT '' COMMENT 'city first letter',
  `lg_ctid` int(10) unsigned NOT NULL DEFAULT '0',
  `lg_code` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ctid`),
  UNIQUE KEY `idx_lg_ctid` (`lg_ctid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for lagou_job
-- ----------------------------
DROP TABLE IF EXISTS `lagou_job`;
CREATE TABLE `lagou_job` (
  `joid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `caid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`joid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for lagou_job_analysis
-- ----------------------------
DROP TABLE IF EXISTS `lagou_job_analysis`;
CREATE TABLE `lagou_job_analysis` (
  `jaid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `joid` int(10) unsigned NOT NULL DEFAULT '0',
  `lg_ctid` int(10) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `num_plus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '职位数是否超过500（拉勾职位数超过500用500+标记）',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`jaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
