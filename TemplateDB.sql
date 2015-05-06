-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: TemplateDB
-- ------------------------------------------------------
-- Server version	5.5.43-0ubuntu0.14.04.1

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
-- Table structure for table `Ballots`
--

DROP TABLE IF EXISTS `Ballots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ballots` (
  `ResultID` int(11) NOT NULL,
  `ElimLevel` int(11) NOT NULL,
  `Round` int(11) NOT NULL,
  `Judge` int(11) NOT NULL,
  `Rank` int(11) NOT NULL,
  `Qual` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ballots`
--

LOCK TABLES `Ballots` WRITE;
/*!40000 ALTER TABLE `Ballots` DISABLE KEYS */;
/*!40000 ALTER TABLE `Ballots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Events` (
  `EventID` int(11) NOT NULL AUTO_INCREMENT,
  `EventName` varchar(50) NOT NULL,
  `Partner` tinyint(1) NOT NULL,
  `EventAbbr` varchar(10) NOT NULL,
  PRIMARY KEY (`EventID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Events`
--

LOCK TABLES `Events` WRITE;
/*!40000 ALTER TABLE `Events` DISABLE KEYS */;
INSERT INTO `Events` VALUES (1,'International Extemp',0,'IX'),(2,'Domestic Extemp',0,'DX'),(3,'Extemp (Novice)',0,'NovX'),(4,'Original Oration',0,'OO'),(5,'Original Oration (Novice)',0,'NovOO'),(6,'Informative',0,'Info'),(7,'Prose',0,'Pr'),(8,'Poetry',0,'Po'),(9,'Humorous Interpretation',0,'HI'),(10,'Dramatic Interpretation',0,'DI'),(11,'Duo/Duet',1,'Duo'),(12,'Duo/Duet (Novice)',1,'NovDuo'),(13,'Improvised Duet Acting',1,'IDA'),(14,'Improvised Duet Acting (Novice)',1,'Nov IDA'),(15,'Impromptu',0,'Imp'),(16,'Impromptu (Novice)',0,'NovImp'),(17,'Dramatic Interpretation (Novice)',0,'NovDI'),(18,'Informative (Novice)',0,'NovInfo'),(19,'Prose (Novice)',0,'NovPr'),(20,'Poetry (Novice)',0,'NovPo'),(21,'Humorous Interpretation (Novice)',0,'NovHI');
/*!40000 ALTER TABLE `Events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Results`
--

DROP TABLE IF EXISTS `Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Results` (
  `ResultID` int(11) NOT NULL AUTO_INCREMENT,
  `StudentID` int(11) NOT NULL,
  `PartnerID` int(11) DEFAULT NULL,
  `EventID` int(11) NOT NULL,
  `TournamentID` int(11) NOT NULL,
  `broke` tinyint(1) NOT NULL,
  `State` tinyint(1) NOT NULL,
  `place` int(11) DEFAULT NULL,
  PRIMARY KEY (`ResultID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Results`
--

LOCK TABLES `Results` WRITE;
/*!40000 ALTER TABLE `Results` DISABLE KEYS */;
/*!40000 ALTER TABLE `Results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Seasons`
--

DROP TABLE IF EXISTS `Seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Seasons` (
  `SeasonID` int(11) NOT NULL AUTO_INCREMENT,
  `StartYear` int(11) NOT NULL,
  `SeasonName` varchar(150) NOT NULL,
  PRIMARY KEY (`SeasonID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Seasons`
--

LOCK TABLES `Seasons` WRITE;
/*!40000 ALTER TABLE `Seasons` DISABLE KEYS */;
/*!40000 ALTER TABLE `Seasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Students`
--

DROP TABLE IF EXISTS `Students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Students` (
  `LName` varchar(50) NOT NULL,
  `FName` varchar(50) NOT NULL,
  `StudentID` int(11) NOT NULL AUTO_INCREMENT,
  `NoviceYear` int(11) NOT NULL,
  PRIMARY KEY (`StudentID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Students`
--

LOCK TABLES `Students` WRITE;
/*!40000 ALTER TABLE `Students` DISABLE KEYS */;
/*!40000 ALTER TABLE `Students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tournaments`
--

DROP TABLE IF EXISTS `Tournaments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tournaments` (
  `TournamentID` int(11) NOT NULL AUTO_INCREMENT,
  `TournamentName` varchar(50) NOT NULL,
  `NumRounds` int(11) NOT NULL,
  `NumJudges` int(11) NOT NULL,
  `NumElimRounds` int(11) NOT NULL,
  `NumElimJudges` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Season` int(11) NOT NULL,
  PRIMARY KEY (`TournamentID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tournaments`
--

LOCK TABLES `Tournaments` WRITE;
/*!40000 ALTER TABLE `Tournaments` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tournaments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `UName` varchar(30) NOT NULL,
  `FName` varchar(70) NOT NULL,
  `LName` varchar(70) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `cookieExp` int(11) DEFAULT NULL,
  `cookie` varchar(255) DEFAULT NULL,
  `CanMod` int(11) NOT NULL,
  `EmailConf` tinyint(1) NOT NULL,
  `EmailCode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
INSERT INTO `Users` VALUES ('admin','William','Klausmeyer','admin@forensicsdb.com','$2y$10$i2PxUObvxbFnyq7UPJgHtuGqD1eAPhQabV908rAtVa39TN8iRpbD.',NULL,NULL,1,1,'2015-05-02');
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
