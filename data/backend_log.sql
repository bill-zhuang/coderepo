/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-05-29 16:30:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for backend_log
-- ----------------------------
DROP TABLE IF EXISTS `backend_log`;
CREATE TABLE `backend_log` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT 'SQL',
  `buid` int(11) NOT NULL COMMENT 'backend_user primary key',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : valid 0 : invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
