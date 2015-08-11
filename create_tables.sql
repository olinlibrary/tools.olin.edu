-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: tools
-- ------------------------------------------------------
-- Server version	5.1.73

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
-- Table structure for table `docs`
--

DROP TABLE IF EXISTS `docs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external` int(1) NOT NULL,
  `url` varchar(512) NOT NULL,
  `displayname` varchar(256) NOT NULL,
  `tools_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_docs` (`tools_id`),
  CONSTRAINT `fk_docs` FOREIGN KEY (`tools_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `error_log`
--

DROP TABLE IF EXISTS `error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `error_log` (
  `error_text` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tools_id` int(11) NOT NULL,
  `url` varchar(256) NOT NULL,
  `thumb` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tools_id` (`tools_id`,`thumb`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`tools_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(256) NOT NULL,
  `hours` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`displayname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations_tools`
--

DROP TABLE IF EXISTS `locations_tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locations_id` int(11) NOT NULL,
  `tools_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_id` (`locations_id`,`tools_id`),
  KEY `fk_loc_tool_tool` (`tools_id`),
  CONSTRAINT `fk_loc_tool_loc` FOREIGN KEY (`locations_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_loc_tool_tool` FOREIGN KEY (`tools_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations_tools`
--

LOCK TABLES `locations_tools` WRITE;
/*!40000 ALTER TABLE `locations_tools` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations_tools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `toolgroups`
--

DROP TABLE IF EXISTS `toolgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(256) NOT NULL,
  `sort_priority` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `displayname` (`displayname`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `toolgroups`
--

LOCK TABLES `toolgroups` WRITE;
/*!40000 ALTER TABLE `toolgroups` DISABLE KEYS */;
INSERT INTO `toolgroups` VALUES (1,'Green Machines',999),(2,'Machining',998),(3,'Digital Prototyping',997),(4,'Welding',2),(5,'Project Building',2),(6,'Advanced Wood',1),(7,'Advanced Fabrication',1),(8,'Instrumentation',1);
/*!40000 ALTER TABLE `toolgroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tools`
--

DROP TABLE IF EXISTS `tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `displayname` varchar(256) NOT NULL,
  `notes` text NOT NULL,
  `training_levels` text NOT NULL,
  `toolgroups_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_toolgroups` (`toolgroups_id`),
  CONSTRAINT `fk_toolgroups` FOREIGN KEY (`toolgroups_id`) REFERENCES `toolgroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tools`
--

LOCK TABLES `tools` WRITE;
/*!40000 ALTER TABLE `tools` DISABLE KEYS */;
INSERT INTO `tools` VALUES (2,'beltsander','Belt Sander','Good for Material Removal','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',1),(3,'drillpress','Drill Press','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',1),(4,'mill','Mill','','[[\"M\",\"Manual\"],[\"C\",\"2-Axis CNC\"],[\"T\",\"Instructor\"]]',2),(5,'bandsaw','Vertical Band Saw','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',1),(6,'horizontalbandsaw','Horizontal Band Saw','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',2),(7,'sheetmetal','Sheet Metal','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',1),(8,'lathe','Lathe','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',2),(9,'coldsaw','Cold Saw','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',2),(10,'3dprinter','3D Printer','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',3),(11,'lasercutter','Laser Cutter','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',3),(12,'spotwelder','Spot Welder','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',4),(13,'mig','MIG','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',4),(14,'tig','TIG','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',4),(15,'oxyacetylene','Oxy-Acetylene','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',4),(16,'sandblaster','Sand Blaster','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',4),(17,'waterjet','Waterjet','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',7),(18,'hydraulicsheetmetal','Hydraulic Sheet Metal','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',7),(19,'chopsaw','Chop Saw','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',6),(20,'shopbot','Shopbot CNC Router','More information on the [Shopbot Wiki](http://tools.olin.edu/wiki/shopbot).','[[\"1\",\"Level 1\"],[\"2\",\"Level 2\"],[\"T\",\"Instructor\"]]',6),(21,'projectbuilding','Project Building','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',5),(22,'composites','Composites','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',5),(24,'panelsaw','Panel Saw','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',6),(25,'spraybooth','Spray Booth','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',5),(26,'vinylcutter','Vinyl Cutter','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',3),(27,'mocap','Motion Capture Studio','','[[\"\",\"Trained\"],[\"T\",\"Instructor\"]]',8);
/*!40000 ALTER TABLE `tools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainings`
--

DROP TABLE IF EXISTS `trainings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(1) NOT NULL,
  `tools_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `timestamp` int(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level` (`level`,`tools_id`,`users_id`),
  KEY `fk_tools` (`tools_id`),
  KEY `fk_users` (`users_id`),
  KEY `fk_instructor` (`instructor_id`),
  CONSTRAINT `fk_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_tools` FOREIGN KEY (`tools_id`) REFERENCES `tools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4571 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `guid` varchar(256) NOT NULL,
  `username` varchar(256) NOT NULL,
  `usergroup` int(4) DEFAULT NULL,
  `displayname` varchar(256) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1811 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-10 20:31:54
