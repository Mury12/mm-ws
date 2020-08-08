-- MySQL dump 10.17  Distrib 10.3.23-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: mm_dietacerta
-- ------------------------------------------------------
-- Server version	10.3.23-MariaDB-0+deb10u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dc_diet_food_registry`
--

DROP TABLE IF EXISTS `dc_diet_food_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_diet_food_registry` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `food_id` int(11) NOT NULL,
  `diet_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `fk_diet_food` (`diet_id`),
  KEY `fk_food_diet` (`food_id`),
  CONSTRAINT `fk_diet_food` FOREIGN KEY (`diet_id`) REFERENCES `dc_diet_registry` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_food_diet` FOREIGN KEY (`food_id`) REFERENCES `dc_food_registry` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_diet_food_registry`
--

LOCK TABLES `dc_diet_food_registry` WRITE;
/*!40000 ALTER TABLE `dc_diet_food_registry` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_diet_food_registry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_diet_registry`
--

DROP TABLE IF EXISTS `dc_diet_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_diet_registry` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `dayturn_time` time NOT NULL DEFAULT '00:00:01',
  `diet_calories` int(11) NOT NULL,
  `diet_carbohidrate` int(11) NOT NULL,
  `diet_fat` int(11) NOT NULL,
  `diet_protein` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `userID` int(11) NOT NULL,
  `templateID` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  KEY `fk_user_dietry` (`userID`),
  KEY `fk_diet_template` (`templateID`),
  CONSTRAINT `fk_diet_template` FOREIGN KEY (`templateID`) REFERENCES `dc_diet_templates` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_dietry` FOREIGN KEY (`userID`) REFERENCES `dc_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_diet_registry`
--

LOCK TABLES `dc_diet_registry` WRITE;
/*!40000 ALTER TABLE `dc_diet_registry` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_diet_registry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_diet_templates`
--

DROP TABLE IF EXISTS `dc_diet_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_diet_templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `base_kcal_kilo` double(10,2) NOT NULL,
  `base_protein_kilo` double(10,2) NOT NULL,
  `base_fat_kilo` double(10,2) NOT NULL,
  `base_carb_kilo` double(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `roleID` int(11) NOT NULL DEFAULT 5,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_user_id_template` (`created_by`),
  KEY `fk_role_id` (`roleID`),
  CONSTRAINT `fk_role_id` FOREIGN KEY (`roleID`) REFERENCES `dc_user_roles` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_id_template` FOREIGN KEY (`created_by`) REFERENCES `dc_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_diet_templates`
--

LOCK TABLES `dc_diet_templates` WRITE;
/*!40000 ALTER TABLE `dc_diet_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_diet_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_error_codes`
--

DROP TABLE IF EXISTS `dc_error_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_error_codes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(45) NOT NULL,
  `typeID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_error_type` (`typeID`),
  CONSTRAINT `fk_error_type` FOREIGN KEY (`typeID`) REFERENCES `dc_error_types` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_error_codes`
--

LOCK TABLES `dc_error_codes` WRITE;
/*!40000 ALTER TABLE `dc_error_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_error_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_food_registry`
--

DROP TABLE IF EXISTS `dc_food_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_food_registry` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `food_name` varchar(100) NOT NULL,
  `food_calories` int(11) NOT NULL,
  `food_carbohidrates` int(11) NOT NULL,
  `food_fat` int(11) NOT NULL,
  `food_sodium` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `food_ucode` varchar(100) DEFAULT NULL,
  `food_protein` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `food_name` (`food_name`,`food_ucode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_food_registry`
--

LOCK TABLES `dc_food_registry` WRITE;
/*!40000 ALTER TABLE `dc_food_registry` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_food_registry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_log_actions`
--

DROP TABLE IF EXISTS `dc_log_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_log_actions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(45) NOT NULL,
  `action_route` varchar(45) NOT NULL,
  `action_description` varchar(100) NOT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_log_actions`
--

LOCK TABLES `dc_log_actions` WRITE;
/*!40000 ALTER TABLE `dc_log_actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_log_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_role_domain`
--

DROP TABLE IF EXISTS `dc_role_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_role_domain` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_role_domain`
--

LOCK TABLES `dc_role_domain` WRITE;
/*!40000 ALTER TABLE `dc_role_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_role_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_sessions`
--

DROP TABLE IF EXISTS `dc_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_sessions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 0,
  `user_cur_ip` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `userID` (`userID`),
  UNIQUE KEY `session_token` (`session_token`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`userID`) REFERENCES `dc_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_sessions`
--

LOCK TABLES `dc_sessions` WRITE;
/*!40000 ALTER TABLE `dc_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_syslog`
--

DROP TABLE IF EXISTS `dc_syslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_syslog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `actionID` int(11) NOT NULL,
  `sessionID` int(11) DEFAULT NULL,
  `time` datetime NOT NULL,
  `user_ip` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_user_log` (`userID`),
  KEY `fk_log_action` (`actionID`),
  KEY `fk_log_session` (`sessionID`),
  CONSTRAINT `fk_log_action` FOREIGN KEY (`actionID`) REFERENCES `dc_log_actions` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_log_session` FOREIGN KEY (`sessionID`) REFERENCES `dc_sessions` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_log` FOREIGN KEY (`userID`) REFERENCES `dc_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_syslog`
--

LOCK TABLES `dc_syslog` WRITE;
/*!40000 ALTER TABLE `dc_syslog` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_syslog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_user_data`
--

DROP TABLE IF EXISTS `dc_user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_user_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `userID` (`userID`),
  KEY `fullname` (`fullname`) USING BTREE,
  CONSTRAINT `fk_user_data` FOREIGN KEY (`userID`) REFERENCES `dc_users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_user_data`
--

LOCK TABLES `dc_user_data` WRITE;
/*!40000 ALTER TABLE `dc_user_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_user_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_user_roles`
--

DROP TABLE IF EXISTS `dc_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_user_roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 10,
  `domainID` int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UQ_NAME_DOMAIN` (`name`,`domainID`),
  KEY `fk_domain_name` (`domainID`),
  CONSTRAINT `fk_domain_name` FOREIGN KEY (`domainID`) REFERENCES `dc_role_domain` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_user_roles`
--

LOCK TABLES `dc_user_roles` WRITE;
/*!40000 ALTER TABLE `dc_user_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dc_users`
--

DROP TABLE IF EXISTS `dc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dc_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(64) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `roleID` int(11) NOT NULL DEFAULT 10,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_user_role` (`roleID`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roleID`) REFERENCES `dc_user_roles` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_users`
--

LOCK TABLES `dc_users` WRITE;
/*!40000 ALTER TABLE `dc_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `view_foods_report`
--

DROP TABLE IF EXISTS `view_foods_report`;
/*!50001 DROP VIEW IF EXISTS `view_foods_report`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_foods_report` (
  `food_name` tinyint NOT NULL,
  `food_calories` tinyint NOT NULL,
  `food_carbohidrates` tinyint NOT NULL,
  `food_fat` tinyint NOT NULL,
  `food_protein` tinyint NOT NULL,
  `food_sodium` tinyint NOT NULL,
  `food_ucode` tinyint NOT NULL,
  `created_at` tinyint NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_user_activity`
--

DROP TABLE IF EXISTS `view_user_activity`;
/*!50001 DROP VIEW IF EXISTS `view_user_activity`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_user_activity` (
  `1` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_user_dietry`
--

DROP TABLE IF EXISTS `view_user_dietry`;
/*!50001 DROP VIEW IF EXISTS `view_user_dietry`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_user_dietry` (
  `username` tinyint NOT NULL,
  `diet_calories` tinyint NOT NULL,
  `diet_carbohidrate` tinyint NOT NULL,
  `diet_fat` tinyint NOT NULL,
  `diet_protein` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `created_at` tinyint NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_users_report`
--

DROP TABLE IF EXISTS `view_users_report`;
/*!50001 DROP VIEW IF EXISTS `view_users_report`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_users_report` (
  `username` tinyint NOT NULL,
  `fullname` tinyint NOT NULL,
  `email` tinyint NOT NULL,
  `created_at` tinyint NOT NULL,
  `last_login` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `level` tinyint NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'mm_dietacerta'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_create_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_create_login`(_username varchar(45), _userpass varchar(255)) RETURNS varchar(64) CHARSET utf8mb4
begin
		declare _userID int(1);
        declare _tok varchar(64);
        
        set _userID = (
			select ID from dc_users where username = _username AND userpass = SHA2(_userpass, 256)
        );
        
        IF _userID IS NOT NULL THEN
			set _tok = (
				select fn_create_session(_userID)
            );
            
            IF _tok IS NOT NULL THEN
				return _tok;
			ELSE 
				return 25010;      
			END IF;
		ELSE
			return 25002;
        END IF;
        
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_create_role` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_create_role`(_rolename varchar(45), _rolelevel int(2)) RETURNS int(11)
begin
		declare _roleID int;
        
        set _roleID = (
			SELECT roleID from dc_roles WHERE rolename = _rolename
        );
        
        IF _roleID IS NULL THEN
			INSERT INTO dc_roles (
				rolename, 
				`level`
            )
            VALUES (
				_rolename,
                _rolelevel
			);
			set _roleID = LAST_INSERT_ID();
        END IF;
        return _roleID;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_create_session` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_create_session`(_userID int(11)) RETURNS varchar(64) CHARSET utf8mb4
begin
		declare _tok varchar(64);
        declare _sessionID int(11);
        
        set _sessionID = (
			select ID from dc_sessions where userID = _userID
        );
        
        set _tok = SHA2( CONCAT(_userID, curdate()), 256 );
        
        IF _sessionID IS NOT NULL THEN
			UPDATE dc_sessions SET 
				token = _tok
			WHERE 
                ID = _sessionID;
		ELSE
			INSERT INTO dc_sessions (
				userID,
                token
			) 
            VALUES (
				_userID, 
                _tok
			);
        END IF;
        
        return _tok;
        
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_add_food` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_food`()
begin
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_add_log` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_log`(_userID int, _actionID int, _sessionID int, _userIP varchar(16))
begin
		INSERT INTO dc_syslog (userID, actionID, sessionID, user_ip) VALUES (_userID, _actionID, _sessionID, _userIP);
        select LAST_INSERT_ID() as log_id;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_add_process_to_role` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_process_to_role`(_rolename varchar(45), _processID varchar(45))
begin
			declare _roleID int;
            declare _relation_exists int;
            
            set _roleID = (
				SELECT ID from dc_roles WHERE rolename = _rolename
            );
            
            IF _roleID IS NOT NULL THEN
				set _relation_exists = (
					SELECT count(1) FROM  dc_roles_processes WHERE roleID = _roleID AND processID = _processID
                );
                IF _relation_exists > 0 THEN
					SELECT 'Processo já listado na Role especificada.'  as error_msg;
				ELSE 
					INSERT INTO dc_roles_processes (
						roleID, processID
                    ) 
                    VALUES (
						_roleID,
                        _processID
                    );
                    SELECT 'Relacionamento inserido com sucesso.' as error_msg;
				END IF;
            ELSE
				SELECT 'Role especificada não existe.' as error_msg;
            END IF;
        end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_user` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_user`(_username varchar(45), _userpass varchar(45), _rolename varchar(45))
begin
		declare _userID int;
        declare _roleID int;
        
        set _roleID = (
			select fn_create_role (_rolename)
        );
        
        set _userID = (
			SELECT ID FROM dc_users WHERE username = _username ORDER BY ID DESC LIMIT 1
        );
        
        IF _userID IS NOT NULL THEN
			CALL sp_get_error_msg(25004);
		ELSE
			INSERT INTO dc_users (
				username,
                userpass,
                roleID
            )
            VALUES (
				_username,
                SHA2(_userpass, 256),
                _roleID
            );
            set _userID = LAST_INSERT_ID();
            IF _userID <> 0 THEN
				select fn_create_session(_userID);
                SELECT _userID as userID;
            ELSE
				CALL sp_get_error_msg(25003);
            END IF;
        END IF;
        
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_diet_calculation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_diet_calculation`()
begin
	end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_error_msg` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_error_msg`(_errcode int)
begin
		SELECT err.`description`, t.`type` from dc_error_codes err JOIN dc_error_types WHERE err.error_code = _errcode LIMIT 1;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_foods` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_foods`()
begin
	end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_role_processes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_role_processes`(_tok varchar(64))
begin
		declare _roleID int;
        
        set _roleID = (
			SELECT wr.roleID from dc_sessions ses
				JOIN dc_users wu ON ses.userID
				JOIN dc_roles wr ON wu.roleID = wr.ID
			WHERE token = _tok
        );
        
        SELECT 
			wps.sessionID,
            wps.processName,
            wps.resourceName,
            wps.statusID,
            wps.end_date as enddatetime,
            wps.exception,
            wps.processID,
            wps.start_date as startdatetime
		FROM 
			dc_bp_sessions wps 
        JOIN 
			dc_roles_processes wrp ON wrp.roleID = _roleID
		WHERE 
			wrp.roleID = _roleID
		ORDER BY wps.start_date DESC LIMIT 50;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_role_templates` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_role_templates`()
begin
	end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_user_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_data`(_tok varchar(64))
begin
		SELECT wu.userID, wu.username, wr.rolename from dc_sessions ses
        JOIN dc_users wu ON ses.userID
        JOIN dc_roles wr ON wu.roleID = wr.ID
        WHERE token = _tok;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_user_diet` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_diet`()
begin
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_new_action` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_new_action`(_name varchar(45), _route varchar(45), _description varchar(100))
begin
		INSERT INTO dc_log_actions (action_name, action_route, action_description) VALUES(_name, _route, _description);
        SELECT LAST_INSERT_ID() as action_id;
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_new_diet` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_new_diet`()
begin
    
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_new_error` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_new_error`(_description varchar(100), _typeID int)
begin
	INSERT INTO dc_error_codes (`description`, typeID) VALUES (_description, typeID);
    SELECT LAST_INSERT_ID() as errcode;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_new_food` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_new_food`(_foodname varchar(45), _calories int, _carb int, _protein int, _fat int, _sodium int, _barcode varchar(100))
begin
    
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_new_template` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_new_template`()
begin
    end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_log` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_user_log`(_userID int, _username varchar(45))
begin
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `view_foods_report`
--

/*!50001 DROP TABLE IF EXISTS `view_foods_report`*/;
/*!50001 DROP VIEW IF EXISTS `view_foods_report`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_foods_report` AS select `dc_food_registry`.`food_name` AS `food_name`,`dc_food_registry`.`food_calories` AS `food_calories`,`dc_food_registry`.`food_carbohidrates` AS `food_carbohidrates`,`dc_food_registry`.`food_fat` AS `food_fat`,`dc_food_registry`.`food_protein` AS `food_protein`,`dc_food_registry`.`food_sodium` AS `food_sodium`,`dc_food_registry`.`food_ucode` AS `food_ucode`,`dc_food_registry`.`created_at` AS `created_at`,`dc_food_registry`.`status` AS `status` from `dc_food_registry` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_user_activity`
--

/*!50001 DROP TABLE IF EXISTS `view_user_activity`*/;
/*!50001 DROP VIEW IF EXISTS `view_user_activity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_user_activity` AS select 1 AS `1` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_user_dietry`
--

/*!50001 DROP TABLE IF EXISTS `view_user_dietry`*/;
/*!50001 DROP VIEW IF EXISTS `view_user_dietry`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_user_dietry` AS select `du`.`username` AS `username`,`dr`.`diet_calories` AS `diet_calories`,`dr`.`diet_carbohidrate` AS `diet_carbohidrate`,`dr`.`diet_fat` AS `diet_fat`,`dr`.`diet_protein` AS `diet_protein`,`dt`.`name` AS `name`,`dr`.`created_at` AS `created_at`,`dr`.`status` AS `status` from ((`dc_diet_registry` `dr` join `dc_diet_templates` `dt` on(`dr`.`templateID` = `dt`.`ID` or `dr`.`templateID` is null)) join `dc_users` `du` on(`dr`.`userID` = `du`.`ID`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_users_report`
--

/*!50001 DROP TABLE IF EXISTS `view_users_report`*/;
/*!50001 DROP VIEW IF EXISTS `view_users_report`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_users_report` AS select `u`.`username` AS `username`,`ud`.`fullname` AS `fullname`,`ud`.`email` AS `email`,`ud`.`created_at` AS `created_at`,`ud`.`last_login` AS `last_login`,`ur`.`name` AS `name`,`ur`.`level` AS `level`,`u`.`status` AS `status` from ((`dc_users` `u` join `dc_user_data` `ud` on(`ud`.`userID` = `u`.`ID`)) join `dc_user_roles` `ur` on(`u`.`roleID` = `ur`.`ID`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-07 23:18:47
