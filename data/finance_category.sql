/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-01-17 16:40:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for finance_category
-- ----------------------------
DROP TABLE IF EXISTS `finance_category`;
CREATE TABLE `finance_category` (
  `fc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fc_name` varchar(255) NOT NULL DEFAULT '',
  `fc_parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fc_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fc_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fc_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
