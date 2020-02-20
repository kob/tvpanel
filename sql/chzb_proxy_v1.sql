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
-- 表的结构chzb_proxy
--

DROP TABLE IF EXISTS `chzb_proxy`;
CREATE TABLE `chzb_proxy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(500) NOT NULL,
  `proxy` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_proxy
--

INSERT INTO `chzb_proxy` VALUES('1','eJzLKCkpsNLXLy8v18ssKCmzsLDQK8kv0DcyMLQ0NDAw1QcAtMcJyA==','eJwrzi+w0tcHAAfeAes=');
