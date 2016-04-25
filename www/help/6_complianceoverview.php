<?php include("includes/head.main.inc.php"); ?>
<div id="mainBanner">
    <div id="title">
        <h1>Compliance Overview</h1>
    </div>
</div>
<div id="mainContent">
    <div class="break"></div>
    <p>
        <span class="rconfigNameStyle">rConfig</span> Compliance Manager is designed hierarchically. The Compliance Managers individual components are Elements, Policies and Reports. When a report is created, it is automatically available in the Task Scheduler. You should create a scheduled task to run the required reports at the required times.
    <ul>
        <li><b>Elements:</b> Single commands that can be exactly matched or similar (contains) type match against the configuration file to be evaluated</li>
        <li><b>Policies:</b> A policy is a grouping of Elements. i.e. A "Security Policy" would contain elements that pertain to the security aspects of a configurations</li>
        <li><b>Reports:</b> A report is a grouping of policies. i.e. A "Security Report" can contain a "Web Access Security Policy" and a "CLI access Security Policy"</li>
    </ul>
</p>
<div class="break"></div>
<p>
    <b>Compliance Overview Page</b><br/>
    The Compliance Overview page contains a listing of historically generated Compliance reports
</p>

</div>
</body>
</html>
