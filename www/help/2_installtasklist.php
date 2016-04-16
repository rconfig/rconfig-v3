<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>Post-Install Tasks</h1>
		</div>
	</div>
	<div id="mainContent">		
		<div class="break"></div>
			<p>
			Once your <span class="rconfigNameStyle">rConfig</span> installation is complete, you need to carry out some essential tasks per the list below. Please review the overview section to remind your self of some of the concepts of <span class="rconfigNameStyle">rConfig</span> before you start inputting data
			</p>
		<div class="break"></div>
		<p>
			<b>Login Credentials:-</b><br/>
			Default username:- <b>admin</b><br/>
			Default password:- <b>admin</b><br/><br/>
			Please carry out the following tasks once you are logged in to <span class="rconfigNameStyle">rConfig</span>
			<ul class="checklist">
				<li>Delete the install diectory after installation is complete</li>
				<li>Reset admin password on the <b>settings/users</b> page</li>
				<li>Create new users on the <b>settings/users</b> page</li>
				<li>Add a vendor on the <b>devices/vendors</b> page</li>
				<li>Add commands on the <b>devices/commands</b> page</li>
				<li>Add custom properties on the <b>devices/Custom Properties</b> page</li>
				<li>Verify and update all settings on the <b>settings</b> page</li>
				<li>Take a system backup on the <b>settings/system backups</b> page</li>
				<li>Add a device</li>
			</ul>
			<div class="break"></div>	
		</p>
		<div class="break"></div>
		<input type="button" value="Print this page" onclick="printpage()">
	</div>
<script>
function printpage()
  {
  window.print()
  }
</script>
</body>
</html>