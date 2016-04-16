-- Dumping structure for table DATABASE_NAME.nodes
ALTER TABLE settings
ADD COLUMN `useDefaultCredsManualSet` int(1) DEFAULT NULL AFTER defaultNodeEnable,
ADD COLUMN `ldapServer` varchar(100) DEFAULT NULL AFTER `timeZone`;

-- Table structure for table `configtemplates`

CREATE TABLE IF NOT EXISTS `configtemplates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateName` varchar(50) NOT NULL,
  `templateDesc` varchar(50) NOT NULL,
  `template` mediumtext NOT NULL,
  `templateVars` mediumtext NOT NULL,
  `templateVarSyms` mediumtext NOT NULL,
  `templateVarSubs` mediumtext NOT NULL,
  `newTemplate` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Table structure for table `generatedConfigs`
--

CREATE TABLE IF NOT EXISTS `generatedConfigs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configName` varchar(50) NOT NULL,
  `templateName` varchar(50) NOT NULL,
  `configDesc` varchar(50) NOT NULL,
  `linkedId` int(11) NOT NULL,
  `newConfig` mediumtext NOT NULL,
  `configLocation` varchar(100) NOT NULL,
  `configFilename` varchar(100) NOT NULL,
  `configDate` date NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1