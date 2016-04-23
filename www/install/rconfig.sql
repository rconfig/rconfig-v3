-- --------------------------------------------------------
-- Database:                     rConfig Database Template
-- Written By:               	 rConfig Design Team
-- Server OS:                    Linux / Centos
-- Developed on:               	 5.5.17 - MySQL Community Server (GPL) by Remi
-- Date/time:                    2012-09-06 12:21:47
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for DATABASE_NAME
CREATE DATABASE IF NOT EXISTS `DATABASE_NAME` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `DATABASE_NAME`;


-- Dumping structure for table DATABASE_NAME.active_guests
CREATE TABLE IF NOT EXISTS `active_guests` (
  `ip` varchar(15) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping structure for table DATABASE_NAME.active_users
CREATE TABLE IF NOT EXISTS `active_users` (
  `username` varchar(30) NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) DEFAULT '0',
  `status` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.categories: ~5 rows (approximately)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `categoryName`, `status`) VALUES
	(1, 'Switches', 1),
	(2, 'Routers', 1),
	(4, 'LoadBalancers', 1),
	(5, 'WANOptimizers', 1),
	(8, 'Firewalls', 1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;


-- Dumping structure for table DATABASE_NAME.cmdCatTbl
CREATE TABLE IF NOT EXISTS `cmdCatTbl` (
  `configCmdId` int(10) DEFAULT NULL,
  `nodeCatId` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping data for table DATABASE_NAME.cmdCatTbl: ~15 rows (approximately)
/*!40000 ALTER TABLE `cmdCatTbl` DISABLE KEYS */;
INSERT INTO `cmdCatTbl` (`configCmdId`, `nodeCatId`) VALUES
	(160, 1),
	(160, 2),
	(160, 4),
	(160, 5),
	(160, 8),
	(161, 1),
	(161, 2),
	(161, 4),
	(161, 5),
	(161, 8),
	(163, 1),
	(163, 2),
	(164, 1),
	(164, 2),
	(165, 2);
/*!40000 ALTER TABLE `cmdCatTbl` ENABLE KEYS */;

-- Dumping structure for table DATABASE_NAME.compliancePolElem
CREATE TABLE IF NOT EXISTS `compliancePolElem` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `elementName` varchar(255) NOT NULL,
  `elementDesc` varchar(255) NOT NULL,
  `singleParam1` int(10) DEFAULT NULL COMMENT '1, equals. 2, contains',
  `singleLine1` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Dumping structure for table DATABASE_NAME.compliancePolElemTbl
CREATE TABLE IF NOT EXISTS `compliancePolElemTbl` (
  `polId` int(10) DEFAULT NULL,
  `elemId` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.compliancePolicies
CREATE TABLE IF NOT EXISTS `compliancePolicies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `policyName` varchar(255) DEFAULT NULL,
  `policyDesc` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.complianceReportPolTbl
CREATE TABLE IF NOT EXISTS `complianceReportPolTbl` (
  `reportId` int(10) DEFAULT NULL,
  `polId` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;


-- Dumping structure for table DATABASE_NAME.complianceReports
CREATE TABLE IF NOT EXISTS `complianceReports` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reportsName` varchar(255) DEFAULT NULL,
  `reportsDesc` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.configcommands
CREATE TABLE IF NOT EXISTS `configcommands` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `command` varchar(255) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.configcommands: ~6 rows (approximately)
/*!40000 ALTER TABLE `configcommands` DISABLE KEYS */;
INSERT INTO `configcommands` (`id`, `command`, `status`) VALUES
	(160, 'show running-config', 1),
	(161, 'show startup-config', 1),
	(162, 'show ip route', 2),
	(163, 'show cdp neigh', 1),
	(164, 'show ip access-list', 1),
	(165, 'show ip route', 1);
/*!40000 ALTER TABLE `configcommands` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

-- Dumping structure for table DATABASE_NAME.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `deviceId` int(10) DEFAULT NULL,
  `configLocation` varchar(255) DEFAULT NULL,
  `configFilename` varchar(255) DEFAULT NULL,
  `configDate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;



-- Dumping structure for view DATABASE_NAME.customProperties
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `customProperties` (
	`customProperty` VARCHAR(64) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view DATABASE_NAME.devicemodelview
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `devicemodelview` (
	`model` VARCHAR(255) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci'
) ENGINE=MyISAM;


-- Dumping structure for table DATABASE_NAME.devicesaccessmethod
CREATE TABLE IF NOT EXISTS `devicesaccessmethod` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `devicesAccessMethod` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.devicesaccessmethod: ~2 rows (approximately)
/*!40000 ALTER TABLE `devicesaccessmethod` DISABLE KEYS */;
INSERT INTO `devicesaccessmethod` (`id`, `devicesAccessMethod`) VALUES
	(1, 'Telnet'),
	(3, 'SSHv2');
/*!40000 ALTER TABLE `devicesaccessmethod` ENABLE KEYS */;


-- Dumping structure for table DATABASE_NAME.nodes
CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `deviceName` varchar(255) DEFAULT NULL,
  `deviceUsername` varchar(255) DEFAULT NULL,
  `devicePassword` varchar(255) DEFAULT NULL,
  `deviceEnableMode` varchar(255) DEFAULT NULL,
  `deviceEnablePassword` varchar(255) DEFAULT NULL,
  `deviceAccessMethodId` varchar(100) DEFAULT NULL,
  `deviceIpAddr` varchar(255) DEFAULT NULL,
  `devicePrompt` varchar(255) DEFAULT NULL,
  `nodeCatId` int(10) DEFAULT NULL,
  `vendorId` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `termLength` int(2) DEFAULT NULL,
  `nodeVersion` varchar(255) DEFAULT NULL,
  `nodeAddedBy` varchar(255) DEFAULT '-',
  `defaultCreds` int(1) DEFAULT NULL,
  `defaultUsername` varchar(255) DEFAULT NULL,
  `defaultPassword` varchar(255) DEFAULT NULL,
  `defaultEnablePassword` varchar(255) DEFAULT NULL,
  `deviceDateAdded` date DEFAULT NULL,
  `deviceLastUpdated` date DEFAULT NULL,
  `connPort` int(2) DEFAULT NULL,
  `status` int(10) DEFAULT '1',
   `custom_Location` varchar(255) DEFAULT NULL COMMENT 'Custom Property - Location',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.reportData
CREATE TABLE IF NOT EXISTS `reportData` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `device` varchar(255) DEFAULT NULL,
  `error` longtext,
  `script` varchar(50) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `fileSaveChk` int(10) DEFAULT NULL,
  `fileLocation` varchar(255) DEFAULT NULL,
  `defaultNodeUsername` varchar(255) DEFAULT NULL,
  `defaultNodePassword` varchar(255) DEFAULT NULL,
  `defaultNodeEnable` varchar(255) DEFAULT NULL,
  `useDefaultCredsManualSet` int(1) DEFAULT NULL,
  `commandDebug` int(10) DEFAULT '0' COMMENT '0 is default where 1 is debug on',
  `commandDebugLocation` varchar(255) DEFAULT NULL,
  `phpErrorLogging` int(2) DEFAULT '0',
  `phpErrorLoggingLocation` varchar(255) DEFAULT '/home/rconfig/logs/phpLog/',
  `deviceConnectionTimout` int(3) DEFAULT '10',
  `smtpServerAddr` varchar(255) DEFAULT NULL,
  `smtpFromAddr` varchar(255) DEFAULT NULL,
  `smtpRecipientAddr` longtext,
  `smtpAuth` tinyint(2) DEFAULT NULL,
  `smtpAuthUser` varchar(100) DEFAULT NULL,
  `smtpAuthPass` varchar(100) DEFAULT NULL,
  `smtpLastTest` varchar(20) DEFAULT NULL,
  `smtpLastTestTime` datetime DEFAULT NULL,
  `timeZone` VARCHAR(100) DEFAULT NULL,
  `ldapServer` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.settings: ~1 rows (approximately)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `fileSaveChk`, `fileLocation`, `defaultNodeUsername`, `defaultNodePassword`, `defaultNodeEnable`, `commandDebug`, `commandDebugLocation`, `phpErrorLogging`, `phpErrorLoggingLocation`, `deviceConnectionTimout`, `smtpServerAddr`, `smtpFromAddr`, `smtpRecipientAddr`, `smtpAuth`, `smtpAuthUser`, `smtpAuthPass`, `smtpLastTest`, `smtpLastTestTime`) VALUES
	(1, 1, '/home/rconfig/data/', '', '', '', 0, '/home/rconfig/logs/debugging/', 0, '/home/rconfig/logs/phpLog/', 60, '', '', '', 1, '', '', '', '');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Dumping structure for table rconfigdev1.snippets
CREATE TABLE IF NOT EXISTS `snippets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snippetName` varchar(255) NOT NULL,
  `snippetDesc` varchar(255) NOT NULL,
  `snippet` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping structure for table DATABASE_NAME.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(6) NOT NULL,
  `taskType` int(3) NOT NULL,
  `taskname` varchar(255) NOT NULL,
  `taskDescription` varchar(255) NOT NULL,
  `snipId` int(10) DEFAULT NULL,
  `crontime` varchar(255) NOT NULL COMMENT 'e.g. 5 * * 6 *',
  `croncmd` varchar(255) NOT NULL COMMENT 'e.g. "php /script/script.php"',
  `addedBy` varchar(255) NOT NULL COMMENT 'for later use',
  `dateAdded` date NOT NULL,
  `catId` varchar(255) DEFAULT NULL COMMENT 'Used for Compare Reports Only',
  `catCommand` varchar(255) DEFAULT NULL COMMENT 'Used for Compare Reports Only',
  `status` int(2) NOT NULL COMMENT 'if 2 = deleted and not in crontab',
  `mailConnectionReport` int(10) DEFAULT '0',
  `mailErrorsOnly` int(10) DEFAULT '0',
  `complianceId` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping structure for table DATABASE_NAME.userlevels
CREATE TABLE IF NOT EXISTS `userlevels` (
  `id` int(10) NOT NULL,
  `userlevel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.userlevels: ~2 rows (approximately)
/*!40000 ALTER TABLE `userlevels` DISABLE KEYS */;
INSERT INTO `userlevels` (`id`, `userlevel`) VALUES
	(1, 'User'),
	(9, 'Administrator');
/*!40000 ALTER TABLE `userlevels` ENABLE KEYS */;


-- Dumping structure for table DATABASE_NAME.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `userid` varchar(32) DEFAULT NULL,
  `userlevel` tinyint(1) unsigned NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `status` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.users: ~3 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `userid`, `userlevel`, `email`, `timestamp`, `status`) VALUES
	(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '6c97424dc92f14ae78f8cc13cd08308d', 9, 'admin@domain.com', 1346920339, 1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


-- Dumping structure for table DATABASE_NAME.vendors
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vendorName` varchar(50) DEFAULT '0',
  `vendorLogo` varchar(255) NOT NULL DEFAULT 'images/logos/Coding16.png',
  `status` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.vendors: ~3 rows (approximately)
/*!40000 ALTER TABLE `vendors` DISABLE KEYS */;
INSERT INTO `vendors` (`id`, `vendorName`, `vendorLogo`, `status`) VALUES
	(1, 'Cisco', ' images/vendor/cisco.jpg', 1);
/*!40000 ALTER TABLE `vendors` ENABLE KEYS */;

-- Dumping structure for view DATABASE_NAME.customProperties
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `customProperties`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customProperties` AS 
SELECT column_name AS customProperty
FROM information_schema.columns
WHERE table_name = 'nodes'
AND column_name LIKE'%custom%';


-- Dumping structure for view DATABASE_NAME.devicemodelview
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `devicemodelview`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `devicemodelview` AS 
SELECT DISTINCT `nodes`.`model` AS `model` 
FROM `nodes` WHERE ((`nodes`.`model` <> 'NULL') 
AND (`nodes`.`model` <> ''));
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;


-- Dumping structure for table DATABASE_NAME.generatedConfigs
-- CREATE TABLE `generatedConfigs` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `configName` varchar(50) NOT NULL,
--   `templateName` varchar(50) NOT NULL,
--   `configDesc` varchar(50) NOT NULL,
--   `linkedId` int(11) NOT NULL,
--   `newConfig` mediumtext NOT NULL,
--   `configLocation` varchar(100) NOT NULL,
--   `configFilename` varchar(100) NOT NULL,
--   `configDate` date NOT NULL,
--   `status` int(1) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;