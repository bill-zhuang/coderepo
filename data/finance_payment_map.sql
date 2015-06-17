/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-06-17 11:24:33
*/

SET FOREIGN_KEY_CHECKS=0;

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
