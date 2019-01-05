/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.48-log : Database - webim
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`webim` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `webim`;

/*Table structure for table `tp_member` */

DROP TABLE IF EXISTS `tp_member`;

CREATE TABLE `tp_member` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  `password` varchar(50) COLLATE utf8_bin NOT NULL,
  `email` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `phone_mob` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `head_image` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `room_id` tinyint(2) DEFAULT '1' COMMENT '房间号',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `tp_member` */

insert  into `tp_member`(`user_id`,`user_name`,`password`,`email`,`phone_mob`,`head_image`,`room_id`) values (1,'czx','aa123456',NULL,NULL,'/static/img/1.jpg',1),(2,'czx2','aa123456',NULL,NULL,'/static/img/2.jpg',1);

/*Table structure for table `tp_message` */

DROP TABLE IF EXISTS `tp_message`;

CREATE TABLE `tp_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `head_image` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `content` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `time` int(11) DEFAULT NULL COMMENT '时间戳',
  `type` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '消息类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `tp_message` */

insert  into `tp_message`(`id`,`user_name`,`user_id`,`head_image`,`content`,`time`,`type`) values (25,'czx',1,'/static/img/1.jpg','<p>wo1hah<br></p>',1546488193,'say'),(26,'czx2',2,'/static/img/2.jpg','<p>你在逗我<br></p>',1546607807,'say'),(27,'czx',1,'/static/img/1.jpg','<p>???<br></p>',1546607813,'say'),(29,'czx2',2,'/static/img/2.jpg','你好呀',1546615697,'say'),(30,'czx',1,'/static/img/1.jpg','/uploads/20190104/dbce43d8c982a6d9e2ab209c5f0f7200.jpg',1546615703,'image');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
