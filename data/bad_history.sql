/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-01-17 15:14:30
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
