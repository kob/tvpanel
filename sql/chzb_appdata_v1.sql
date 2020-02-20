--
-- MySQL database dump
-- Created by DbManage class, Power By yanue. 
-- http://yanue.net 
--
-- 生成日期: 2020 年  02 月 19 日 20:37
-- MySQL版本: 5.5.62-log
-- PHP 版本: 7.2.25

--
-- 数据库: ``
--

-- -------------------------------------------------------

--
-- 表的结构chzb_appdata
--

DROP TABLE IF EXISTS `chzb_appdata`;
CREATE TABLE `chzb_appdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataver` int(11) NOT NULL,
  `appver` varchar(16) NOT NULL,
  `setver` int(11) NOT NULL DEFAULT '0',
  `dataurl` varchar(255) NOT NULL,
  `appurl` varchar(255) NOT NULL,
  `adtext` varchar(1024) NOT NULL,
  `showtime` int(11) NOT NULL,
  `showinterval` int(11) NOT NULL,
  `needauthor` int(11) NOT NULL DEFAULT '1',
  `splashbj` varchar(255) NOT NULL,
  `decoder` int(11) NOT NULL DEFAULT '0',
  `buffTimeOut` int(11) NOT NULL DEFAULT '10',
  `tipusernoreg` varchar(100) NOT NULL,
  `tipuserexpired` varchar(100) NOT NULL,
  `tipuserforbidden` varchar(100) NOT NULL,
  `tiploading` varchar(100) NOT NULL,
  `ipcount` int(11) NOT NULL DEFAULT '5',
  `trialdays` int(11) DEFAULT NULL,
  `qqinfo` varchar(255) DEFAULT NULL,
  `autoupdate` int(11) DEFAULT '1',
  `randkey` varchar(100) DEFAULT '827ccb0eea8a706c4c34a16891f84e7b',
  `updateinterval` int(11) DEFAULT '15',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 chzb_appdata
--

INSERT INTO `chzb_appdata` VALUES('1','2','2.0','1','http://tv.luo2888.cn/data.php','http://download.luo2888.cn/software/%E8%82%A5%E7%B1%B3TV/%E8%82%A5%E7%B1%B3TV_V2.0.apk','','30','100','1','http://tv.luo2888.cn/images/tv.png','1','30','请输入授权码登录，或联系公众号客服@luo2888的工作室。','账号已到期，请联系公众号客服@luo2888的工作室续费。','账号已禁用，请联系公众号客服@luo2888的工作室。','正在连接，请稍后 ...','2','365','欢迎关注微信公众号@luo2888的工作室','1','827ccb0eea8a706c4c34a16891f84e7b','10');
