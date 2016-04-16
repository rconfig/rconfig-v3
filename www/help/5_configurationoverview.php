<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>Configuration Overview</h1>
		</div>
	</div>
	<div id="mainContent">
		<p>
			<b>Device Configuration Statistics</b><br/>
		</p>

		<div id="tableDivHelp">
			<table class="helpTbl" align="left" border="0" cellspacing="0" cellpadding="0" width="50%">
				<thead>
					<tr align="left" valign="top">
						<th>Item</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr align="left" valign="top">
						<td id="leftcell">Total Categories</td>
						<td>Number of configured Categories</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Total Devices</td>
						<td>Total number of configuration Devices in <span class="rconfigNameStyle">rConfig</span></td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell"> Total Configuration Files </td>
						<td>Total number of downloaded configuration files in <span class="rconfigNameStyle">rConfig</span></td>
					</tr>				
					<tr align="left" valign="top">
						<td id="leftcell">  Purge Configuration Files </td>
						<td>Allows you to purge all device configuration files older than X days</td>
					</tr>
				</tbody>
			</table>
		</div>
	<div class="break" style="clear:both;"></div>
	<div class="break" style="clear:both;"></div>

		<p>
			<b>Connection Log</b><br/>
		The connection log displays device connections success or failures. It will also output the commands run on a paticular device. <b>Note, for telnet to devices, a single connection is logged with multiple commands run subsequently. For SSHv2, each command requires a new connection. This is due to a known issue with some manufacturers SSH implementions.</b>
			
		</p>
		<div class="break"></div>

	</div>
</body>
</html>
