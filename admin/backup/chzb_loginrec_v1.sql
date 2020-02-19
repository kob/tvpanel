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
-- 表的结构chzb_loginrec
--

DROP TABLE IF EXISTS `chzb_loginrec`;
CREATE TABLE `chzb_loginrec` (
  `userid` bigint(15) NOT NULL,
  `deviceid` varchar(32) NOT NULL,
  `mac` varchar(32) NOT NULL,
  `model` varchar(32) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `region` varchar(32) NOT NULL,
  `logintime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
