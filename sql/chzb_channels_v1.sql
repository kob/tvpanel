--
-- MySQL database dump
-- Created by DbManage class, Power By yanue. 
-- http://yanue.net 
--
-- 生成日期: 2020 年  02 月 13 日 00:05
-- MySQL版本: 5.5.62-log
-- PHP 版本: 7.2.25

--
-- 数据库: ``
--

-- -------------------------------------------------------

--
-- 表的结构chzb_channels
--

DROP TABLE IF EXISTS `chzb_channels`;
CREATE TABLE `chzb_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(600) DEFAULT NULL,
  `category` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_channels
--

INSERT INTO `chzb_channels` VALUES('1','测试','http://127.0.0.1','默认频道');
INSERT INTO `chzb_channels` VALUES('2','测试','http://127.0.0.1','隐藏频道');
