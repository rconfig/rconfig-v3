<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>General Settings </h1>
		</div>
	</div>
	<div id="mainContent">
		<div class="break"></div>
		<p>
			The General Settings page contains some generic rConfig system settings and information
			</p>
			<h4 class="helpH4">Server Details</h4>
			<p>
				This section contains some basic server resource information such as Memory, CPU and hard disk space.
				Also, you can set the time zone on this page. It is important that you do this, as some backend scripts require this setting to be accurate.<br /><br />
			</p>
			<h4 class="helpH4">Device Settings</h4>
			<p>
				This sections has settings and information relating to devices.
				The first three username/ password fields are used to auto-populate the username/password fields for the device add form on the Device page.<br /><br />
				Manual up/download credentials section can be set to Yes to force users to manually enter in their username and password for logging into a network device prior to manually initiating a config snippet upload or config download  If set to no, a manual config snippet upload or config download will use the default credentials stored from the fields above.
				This setting can only be modified by a user with the Admin privileges.<br /><br />
				The default device connection timeout when rConfig is connecting to devices is 60 seconds. You may change this to suit your requirements.<br /><br />
				Turn on Debug device output to get more verbose logging. Generally only used for support purposes.
				You can view and delete debug logs here also.<br /><br />
			</p>
			<h4 class="helpH4">Email Settings</h4>
			<p>
				Please enter your mail server details per the Email Settings form. You can also test your SMTP server by clicking the 'Test Mail Server' button.<br /><br />
			</p>
			<h4 class="helpH4">Software & Database Details</h4>
			<p>
				PHP, Database and node count information is displayed in the next section, as well as the option to enable PHP logging. This should be set to Off, except if explicitly asked to turn it on for support.
			</p>

		<div class="break"></div>
	</div>
</body>
</html>
