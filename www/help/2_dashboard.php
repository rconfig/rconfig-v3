<?php include("includes/head.main.inc.php"); ?>
<div id="mainBanner">
    <div id="title">
        <h1>Dashboard Overview</h1>
    </div>
</div>
<div id="mainContent">
    <div class="break"></div>
    <p>
        The <span class="rconfigNameStyle">rConfig</span> dashboard is a landing page after login to 
        <span class="rconfigNameStyle">rConfig</span> and a location to get some basic system information. 
        The <span class="rconfigNameStyle">rConfig</span> dashboard for this <a href="2_version.php" target="content">version</a> 
        contains two sections. 
    <ul>
        <li>Server information</li>
        <li>Last 5 devices added</li>
    </ul>
</p>

<div class="break"></div>
<p>
    The first thing you should know about the <span class="rconfigNameStyle">rConfig</span> dashboard is, 
    if the install directory is not deleted, or renamed per the setup requirements, a warning per fig3. 
    will display every time the dashboard page is loaded. You will be present the option to click the 'remove' link when the message displays
    where <span class="rconfigNameStyle">rConfig</span> will then remove the installation directory for you.
</p>
<div class="break"></div>
<p>
    fig3.
<div class="break"></div>
<img class="imgfig" src="images/fig3.png"/>
</p>
<div class="break"></div>
<p>
    The <span class="rconfigNameStyle">Server information</span> section contains the following items
<div class="break"></div>
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
                <td id="leftcell">Servername</td>
                <td>Current name of <span class="rconfigNameStyle">rConfig</span> Server</td>
            </tr>
            <tr align="left" valign="top">
                <td id="leftcell">IP Address</td>
                <td><span class="rconfigNameStyle">rConfig</span> Server configured IP address</td>
            </tr>
            <tr align="left" valign="top">
                <td id="leftcell"> DNS Addresses</td>
                <td> <span class="rconfigNameStyle">rConfig</span> Servers configured DNS Servers for name resolution</td>
            </tr>
            <tr align="left" valign="top">
                <td id="leftcell">Internet IP </td>
                <td>NAT'd IP address of <span class="rconfigNameStyle">rConfig</span> Server, if available. The reads the public IP as returned by <a href="http://www.rconfig.com/ip.php" target="_blank">http://www.rconfig.com/ip.php </a></td>
            </tr>					
            <tr align="left" valign="top">
                <td id="leftcell">Disk Free Space</td>
                <td>Contains the number of GiB available on "/" - root partition</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="break" style="clear:both;"></div>
<div class="break" style="clear:both;"></div>
<p>
    The <span class="rconfigNameStyle">Last 5 devices added/modified</span> section shows a list of the last 5 devices added to or modified in <span class="rconfigNameStyle">rConfig</span>. You will be able to click the devices name directly to bring you to the devices home page in <span class="rconfigNameStyle">rConfig</span>. The date added and the user who added the device are also shown in this table.
</p>
</div>
</body>
</html>

