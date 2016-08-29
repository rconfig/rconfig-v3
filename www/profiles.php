<?php include("includes/head.inc.php"); ?>
<body>
    <!-- Headwrap Include -->
    <?php include("includes/masthead.inc.php"); ?>
    <div id="mainwrap">
        <!-- TopNav Include -->
        <?php include("includes/topnav.inc.php"); ?>
        <?php
        /* Custom Devices Custom Form Functions, using this instead of repeating the code for profiles vendor select below */
        require_once("lib/crud/devices.frm.func.php");
        ?>
        <div id="main">
            <!-- Breadcrumb Include -->
            <?php include("includes/breadcrumb.inc.php"); ?>
            <!-- Announcement Include -->
            <?php include("includes/announcement.inc.php"); ?>
            <div id="content">
                <?php
                if (isset($_SESSION['errors'])) {
                    $errors = $_SESSION['errors'];
                }
                /* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
                $_SESSION['errors'] = array();
                ?>
                <fieldset id="tableFieldset">
                    <legend>Profile Management</legend>
                    <?php
                    if (isset($errors['Success'])) {
                        echo "<span class=\"error\">" . $errors['Success'] . "</span><br/>";
                    }
                    ?> 
                    <?php
                    if (isset($errors['Fail'])) {
                        echo "<span class=\"error\">" . $errors['Fail'] . "</span><br/>";
                    }
                    ?>
                    <div id="toolbar">
                        <button class="show_hide">Add Profile</button>
                        <button onclick="editProfile()">Edit Profile</button>
                        <button onclick="delProfile()">Remove Profile</button>
                    </div>
                    <!-- begin Profile form -->
                    <div class="mainformDiv">
                        <form id="profilesAdd" method="post" action="lib/crud/profiles.crud.php" enctype="multipart/form-data"  class="myform stylizedForm stylized"  style="width:100%;">

                            <div style="width:300px; margin-bottom:10px;">
                                <?php
// echo error message if is sent back in GET from CRUD
                                if (isset($errors['duplicatefile'])) {
                                    echo "<span class=\"error\">" . $errors['duplicatefile'] . "</span>";
                                }
                                ?>
                                <label for="profileName"><font color="red">*</font> Profile Name:</label>
                                <input name="profileName" id="profileName">
                                <div class="spacer"></div>
                                <?php
// echo error message if is sent back in GET from CRUD
                                if (isset($errors['profileName'])) {
                                    echo "<span class=\"error\">" . $errors['profileName'] . "</span>";
                                }
                                ?>
                                <label for="profileDescription"><font color="red">*</font> Profile Description:</label>
                                <input name="profileDescription" id="profileDescription">
                                <div class="spacer"></div>
                                <?php
// echo error message if is sent back in GET from CRUD
                                if (isset($errors['profileDescription'])) {
                                    echo "<span class=\"error\">" . $errors['profileDescription'] . "</span>";
                                }
                                ?>

                                <label for="profileFile">Profile File:</label>
                                <input name="profileFile" type="file" id="profileFile" size="40"/>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['fileInvalid'])) {
                                    echo "<span class=\"error\">" . $errors['fileInvalid'] . "</span>";
                                }
                                ?> 
                                <?php
                                if (isset($errors['fileError'])) {
                                    echo "<span class=\"error\">" . $errors['fileError'] . "</span>";
                                }
                                ?> 
                                <label><font color="red">*</font> Access Method:</label>

                                <select name="deviceAccessMethodId" id="accessMeth" tabindex="10"  style="width:155px;"  onchange="updatePort(this.value);">

                                    <?php
                                    /* func is from devices.frm.func.php */
                                    if (isset($deviceAccessMethodId)) {
                                        accessMethod($deviceAccessMethodId);
                                    } else {
                                        accessMethod();
                                    }
                                    ?>
                                </select>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['deviceAccessMethodId'])) {
                                    echo "<span class=\"error\">" . $errors['deviceAccessMethodId'] . "</span>";
                                }
                                ?><br/>    


                                <label><font color="red">*</font> Vendor:</label>
                                <select name="vendorId" id="vendorId" tabindex='3' style="width:155px;">
                                    <?php
                                    if (isset($vendorId)) {
                                        vendorId($vendorId);
                                    } else {
                                        vendorId();
                                    }
                                    /* taken from devices.frm.func.php */
                                    ?>
                                </select>
                                <div class="spacer"></div>
                                <?php
                                if (isset($errors['vendorId'])) {
                                    echo "<span class=\"error\">" . $errors['vendorId'] . "</span>";
                                }
                                ?> 

                                <input type="hidden" id="add" name="add" value="add">
                                <input type="hidden" id="editid" name="editid" value="">
                                <div class="spacer"></div>
                                <button id="save" type="submit">Save</button>
                                <button class="show_hide" type="button">Close</button><?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
                            </div>
                        </form>
                    </div>
                    <!-- End mainformDiv -->
                    <div id="table">
                        <?php
                        /* full table stored off in different script */
                        include("profiles.inc.php");
                        ?>
                    </div>
                </fieldset>

                <fieldset id="connProfilesFieldset" style="width:30%; min-height:147px; float:left;">
                    <legend>Connection Profiles</legend>
                    <?php
                    // For documentation and updates, visit http://abeautifulsite.net/notebook.php?article=21
                    // Main function file
                    include("../classes/php_file_tree.php");
                    $allowedExtentsions = array("php", "txt");
                    echo php_file_tree("/home/rconfig/classes/connectionProfiles/", "onclick=javascript:openFile('[link]');", $allowedExtentsions);
                    ?>
                    <button id="expandAll" onclick="expandAll()" tabindex="7" class="smlButton">Show All</button> 
                    <button id="hideAll" onclick="hideAll()" tabindex="8" class="smlButton">Close All</button> 
                </fieldset>

                <fieldset id="profileDetailsFieldset" style="width:50%; min-height:180px; float:left;">
                    <legend>Profile Details</legend>
                    <div class="panel-group">
                        <div class="panel panel-default" style="width: 450px; height: 180px;">
                            <div class="panel-heading">
                                <img src="images/icomoon_20.png" style="margin-left: -8px;"> 
                                <span id="about" style="font-weight: bold; font-size: 0.98em; color: #939393; display: block; margin-top: -15px; margin-left: 15px;"> Profile Info </span>
                            </div>
                            <div class="panel-body">
                                <table style="border-spacing: 5px;">
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Filename: </td>
                                        <td><span id="filename" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Description: </td>
                                        <td><span id="connection" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Uploaded: </td>
                                        <td><span id="uploaded" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Uploaded By: </td>
                                        <td><span id="uploadedBy" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Last Edit: </td>
                                        <td><span id="profileLastEdit" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="line-height: 10px; font-weight: bold;">Last Edit By: </td>
                                        <td><span id="profileLastEditBy" style="font-size: 1em; font-weight: normal;"></span></td>
                                    </tr>                                    
                                </table>


                            </div>
                        </div>
                    </div>
                        <div id="codeContent"></div>
                        <input type="hidden" id="filepath" name="filepath" value="">
                        <input id="clickMe" type="button" value="Save Edits" onclick="save_editor_content()" />
                </fieldset>
            </div>
            <!-- End Content -->
            <div style="clear:both;">
            </div>
        </div>
        <!-- End Main -->
        <!-- JS script Include -->
        <script type="text/JavaScript" src="js/profiles.js"></script>
        <script src="https://ace.c9.io/build/src/ace.js" type="text/javascript" charset="utf-8"></script>
        <!-- Footer Include -->
        <?php include("includes/footer.inc.php"); ?>
    </div>
    <!-- End Mainwrap -->
</body>
</html>