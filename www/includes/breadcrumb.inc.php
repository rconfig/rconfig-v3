<div id="breadcrumb">
		<h2>
			<?php
			switch ($config_page){
				case "login.php":
				echo "Login Page";
					break;
				case "compatibility.php":
				echo "Compatibility Page";
					break;
				case "dashboard.php":
				echo "Dashboard";
					break;
				/* Devices Subpages */
					case "devices.php":
					echo "Devices";
						break;
					case "devicemgmt.php":
					echo "Devices > Device Management";
						break;
					case "customProperties.php":
					echo "Devices > Custom Properties";
						break;
					case "categories.php":
					echo "Devices > Categories";
						break;
					case "commands.php":
					echo "Devices > Commands";
						break;
					case "vendors.php":
					echo "Devices > Vendors";
						break;
				/* Config Tools Subpages */
					case "configoverview.php":
					echo "Configuration Tools > Overview";
						break;
					case "configcompare.php":
					echo "Configuration Tools > Comparison";
						break;					
					case "search.php":
					echo "Configuration Tools > Search";
						break;						
					case "snippets.php":
					echo "Configuration Tools > Config Snippets";
						break;		
					case "configreports.php":
					echo "Configuration Tools > Reports";
						break;	
					case "configlogging.php":
					echo "Configuration Tools > Logging Information";
						break;		
				/* Compliance Subpages */
					case "complianceoverview.php":
					echo "Compliance > Overview";
						break;		
					case "compliancereports.php":
					echo "Compliance > Reports";
						break;	
					case "compliancepolicies.php":
					echo "Compliance > Policies";
						break;	
					case "compliancepolicyelements.php":
					echo "Compliance > Policy Elements";
						break;		
				/* Template Subpages */
					case "templategen.php":
					echo "Template > Template Generator";
						break;
					case "templategenconfig.php":
					echo "Template > Generated Configs";
						break;					
				/* Settings Subpages */
					case "settings.php": 
					echo "Settings > General Settings";
						break;
					case "scheduler.php":
					echo "Scheduled Tasks";
						break;
					case "useradmin.php":
					echo "Settings > Users Management";
						break;
					case "settingsBackup.php":
					echo "Settings > Backup";
						break;
					case "updater.php":
					echo "Update";
						break;
				default:
			echo "<font color=\"red\">Page Title Not Found</font>";			
			}
			?>
			<!-- <img id="helpIcon" src="images/helpIcon16.png" alt="Click for Help!" title="Click for Help!"/> -->
		</h2>
	</div>
