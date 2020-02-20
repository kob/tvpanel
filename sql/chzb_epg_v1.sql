--
-- MySQL database dump
-- Created by DbManage class, Power By yanue. 
-- http://yanue.net 
--
-- 生成日期: 2020 年  02 月 20 日 21:40
-- MySQL版本: 5.5.62-log
-- PHP 版本: 7.2.25

--
-- 数据库: ``
--

-- -------------------------------------------------------

--
-- 表的结构chzb_epg
--

DROP TABLE IF EXISTS `chzb_epg`;
CREATE TABLE `chzb_epg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `content` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `beizhu` varchar(100) DEFAULT NULL,
  `epg` varchar(100) DEFAULT NULL,
  `bdpd` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_epg
--

INSERT INTO `chzb_epg` VALUES('1','cntv-cctv1','CCTV-1','1','','','');
