<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>Devices</h1>
		</div>
	</div>
	<div id="mainContent">
		<div class="break"></div>
		<p>
			On the devices page you can add, remove and edit devices. All devices are list in a table also, and you can browse, search and sort the table.
		</p>
		<div class="break"></div>
		<p>
			Please see the table below for details on adding a device
		</p>
	<div class="break" style="clear:both;"></div>
		<div id="tableDivHelp">
		<h4 class="helpH4">Device Details</h4>
			<table class="helpTbl" align="left" border="0" cellspacing="0" cellpadding="0" width="50%">
				<thead>
					<tr align="left" valign="top">
						<th>Item</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr align="left" valign="top">
						<td id="leftcell">Device Name</td>
						<td>Device name of the device you want to add</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">IP Address</td>
						<td>IP address of the device you want to add. You can click <a href="">resolve device name</a> hyperlink to automatically complete the <span class="rconfigNameStyle">Device Name</span> field, but your DNS must be configured correctly on the server to be able to resolve the IP address to a name</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Prompt</td>
						<td>The <span class="rconfigNameStyle">rConfig</span> Telnet/SSH scripts required the actual device privileged exec mode prompt for fast execution of the script. Please input the exec mode prompt include the <b>#</b> symbol for the device. <br/>i.e.  <b>"router1#" </b></td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Vendor</td>
						<td>Please select a vendor from the select list. This is auto-populated from the <a href="3_vendors.php">vendors</a> page</td>
					</tr>					
					<tr align="left" valign="top">
						<td id="leftcell">Model</td>
						<td>The model field is a free text field with autocomplete, and the values are stored in the database for easy retrieval later. A single character entered into this field will display the current values displayed in the database beginning with the entered character. This is to ensure a consistency in naming conventions for device models. <br/> i.e. <b>ASA5510</b> or <b>C3750x</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	<div class="break" style="clear:both;"></div>
		<div id="tableDivHelp">
		<h4 class="helpH4">Other Details</h4>
			<table class="helpTbl" align="left" border="0" cellspacing="0" cellpadding="0" width="50%">
				<thead>
					<tr align="left" valign="top">
						<th>Item</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr align="left" valign="top">
						<td id="leftcell">Category</td>
						<td>Please select a category form the select list. This is auto-populated from the <a href="3_categories.php">Categories</a> page.</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Custom Properties</td>
						<td>
							Under the Custom Properties section, a number of text input fields will be display based on the list of items in the <a href="3_customprop.php">Custom Properties</a> page. This can be populated as free text
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	<div class="break" style="clear:both;"></div>
		<div id="tableDivHelp">
		<h4 class="helpH4"> Credentials</h4>
			<table class="helpTbl" align="left" border="0" cellspacing="0" cellpadding="0" width="50%">
				<thead>
					<tr align="left" valign="top">
						<th>Item</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr align="left" valign="top">
						<td id="leftcell">Username</td>
						<td>Enter the devices login Username. By clicking the <a href="#">default username/password</a> check box, you will auto populate the Username, Password, Confirm Password, and Enable Password fields with the credentials saved in  the settings page
						</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Password/ Confirm Password</td>
						<td>Enter the devices login Password</td>
					</tr>				
					<tr align="left" valign="top">
						<td id="leftcell">Enable Mode</td>
						<td>If you need to access privileged EXEC mode to run show commands, and this is done by typing 'enable' at the user prompt ('>'), then click to select this checkbox</td>
					</tr>		
					<tr align="left" valign="top">
						<td id="leftcell">Enable Password</td>
						<td>Enter the devices Enable Password if required</td>
					</tr>
					<tr align="left" valign="top">
						<td id="leftcell">Access Method</td>
						<td>Enter the devices access method. Choose Telnet or SSHv2</td>
					</tr>		
					<tr align="left" valign="top">
						<td id="leftcell">Connection Port</td>
						<td>In some situations, the connection port for the Access Method will have changed. If so, you may manually change it here. This field changes automatically when choosing the Access Method. </td>
					</tr>
				</tbody>
			</table>
		</div>
	<div class="break" style="clear:both;"></div>
		<p>
		The device table contains a list of all devices. You may sort, search, and paginate through the pages of devices. There is no limit to the number of devices that can be saved. Click on a device name will bring you to the <a href="3_devicemgmt.php">Device Management</a> page for that device.
		</p>
		<div class="break"></div>
	</div>
</body>
</html>