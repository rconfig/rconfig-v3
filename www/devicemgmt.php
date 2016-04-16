<?php require("../classes/db.class.php"); ?>
<?php include("includes/head.inc.php"); ?>
<body>
	<!-- Headwrap Include -->    
	<?php include("includes/masthead.inc.php"); ?>
	
<div id="mainwrap">
	<!-- TopNav Include -->    
	<?php include("includes/topnav.inc.php"); ?>
	
	<?php 
	/* Custom Devices Custom Form Functions */
	require_once("lib/crud/devices.frm.func.php");
	?>
	<div id="main">
	<!-- Breadcrumb Include -->    
	<?php include("includes/breadcrumb.inc.php"); ?>
		
	<!-- Announcement Include -->    
	<?php include("includes/announcement.inc.php"); ?>
			<div id="content">
			<?php
				/* Instantiate DB Class */
				$db     = new db();
				$q      = "SELECT 
							n.id,
							n.deviceName,
							n.deviceIpAddr,
							n.connPort,
							v.vendorName vendorName,
							n.model,
							cat.categoryName categoryName
						FROM nodes n
						LEFT OUTER JOIN vendors v ON n.vendorId = v.id
						LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
						LEFT OUTER JOIN devicesaccessmethod a ON n.deviceAccessMethodId = a.id
						LEFT OUTER JOIN categories cat ON n.nodeCatId = cat.id
						WHERE n.status = 1
						AND n.id = " . $_GET['deviceId'];
				$result = $db->q($q);

				//$result = $database->query($q);
				$items = array();
				while ($row = mysql_fetch_assoc($result)) {
					$items = $row;
				}
				// set VARs
				$deviceName   = $items['deviceName'];
				$deviceIpAddr = $items['deviceIpAddr'];
				$connPort = $items['connPort'];
				$vendorName   = $items['vendorName'];
				$model        = $items['model'];
				$categoryName = $items['categoryName'];
			?>

					<a name="top"></a> 
				
					<fieldset id="dashboardFieldset" style="width:35%; min-height:147px; float:left;">
						<legend>Device Details</legend>
						<div id="deviceSummaryDiv">
						    <table id="deviceSummaryTbl" cellspacing="0" cellpadding="0">
							  <tr>
								<td><b>Device Name:</b></td>
								<td class="infoCell"><?php echo $deviceName;?></td>
							  </tr>
							  <tr>
								<td><b>IP Address:</b></td>
								<td class="infoCell"><?php echo $deviceIpAddr;?></td>
							  </tr>
							  <tr>
								<td><b>Vendor:</b></td>
								<td class="infoCell"><?php echo $vendorName;?></td>
							  </tr>
							  <tr>
								<td><b>Model:</b></td>
								<td class="infoCell"><?php echo $model;?></td>
							  </tr>							 
							  <tr>
								<td><b>Category:</b></td>
								<td class="infoCell"><?php echo $categoryName;?></td>
							  </tr>							 
							  <tr>
								<td><b>Status:</b></td>
								<td class="infoCell">
									<div id="hostStatus">
										<span id="pleaseWait" style="display:none">Please wait... <img width="12" height="12" src='images/ajax_loader.gif' alt='Please wait... '/></span>
									</div>
								</td>
							  </tr>
							</table>
						</div>
						<div id="#deviceActionsDiv">
						<?php
							/* Instantiate DB Class */
							//$db     = new db();
							$q      = $db->q("SELECT useDefaultCredsManualSet FROM settings WHERE id = '1'");
							$result = mysql_fetch_assoc($q);
							$useDefaultCredsManualSet = $result['useDefaultCredsManualSet'];

							if ($useDefaultCredsManualSet == '1'){
								echo '<button id="hideAll" onclick="manualDownloadCreds(\''.$_GET['deviceId'].'\')" tabindex="8" class="smlButton" style="margin-right:5px;margin-top:5px;" title="Begin a manual download of configurations for this device">Manual Download</button>';
								echo '<button id="hideAll" onclick="configDeviceCreds(\''.$_GET['deviceId'].'\')" tabindex="8" class="smlButton" style="margin-right:5px;margin-top:5px;" title="Run a configuration snippet on this device">Run Config Snippet</button>';
							}
							else{
								echo '<button id="hideAll" onclick="manualDownload(\''.$_GET['deviceId'].'\')" tabindex="8" class="smlButton" style="margin-right:5px;margin-top:5px;" title="Begin a manual download of configurations for this device">Manual Download</button>';
								echo '<button id="hideAll" onclick="configDevice(\''.$_GET['deviceId'].'\')" tabindex="8" class="smlButton" style="margin-right:5px;margin-top:5px;" title="Run a configuration snippet on this device">Run Config Snippet</button>';
							}
						?>
						</div>
						
						
						
					</fieldset>
					
					<fieldset id="dashboardFieldset" style="width:50%; min-height:147px; float:left;">
							<legend>Device Configurations</legend>
							<button id="expandAll" onclick="expandAll()" tabindex="7" class="smlButton">Show All</button> 
							<button id="hideAll" onclick="hideAll()" tabindex="8" class="smlButton">Close All</button> 
							<?php 						
							// For documentation and updates, visit http://abeautifulsite.net/notebook.php?article=21

							// Main function file
							include("../classes/php_file_tree.php");
							
							echo php_file_tree("/home/rconfig/data/".$categoryName."/".$deviceName, "onclick=javascript:openFile('[link]');"); ?>
						<div id ="bottomButtons">
							<button id="expandAll" onclick="expandAll()"  class="smlButton">Show All</button> 
							<button id="hideAll" onclick="hideAll()"  class="smlButton">Close All</button> 
							<button onClick="parent.location='#top'" class="smlButton">Top of Page</button> 
						</div>	
						</fieldset>

			</div><!-- End Content -->
		<div style="clear:both;"></div>
	</div><!-- End Main -->
				
<!-- JS script Include -->
<script type="text/JavaScript" src="js/devicemgmt.js"></script> 

<script>
// var passed to onload function to load device status
var deviceIpAddr = "<?php echo $deviceIpAddr ;?>";
var connPort = "<?php echo $connPort ;?>";
</script>
<!-- Footer Include -->    
<?php include("includes/footer.inc.php"); ?>

</div> <!-- End Mainwrap -->
</body>
</html>