-- MySQL dump 10.13  Distrib 5.6.22, for osx10.8 (x86_64)
--
-- Host: localhost    Database: fayuan
-- ------------------------------------------------------
-- Server version	5.6.24-ndb-7.4.6-cluster-gpl

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `shixin`
--

DROP TABLE IF EXISTS `shixin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shixin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(45) DEFAULT NULL,
  `caseCode` varchar(45) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sexy` varchar(10) DEFAULT NULL,
  `cardNum` varchar(45) DEFAULT NULL,
  `businessEntity` varchar(45) DEFAULT NULL,
  `courtName` varchar(45) DEFAULT NULL,
  `areaName` varchar(45) DEFAULT NULL,
  `partyTypeName` varchar(45) DEFAULT NULL,
  `gistId` varchar(45) DEFAULT NULL,
  `regDate` varchar(45) DEFAULT NULL,
  `gistUnit` varchar(45) DEFAULT NULL,
  `duty` varchar(255) DEFAULT NULL,
  `performance` varchar(45) DEFAULT NULL,
  `disruptTypeName` varchar(45) DEFAULT NULL,
  `publishDate` varchar(45) DEFAULT NULL,
  `performedPart` varchar(45) DEFAULT NULL,
  `unperformPart` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7223 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-09-29 12:40:33
