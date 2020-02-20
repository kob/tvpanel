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
-- 表的结构chzb_admin
--

DROP TABLE IF EXISTS `chzb_admin`;
CREATE TABLE `chzb_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `psw` varchar(16) NOT NULL,
  `showcounts` int(11) NOT NULL DEFAULT '100',
  `author1` tinyint(4) NOT NULL DEFAULT '0',
  `author2` tinyint(4) NOT NULL DEFAULT '0',
  `useradmin` tinyint(4) NOT NULL DEFAULT '0',
  `channeladmin` tinyint(4) NOT NULL DEFAULT '0',
  `ipcheck` tinyint(11) NOT NULL DEFAULT '0',
  `unbind` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_admin
--

INSERT INTO `chzb_admin` VALUES('1','admin','admin','20','1','1','1','1','1','0');
