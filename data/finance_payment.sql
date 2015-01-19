/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-01-17 16:40:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for finance_payment
-- ----------------------------
DROP TABLE IF EXISTS `finance_payment`;
CREATE TABLE `finance_payment` (
  `fp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fp_payment` float(9,1) unsigned NOT NULL,
  `fp_payment_date` date NOT NULL,
  `fc_id` int(10) unsigned NOT NULL COMMENT 'finance category primary key',
  `fp_detail` varchar(255) NOT NULL DEFAULT '',
  `fp_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fp_create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fp_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
