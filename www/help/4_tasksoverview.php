<?php include("includes/head.main.inc.php"); ?>
<div id="mainBanner">
    <div id="title">
        <h1>Scheduled Tasks Overview</h1>
    </div>
</div>
<div id="mainContent">
    <div class="break"></div>
    <p>
        <span class="rconfigNameStyle">rConfig</span> utilizes the Linux CRON daemon to run scheduled tasks. Currently you can create a three types of scheduled tasks. 
    <ul>
        <li>Download Configurations</li>
        <li>Run Reports</li>
        <li>Schedule Config Snippet</li>
    </ul>

</p>
<div class="break"></div>
<p>
    <b>Download Configurations</b><br/>
    When you select to create a scheduled task to Download Configurations, you will choose the category of devices or the individual devices themselves of which to run the pre-configured set of commands against. You will choose the recurring times which to run the script. You can also select to email a connectivity report.
</p>
<div class="break"></div>
<p>
    <b>Run Reports</b><br/>

    <span class="rconfigNameStyle">rConfig</span> version <b><?php echo $config_version; ?></b> only has a single report to run. This is expected to grow in future versions. <br/>The <b>Configuration Comparison</b> report allows you to compare downloaded outputs. In this report, the latest downloaded output is compared to the version just before it by date. i.e A <b>show run</b> downloaded today, will be compared against a <b>show run</b> for yesterday. Equally, if the latest <b>show run</b> was last Friday, and the most recent before this was the Friday before, these files are compared.
</p>
<div class="break"></div>
<p>
    The report iterates over a category when run. The selected command is compared for all devices in a selected category. The output is saved to a file and can be viewed in the reports section, and/or emailed per the email settings also.
</p>
<div class="break"></div>
<p>
    <b>Schedule Config Snippet</b><br/>

    You are able to schedule predefined Config Snippets to automatically execute on one or more devices at a scheduled time.  This uses the same Cron schedule layout as Download Configurations.  Once completed, a status report can optionally be emailed to any email address set on the settings page.
</p>
<div class="break"></div>
</div>
</body>
</html>