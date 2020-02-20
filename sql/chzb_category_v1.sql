--
-- MySQL database dump
-- Created by DbManage class, Power By yanue. 
-- http://yanue.net 
--
-- 生成日期: 2020 年  02 月 19 日 20:05
-- MySQL版本: 5.5.62-log
-- PHP 版本: 7.2.25

--
-- 数据库: ``
--

-- -------------------------------------------------------

--
-- 表的结构chzb_category
--

DROP TABLE IF EXISTS `chzb_category`;
CREATE TABLE `chzb_category` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `psw` varchar(16) DEFAULT '',
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_category
--

INSERT INTO `chzb_category` VALUES('1','默认频道','1','');
INSERT INTO `chzb_category` VALUES('2','隐藏频道','1','12345');
