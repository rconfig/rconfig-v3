<div id="announcement">
	<p class="notification loginInformation">
		<?php
			switch ($config_page){
				case "login.php":
					echo "Enter Username & Password to login";
					break;
				case "compatibility.php":
					echo "rConfig Compatibility Details";
					break;
				case "dashboard.php":
					echo "View rConfig Server and Device Status on this page";
					break;
				/* Devices Subpages */
				case "devices.php":
					echo "View/Edit Devices on this page";
					break;
				case "devicemgmt.php":
					echo "Manage device <strong>".$_GET['device']."</strong>on this page";
					break;
				case "customProperties.php":
					echo "Update Custom Properties on this page";
					break;
				case "categories.php":
					echo "Update Categories on this page";
					break;
				case "commands.php":
					echo "Update Commands on this page";
					break;
				case "vendors.php":
					echo "Update Vendor details on this page";
					break;
				/* Scheduler Subpages */
				case "scheduler.php":
					echo "Manage Scheduled Tasks on this page";
					break;  
				/* Config Tools Subpages */
				case "configoverview.php":
					echo "Configurations Overview";
					break;
				case "configcompare.php":
					echo "Configurations Comparison";
					break;  
				case "search.php":
					echo "Search Configurations";
					break;												  
				case "snippets.php":
					echo "Configuration Snippets";
					break;  
				case "configreports.php":
					echo "Reports";
					break;  
				case "configlogging.php":
					echo "Logging files and archives";
					break;		  
				/* Reports Subpages */
				case "complianceoverview.php":
					echo "Configuration Compliance Management Overview";
					break;												  
				case "compliancereports.php":
					echo "Configuration Compliance Reports";
					break;												  
				case "compliancepolicies.php":
					echo "Configuration Compliance Policies";
					break;												  
				case "compliancepolicyelements.php":
					echo "Configuration Compliance Policy Elements";
					break;
				/* Template Subpages */
				case "templategen.php":
					echo "Config Template Generator";
					break;
				case "templategenconfig.php":
					echo "New Template-Based Generated Configs";
					break;							  
				/* Settings Subpages */
				case "settings.php":
					echo "Change general systems settings on this page";
					break;												  
				case "useradmin.php":
					echo "Manage User details on this page";
					break;								  
				case "settingsBackup.php":
					echo "Backup rConfig on this page";
					break;
				case "updater.php":
					echo "Update rConfig on this page";
					break;
				default:
					echo "Page Title Not Found";
			}
		?>
	</p>
</div>
