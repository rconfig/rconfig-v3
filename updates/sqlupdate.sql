-- Dumping structure for table DATABASE_NAME.nodes
ALTER TABLE settings
ADD COLUMN `pageTimeout` int(1) NOT NULL DEFAULT '600' COMMENT 'Page Timeout Value';

ALTER TABLE configs
ADD COLUMN `configTime` time DEFAULT NULL AFTER configDate;

-- Dumping structure for table DATABASE_NAME.menuPages
DROP TABLE IF EXISTS `menuPages`;
CREATE TABLE IF NOT EXISTS `menuPages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(50) NOT NULL DEFAULT '0',
  `breadcrumbText` varchar(100) NOT NULL DEFAULT '0',
  `annoucementText` varchar(100) NOT NULL DEFAULT '0',
  `menuName` varchar(100) NOT NULL DEFAULT '0',
  `topLevel` varchar(100) NOT NULL DEFAULT '0',
  `parentId` int(11) NOT NULL DEFAULT '0',
  `menuSortId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table DATABASE_NAME.menuPages: 24 rows
DELETE FROM `menuPages`;
/*!40000 ALTER TABLE `menuPages` DISABLE KEYS */;
INSERT INTO `menuPages` (`id`, `pageName`, `breadcrumbText`, `annoucementText`, `menuName`, `topLevel`, `parentId`, `menuSortId`) VALUES
	(1, 'login.php', 'Login Page', 'Enter Username & Password to login', 'Login', '2', 1, 0),
	(2, 'compatibility.php', 'Compatibility Page', 'rConfig Compatibility Details', 'Compatibility', '2', 0, 0),
	(3, 'dashboard.php', 'Dashboard', 'View rConfig Server and Device Status on this page', 'Home', '1', 3, 1),
	(4, 'devices.php', 'Devices', 'View/Edit Devices on this page', 'Devices', '1', 4, 2),
	(5, 'devicemgmt.php', 'Devices > Device Management', 'Manage devices on this page', 'Device Management', '0', 5, 0),
	(6, 'customProperties.php', 'Devices > Custom Properties', 'Update Custom Properties on this page', 'Custom Properties', '0', 4, 0),
	(7, 'categories.php', 'Devices > Categories', 'Update Categories on this page', 'Categories', '0', 4, 0),
	(8, 'commands.php', 'Devices > Commands', 'Update Commands on this page', 'Commands', '0', 4, 0),
	(9, 'vendors.php', 'Devices > Vendors', 'Update Vendor details on this page', 'Vendors', '0', 4, 0),
	(10, 'configoverview.php', 'Configuration Tools > Overview', 'Configurations Overview', 'Configuration Tools', '1', 10, 4),
	(11, 'configcompare.php', 'Configuration Tools > Comparison', 'Configurations Comparison', 'Compare', '0', 10, 0),
	(12, 'search.php', 'Configuration Tools > Search', 'Search Configurations', 'Config Search', '0', 10, 0),
	(13, 'snippets.php', 'Configuration Tools > Config Snippets', 'Configuration Snippets', 'Config Snippets', '0', 10, 0),
	(14, 'configreports.php', 'Configuration Tools > Reports', 'Reports', 'Reports', '0', 10, 0),
	(15, 'configlogging.php', 'Configuration Tools > Logging Information', 'Logging files and archives', 'Logs', '0', 10, 0),
	(16, 'complianceoverview.php', 'Compliance > Overview', 'Configuration Compliance Management Overview', 'Compliance', '1', 16, 5),
	(17, 'compliancereports.php', 'Compliance > Reports', 'Configuration Compliance Reports', 'Reports', '0', 16, 0),
	(18, 'compliancepolicies.php', 'Compliance > Policies', 'Configuration Compliance Policies', 'Policies', '0', 16, 0),
	(19, 'compliancepolicyelements.php', 'Compliance > Policy Elements', 'Configuration Compliance Policy Elements', 'Policy Elements', '0', 16, 0),
	(20, 'settings.php', 'Settings > General Settings', 'Change general systems settings on this page', 'Settings', '1', 20, 6),
	(21, 'scheduler.php', 'Scheduled Tasks', 'Manage Scheduled Tasks on this page', 'Scheduled Tasks', '1', 21, 3),
	(22, 'useradmin.php', 'Settings > Users Management', 'Manage User details on this page', 'Users (Admin)', '0', 20, 0),
	(23, 'settingsBackup.php', 'Settings > Backup', 'Backup rConfig on this page', 'System Backup(Admin)', '0', 20, 0),
	(24, 'updater.php', 'Update', 'Update rConfig on this page', 'Updater', '2', 24, 0);
/*!40000 ALTER TABLE `menuPages` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;