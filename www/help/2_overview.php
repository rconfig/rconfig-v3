<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>Overview</h1>
		</div>
	</div>
	<div id="mainContent">
		<div class="break"></div>
		<p>
			<span class="rconfigNameStyle">rConfig</span> allows you to download the output of multiple show commands via telnet or SSHv2 from CLI based devices. Please check the <span class="rconfigNameStyle">rConfig</span> website for a list of supported manufacturers and models.
		</p>
		<div class="break"></div>
		<p>
			Most current configuration management tools only allow the download of running or startup configurations from CLI based devices. However, With <span class="rconfigNameStyle">rConfig</span> you are not limited in the amount of commands you can assign to a device, or category of devices. 
		</p>
		<div class="break"></div>
		<p>
			Take the example below, where six show commands are applied to the Firewall category. Two of those commands are applied to both Firewalls and Routers categories, and the Routers category has a further two commands applied. This allows you to take an accurate snapshot of your devices at a given moment in time, or multiple times during the day.
		<div class="break"></div>
		</p>
		<p>
			The output of each command is stored as a text file on the Operating Systems file storage instead of the <span class="rconfigNameStyle">rConfig</span> Database. The file is stored by its command name text, in a folder structure for the date of download. See fig2. The allows the use of OS level utilities for quick search and retrieval of the data. 
			<br/> <br/> <font color='red'>* as of version 2.0.3 the filenames are appended with '-1301' (i.e. <i>showrunning-config-1301.txt</i>) to represent the timestamp that the file was downloaded. This allows downloads of configuration files form the same devices multiple times in a single day.</font>
		<div class="break"></div>
		</p>
		<p>
			fig1.
			<div class="break"></div>
			<img class="imgfig" src="images/fig1.png"/>
		</p>
		<p>
			fig2.
			<div class="break"></div>
			<img class="imgfig" src="images/fig2.png"/>
		</p>
		<div class="break"></div>
		<p>
			<span class="rconfigNameStyle">rConfig</b> enables you to set a category of devices, add devices to set categorys and apply a command or set of commands to a category for scheduled downloads at a later time, or regular intervals. You may also set custom properties, for searching and categorization. 
		<div class="break"></div>
		</p>
			
		<h4 class="helpH4">Scheduling</h4>
		<p>
			Please review the section for information on choosing and running scheduled tasks.  <span class="rconfigNameStyle">rConfig</span>  allows two types of scheduled tasks. A configuration download task and a configuration comparison task. You may create your own tasks scripts to run, but it is expected this list of tasks will grow in future releases as demand grows.
		<div class="break"></div>
		</p>
		<h4 class="helpH4">Reports</h4>
		<p>
			There are two different report types in <span class="rconfigNameStyle">rConfig</span> also. Device connectivity reports and configuration comparison reports. These can be emailed when a scheduled task is run or historical reports can be viewed in the reports section
		<div class="break"></div>
		</p>
		<h4 class="helpH4">Search and Compare</h4>
		<p>
			<span class="rconfigNameStyle">rConfig</span> has an extremely quick search utility which is based exclusively on the Linux command lin tools, <em>find</em> and <em>grep</em>. When run, the webpage ask the OS to search all files within the specified criteria and return the result.
			<p>
			Similarly, <span class="rconfigNameStyle">rConfig</span> has a very fast configuration comparison tool, allowing you to very quickly compare configuration between devices and command outputs.
		</p>
		<div class="break"></div>
		</p>
	</div>
</body>
</html>