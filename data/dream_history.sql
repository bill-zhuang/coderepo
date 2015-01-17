/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-01-17 15:14:38
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;
