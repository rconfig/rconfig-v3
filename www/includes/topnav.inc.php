<?php 
	$host = gethostname();
	$ip = $_SERVER['SERVER_ADDR'];
/* DASHBOARD */	
?>

<?php
if($config_page == "dashboard.php") /* check if is dashboard and only display top menu*/{?>
<div id="navwrap">
	<div id="nav">
		<ul>
			<li><a href="dashboard.php" class="active">Home</a></li>
			<li><a href="devices.php">Devices</a></li>
			<li><a href="scheduler.php">Scheduled Tasks</a></li>
			<li><a href="configoverview.php">Configuration Tools</a></li>
			<li><a href="complianceoverview.php">Compliance</a></li>
			<li><a href="templategen.php">Templates</a></li>
			<li><a href="settings.php">Settings</a></li>
		</ul>
		<div id="navtitle"><?php echo $host.":". $ip ?></div>
	</div>
</div>
<?php 
/* DASHBOARD */	

/* DEVICES */	

} else { /* otherwise display both top and bottom Navs */ 

/* Switch check for Devices and subpages */
	switch ($config_page){
		case "devices.php":
		case "categories.php";
		case "customProperties.php";
		case "commands.php";
		case "vendors.php";
		case "devicemgmt.php";
?>
<div id="navwrap">
	<div id="nav">
		<ul>
			<li><a href="dashboard.php" >Home</a></li>
			<li><a href="devices.php" 
				<?php if($config_page == "devices.php" ||					
					$config_page == "devicemgmt.php" || 
					$config_page == "categories.php" || 
					$config_page == "customProperties.php" || 
					$config_page == "commands.php" || 
					$config_page == "vendors.php") {
					echo "class='active'";
					}?>
					>Devices</a></li>
			<li><a href="scheduler.php">Scheduled Tasks</a></li>
			<li><a href="configoverview.php">Configuration Tools</a></li>
			<li><a href="complianceoverview.php">Compliance</a></li>
			<li><a href="settings.php">Settings</a></li>
		</ul>
		<div id="navtitle"><?php echo $host.":". $ip ?></div>
	</div>
	<div id="nav2">
		<ul>
			<li <?php if($config_page == "devices.php" || $config_page == "devicemgmt.php") {echo "class='nav2Selected'";}?>><a href="devices.php">Devices</a></li>
			<li <?php if($config_page == "categories.php") {echo "class='nav2Selected'";}?>><a href="categories.php">Categories</a></li>
			<li <?php if($config_page == "customProperties.php") {echo "class='nav2Selected'";}?>><a href="customProperties.php">Custom Properties</a></li>			
			<li <?php if($config_page == "commands.php") {echo "class='nav2Selected'";}?>><a href="commands.php">Commands</a></li>
			<li <?php if($config_page == "vendors.php") {echo "class='nav2Selected'";}?>><a href="vendors.php">Vendors</a></li>
		</ul>
	</div>
</div>
<?php 
			break; /* End Switch check for Devices and subpages */
/* DEVICES */


/* SCHEDULER */

		case "scheduler.php":
		
	?>
	<div id="navwrap">
		<div id="nav">
			<ul>
				<li><a href="dashboard.php">Home</a></li>
				<li><a href="devices.php">Devices</a></li>
				<li><a href="scheduler.php"  class="active">Scheduled Tasks</a></li>
				<li><a href="configoverview.php">Configuration Tools</a></li>
				<li><a href="complianceoverview.php">Compliance</a></li>
				<li><a href="settings.php">Settings</a></li>
			</ul>
			<div id="navtitle"><?php echo $host.":". $ip ?></div>
		</div>
	</div>
		
	<?php 
		break; /* End scheduler.php check */

/* SCHEDULER */

/* CONFIG TOOLS */

/* Switch check for Config Overview and subpages */
		case "configoverview.php":
		case "configcompare.php":
		case "search.php";
		case "snippets.php";
		case "configreports.php";
		case "configlogging.php";
		
?>
<div id="navwrap">
	<div id="nav">
		<ul>
			<li><a href="dashboard.php" >Home</a></li>
			<li><a href="devices.php">Devices</a></li>
			<li><a href="scheduler.php">Scheduled Tasks</a></li>
			<li><a href="configoverview.php" 
				<?php if($config_page == "configoverview.php" ||					
					$config_page == "configcompare.php" || 
					$config_page == "search.php" || 
					$config_page == "snippets.php" || 
					$config_page == "configreports.php" || 
					$config_page == "configlogging.php") {
					echo "class='active'";
					}?>
					>Configuration Tools</a></li>
			<li><a href="complianceoverview.php">Compliance</a></li>
			<li><a href="settings.php">Settings</a></li>
		</ul>
		<div id="navtitle"><?php echo $host.":". $ip ?></div>
	</div>
	<div id="nav2">
		<ul>
			<li <?php if($config_page == "configoverview.php") {echo "class='nav2Selected'";}?>><a href="configoverview.php">Config Overview</a></li>
			<li <?php if($config_page == "configcompare.php") {echo "class='nav2Selected'";}?>><a href="configcompare.php">Compare</a></li>
			<li <?php if($config_page == "search.php") {echo "class='nav2Selected'";}?>><a href="search.php">Config Search</a></li>			
			<li <?php if($config_page == "snippets.php") {echo "class='nav2Selected'";}?>><a href="snippets.php">Config Snippets</a></li>			
			<li <?php if($config_page == "configreports.php") {echo "class='nav2Selected'";}?>><a href="configreports.php">Reports</a></li>			
			<li <?php if($config_page == "configlogging.php") {echo "class='nav2Selected'";}?>><a href="configlogging.php">Logs</a></li>			
		</ul>
	</div>
</div>
<?php 
			break; /* End Switch check for Devices and subpages */
/* CONFIG TOOLS */

/* REPORTS */

	case "complianceoverview.php";
	case "compliancereports.php";
	case "compliancepolicies.php";
	case "compliancepolicyelements.php";
	?>
<div id="navwrap">
	<div id="nav">
		<ul>
			<li><a href="dashboard.php">Home</a></li>
			<li><a href="devices.php">Devices</a></li>
			<li><a href="scheduler.php">Scheduled Tasks</a></li>
			<li><a href="configoverview.php">Configuration Tools</a></li>
			<li><a href="complianceoverview.php" class="active">Compliance</a></li>
			<li><a href="settings.php">Settings</a></li>
		</ul>
		<div id="navtitle"><?php echo $host.":". $ip ?></div>
	</div>
	<div id="nav2">
		<ul>
			<li <?php if($config_page == "complianceoverview.php") {echo "class='nav2Selected'";}?>><a href="complianceoverview.php">Overview</a></li>
			<li <?php if($config_page == "compliancereports.php") {echo "class='nav2Selected'";}?>><a href="compliancereports.php">Reports</a></li>			
			<li <?php if($config_page == "compliancepolicies.php") {echo "class='nav2Selected'";}?>><a href="compliancepolicies.php">Policies</a></li>			
			<li <?php if($config_page == "compliancepolicyelements.php") {echo "class='nav2Selected'";}?>><a href="compliancepolicyelements.php">Policy Elements</a></li>			
		</ul>
	</div>
</div>
<?php 
/* SETTINGS */

                        break; /* End Switch check for Devices and subpages */

/* REPORTS */


/* SETTINGS */
		/* Check for Settings.php and subpages */
		case "settings.php":
		case "updater.php":
		case "settingsServer.php";
		case "settingsDevices.php";
		case "settingsEmail.php";
		case "settingsDb.php";
		case "useradmin.php";
		case "settingsBackup.php";

?>
<div id="navwrap">
	<div id="nav">
		<ul>
			<li><a href="dashboard.php" >Home</a></li>
			<li><a href="devices.php" >Devices</a></li>
			<li><a href="scheduler.php">Scheduled Tasks</a></li>
			<li><a href="configoverview.php">Configuration Tools</a></li>
			<li><a href="complianceoverview.php">Compliance</a></li>
			<li><a href="templategen.php">Templates</a></li>
			<li><a href="settings.php" 
				<?php if($config_page == "settings.php" || 
				$config_page == "useradmin.php" || 
				$config_page == "updater.php" || 
				$config_page == "settingsBackup.php") {
				echo "class='active'";
				}?>>Settings</a></li>
		</ul>
		<div id="navtitle"><?php echo $host.":". $ip ?></div>
	</div>
	<div id="nav2">
		<ul>
			<li <?php if($config_page == "settings.php") {echo "class='nav2Selected'";}?>><a href="settings.php">Main Settings</a></li>
			<?php
			// check if logged in user is admin and display next lines
				if($session->isAdmin()){ ?>
			<li <?php if($config_page == "useradmin.php") {echo "class='nav2Selected'";}?>><a href="useradmin.php">Users (Admin)</a></li>
			<?php } // End check if logged in user is admin
			?>
			<?php
			// check if logged in user is admin and display next lines
				if($session->isAdmin()){ ?>
			<li <?php if($config_page == "settingsBackup.php") {echo "class='nav2Selected'";}?>><a href="settingsBackup.php">System Backup (Admin)</a></li>
			<?php } // End check if logged in user is admin
			?> 			
		</ul>
	</div>
</div>
<?php
                        break; /* End Switch check for Devices and subpages */
/* TEMPLATE TOOLS */

                        break;
                                default:
                                echo "Page Title Not Found";
                        }
} ?>
