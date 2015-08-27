/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50522
Source Host           : localhost:3306
Source Database       : bill

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2015-08-27 14:06:47
*/

SET FOREIGN_KEY_CHECKS=0;

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
