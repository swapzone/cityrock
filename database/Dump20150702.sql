CREATE DATABASE  IF NOT EXISTS `cityrock` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `cityrock`;
-- MySQL dump 10.13  Distrib 5.6.22, for osx10.8 (x86_64)
--
-- Host: 192.168.178.100    Database: cityrock
-- ------------------------------------------------------
-- Server version	5.5.43-0+deb7u1

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
-- Table structure for table `availability`
--

DROP TABLE IF EXISTS `availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `interval` int(11) NOT NULL,
  PRIMARY KEY (`id`,`interval`),
  KEY `fk_availability_interval1_idx` (`interval`),
  CONSTRAINT `fk_availability_interval1` FOREIGN KEY (`interval`) REFERENCES `interval` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `availability`
--

LOCK TABLES `availability` WRITE;
/*!40000 ALTER TABLE `availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confirmation`
--

DROP TABLE IF EXISTS `confirmation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confirmation` (
  `id` int(11) NOT NULL,
  `registrant_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `activation_key` varchar(32) NOT NULL,
  `registration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_confirmation_registrant1_idx` (`registrant_id`),
  KEY `fk_confirmation_course1_idx` (`course_id`),
  CONSTRAINT `fk_confirmation_course1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_confirmation_registrant` FOREIGN KEY (`registrant_id`) REFERENCES `registrant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confirmation`
--

LOCK TABLES `confirmation` WRITE;
/*!40000 ALTER TABLE `confirmation` DISABLE KEYS */;
/*!40000 ALTER TABLE `confirmation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(6,2) DEFAULT NULL,
  `max_participants` int(3) NOT NULL DEFAULT '10',
  `min_staff` int(3) NOT NULL,
  `interval` tinyint(1) DEFAULT '0',
  `interval_value` varchar(45) DEFAULT NULL COMMENT 'e.g. every 2. (weekened)',
  `interval_designator` int(11) DEFAULT NULL,
  `interval_end` varchar(45) DEFAULT NULL COMMENT 'interval start is set by the reference to the "date" table',
  `interval_during_holidays` tinyint(1) DEFAULT NULL,
  `interval_restart_after_holidays` varchar(45) DEFAULT NULL,
  `course_type_id` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_course_interval1_idx` (`interval_designator`),
  KEY `fk_course_type_idx` (`course_type_id`),
  CONSTRAINT `fk_course_interval1` FOREIGN KEY (`interval_designator`) REFERENCES `interval` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_type` FOREIGN KEY (`course_type_id`) REFERENCES `course_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` VALUES (1,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(6,NULL,13,2,0,NULL,NULL,NULL,NULL,NULL,3),(7,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(8,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(9,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(10,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(12,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(13,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(14,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(15,NULL,12,2,0,NULL,NULL,NULL,NULL,NULL,3),(16,NULL,16,2,0,NULL,NULL,NULL,NULL,NULL,1),(17,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,1),(18,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,1),(19,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,1),(20,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,1),(21,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,2),(22,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,2),(23,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,2),(24,NULL,15,2,0,NULL,NULL,NULL,NULL,NULL,2);
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_has_registrant`
--

DROP TABLE IF EXISTS `course_has_registrant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_has_registrant` (
  `course_id` int(11) NOT NULL,
  `registrant_id` int(11) NOT NULL,
  `confirmed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`course_id`,`registrant_id`),
  KEY `fk_courses_has_registrants_registrants1_idx` (`registrant_id`),
  KEY `fk_courses_has_registrants_courses1_idx` (`course_id`),
  CONSTRAINT `fk_courses_has_registrants_courses1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_courses_has_registrants_registrants1` FOREIGN KEY (`registrant_id`) REFERENCES `registrant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_has_registrant`
--

LOCK TABLES `course_has_registrant` WRITE;
/*!40000 ALTER TABLE `course_has_registrant` DISABLE KEYS */;
INSERT INTO `course_has_registrant` VALUES (6,4,1),(16,6,1),(16,8,1),(16,10,1),(16,11,1),(16,12,1),(16,13,1),(16,14,1),(16,15,1),(16,16,1),(16,17,1),(16,18,1);
/*!40000 ALTER TABLE `course_has_registrant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_has_user`
--

DROP TABLE IF EXISTS `course_has_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_has_user` (
  `courses_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`courses_id`,`user_id`),
  KEY `fk_courses_has_staff_staff1_idx` (`user_id`),
  KEY `fk_courses_has_staff_courses1_idx` (`courses_id`),
  CONSTRAINT `fk_courses_has_staff_courses1` FOREIGN KEY (`courses_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_courses_has_staff_staff1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_has_user`
--

LOCK TABLES `course_has_user` WRITE;
/*!40000 ALTER TABLE `course_has_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_has_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_requires_qualification`
--

DROP TABLE IF EXISTS `course_requires_qualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_requires_qualification` (
  `course_id` int(11) NOT NULL,
  `qualification_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`qualification_id`),
  KEY `fk_course_has_qualification_qualification1_idx` (`qualification_id`),
  KEY `fk_course_has_qualification_course1_idx` (`course_id`),
  CONSTRAINT `fk_course_has_qualification_course1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_has_qualification_qualification1` FOREIGN KEY (`qualification_id`) REFERENCES `qualification` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_requires_qualification`
--

LOCK TABLES `course_requires_qualification` WRITE;
/*!40000 ALTER TABLE `course_requires_qualification` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_requires_qualification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_type`
--

DROP TABLE IF EXISTS `course_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_type` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_type`
--

LOCK TABLES `course_type` WRITE;
/*!40000 ALTER TABLE `course_type` DISABLE KEYS */;
INSERT INTO `course_type` VALUES (1,'Toprope',''),(2,'Vorstieg',NULL),(3,'Schnupper',NULL);
/*!40000 ALTER TABLE `course_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `date`
--

DROP TABLE IF EXISTS `date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'in minutes',
  `course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_date_course1_idx` (`course_id`),
  CONSTRAINT `fk_date_course1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `date`
--

LOCK TABLES `date` WRITE;
/*!40000 ALTER TABLE `date` DISABLE KEYS */;
INSERT INTO `date` VALUES (25,'2015-02-21 08:00:00',240,1),(26,'2015-03-21 08:00:00',240,7),(27,'2015-04-11 08:00:00',240,8),(28,'2015-05-09 08:00:00',240,9),(29,'2015-06-13 08:00:00',240,10),(31,'2015-07-11 08:00:00',240,12),(32,'2015-10-17 08:00:00',240,13),(33,'2015-11-14 08:00:00',240,14),(34,'2015-12-12 08:00:00',240,15),(43,'2015-02-14 12:00:00',240,17),(44,'2015-02-15 12:00:00',240,17),(45,'2015-03-14 08:00:00',240,18),(46,'2015-03-15 08:00:00',240,18),(47,'2015-04-18 12:00:00',240,19),(48,'2015-04-19 12:00:00',240,19),(53,'2015-05-09 12:00:00',240,20),(54,'2015-05-10 12:00:00',240,20),(55,'2015-01-31 12:00:00',240,21),(56,'2015-02-01 12:00:00',240,21),(59,'2015-04-11 12:00:00',240,22),(60,'2015-04-12 12:00:00',240,22),(61,'2015-07-04 12:00:00',240,23),(62,'2015-07-05 12:00:00',240,23),(65,'2015-11-07 12:00:00',240,24),(66,'2015-11-08 12:00:00',240,24),(67,'2015-01-10 08:00:00',240,6),(68,'2015-01-17 12:00:00',240,16),(69,'2015-01-18 12:00:00',240,16);
/*!40000 ALTER TABLE `date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `date_has_staff`
--

DROP TABLE IF EXISTS `date_has_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `date_has_staff` (
  `date_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`date_id`,`user_id`),
  KEY `fk_date_has_staff_staff1_idx` (`user_id`),
  KEY `fk_date_has_staff_date1_idx` (`date_id`),
  CONSTRAINT `fk_date_has_staff_date1` FOREIGN KEY (`date_id`) REFERENCES `date` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_date_has_staff_staff1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `date_has_staff`
--

LOCK TABLES `date_has_staff` WRITE;
/*!40000 ALTER TABLE `date_has_staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `date_has_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interval`
--

DROP TABLE IF EXISTS `interval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interval` (
  `id` int(11) NOT NULL,
  `description` varchar(45) NOT NULL COMMENT 'e.g. month, week, day, weekend, weekday',
  PRIMARY KEY (`id`),
  UNIQUE KEY `description_UNIQUE` (`description`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interval`
--

LOCK TABLES `interval` WRITE;
/*!40000 ALTER TABLE `interval` DISABLE KEYS */;
/*!40000 ALTER TABLE `interval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualification`
--

DROP TABLE IF EXISTS `qualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualification`
--

LOCK TABLES `qualification` WRITE;
/*!40000 ALTER TABLE `qualification` DISABLE KEYS */;
/*!40000 ALTER TABLE `qualification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrant`
--

DROP TABLE IF EXISTS `registrant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `street` varchar(60) DEFAULT NULL,
  `zip` int(6) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrant`
--

LOCK TABLES `registrant` WRITE;
/*!40000 ALTER TABLE `registrant` DISABLE KEYS */;
INSERT INTO `registrant` VALUES (4,'Vincent','Zerweck','Hasenbergstr. 17',70178,'Stuttgart','18.06.1990','vincent@webwerkstatt.de',NULL,NULL),(5,'Sascha','Gros','Katharinenstraße 20',73262,'Reichenbach an der Fils','17.08.1987','gros.sascha@gmail.com',NULL,NULL),(6,'Vincent','Zerweck','Hasenbergstr. 17',70178,'Stuttgart','18.06.1990','vincent@webwerkstatt.de',NULL,NULL),(8,'Michael','Höhn','Bromberger Straße 16',70374,'Stuttgart','11.11.1984','M.Hoehn@dymatrix.de',NULL,NULL),(10,'Kamlesh','Kshirsagar','Schneideräcker 51',70378,'Stuttgart','19.01.1982','info@cityrock.de',NULL,NULL),(11,'Christian','Schneider','Rotenwaldstr. 82',70197,'Stuttgart','16.07.1984','cschneider84@gmx.de',NULL,NULL),(12,'Myriam','Laux','Gutenbergstr. 9',70176,'Stuttgart','28.08.1987','info@cityrock.de',NULL,NULL),(13,'Melanie','Hartmann','Waiblinger Str. 19',701372,'Stuttgart','01.01.1900','info@cityrock.de',NULL,NULL),(14,'Denis','Gerber','Waiblinger Str. 19',70372,'Stuttgart','01.01.1900','info@cityrock.de',NULL,NULL),(15,'Simeon','Manz','Im Schafhof 2',73760,'Ostfildern','02.12.1987','info@cityrock.de',NULL,NULL),(16,'Csilla','Illichmann','Elisabethenstr. 35',70197,'Stuttgart','17.02.1983','info@cityrock.de',NULL,NULL),(17,'Matthias','Hugger','Römerstr. 73',70180,'Stuttgart','03.05.1982','info@cityrock.de',NULL,NULL),(18,'Luc','Lin','?',0,'Stuttgart','01.01.1900','info@cityrock.de',NULL,NULL);
/*!40000 ALTER TABLE `registrant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `description` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Administrator',''),(2,'Mitarbeiter','');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `deletable` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'vincent','891a54c4e2eab52d01c6fbf85a4c143e','Vincent','Zerweck','01785487687',1,0),(2,'sascha','891a54c4e2eab52d01c6fbf85a4c143e','Sascha ','Gros','01788171987',0,1),(4,'Rainer','82fccb1a8e4f96fc402c053895f47fb7',NULL,NULL,NULL,1,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_availability`
--

DROP TABLE IF EXISTS `user_has_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_availability` (
  `user_id` int(11) NOT NULL,
  `availability_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`availability_id`),
  KEY `fk_staff_has_availability_availability1_idx` (`availability_id`),
  KEY `fk_staff_has_availability_staff1_idx` (`user_id`),
  CONSTRAINT `fk_staff_has_availability_availability1` FOREIGN KEY (`availability_id`) REFERENCES `availability` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_staff_has_availability_staff1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_availability`
--

LOCK TABLES `user_has_availability` WRITE;
/*!40000 ALTER TABLE `user_has_availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_has_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_qualification`
--

DROP TABLE IF EXISTS `user_has_qualification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_qualification` (
  `user_id` int(11) NOT NULL,
  `qualification_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`qualification_id`),
  KEY `fk_staff_has_qualification_qualification1_idx` (`qualification_id`),
  KEY `fk_staff_has_qualification_staff1_idx` (`user_id`),
  CONSTRAINT `fk_staff_has_qualification_qualification1` FOREIGN KEY (`qualification_id`) REFERENCES `qualification` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_staff_has_qualification_staff1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_qualification`
--

LOCK TABLES `user_has_qualification` WRITE;
/*!40000 ALTER TABLE `user_has_qualification` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_has_qualification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_role`
--

DROP TABLE IF EXISTS `user_has_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_user_has_role_role1_idx` (`role_id`),
  KEY `fk_user_has_role_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_has_role_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_role_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_role`
--

LOCK TABLES `user_has_role` WRITE;
/*!40000 ALTER TABLE `user_has_role` DISABLE KEYS */;
INSERT INTO `user_has_role` VALUES (1,1),(2,1),(4,1);
/*!40000 ALTER TABLE `user_has_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-02 17:58:03
