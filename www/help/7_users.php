<?php include("includes/head.main.inc.php"); ?>
<div id="mainBanner">
    <div id="title">
        <h1>User Management </h1>
    </div>
</div>
<div id="mainContent">
    <div class="break"></div>
    <p>
        You may add, edit or delete users on this page. 
        It is recommended to change the Admin user Password , 
        or even create a new Admin user, and delete the default Admin account afterwards. 
        As of <span class="rconfigNameStyle">rConfig</span> Version <?php echo $config_version; ?> 
        Admin users have exclusive access to the Users Page and Backup pages. All users have access to everything else.
    </p>
    <br />
    <br />
    <br />
    <p>
        Password not alphanumeric. Must be: <br />
        at least one lowercase char <br/>
        at least one uppercase char <br/>
        at least one digit <br/>
        at least one special sign of @#-_$%^&+=§!? <br/> <br/> <br/>

    </p>
    <div class="break"></div>
</div> 
</body>
</html>