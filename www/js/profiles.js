$(document).ready(function () {
    if (location.href.match(/\error/)) {
        $('.mainformDiv').show();
        $(".show_hide").show();
    } else {
        $(".mainformDiv").hide();
        $(".show_hide").show();
    }
    $('.show_hide').click(function () {
        $(".mainformDiv").toggle();
    });

    $("#profileDetailsFieldset").hide();

    // tinymce config
    tinymce.init({
        selector: 'textarea',
        force_p_newlines: false,
        force_br_newlines: true,
        convert_newlines_to_brs: false,
        remove_linebreaks: true,
        forced_root_block: 'pre',
        menubar: false,
        statusbar: false,
        plugins: "save autoresize",
        toolbar: "save"
    });

});

// single row selector from rconfigFunctions.js
tblRowSelector('profilesTbl');

// Next action when delProfile function is called from Delete button
function delProfile() {
    var message = "If you remove a connection profile, you will need to review your devices configurations in rConfig for missing connection profiles. <br />Deleting a connection profile, removes the profile from any devices that are configured to use it. You will need to re-configure manually. Please click view devices for this connection profiles for a full list of devices configured.";

    bootbox.confirm({
        message: message,
        backdrop: false,
        size: 'small',
        title: "Notice!",
        callback: function (result) {
            if (result) {
//                removeItem("Are you sure you want to remove this Profile?", 'lib/crud/profiles.crud.php', "Please select a Profile!");
            } else {
                window.location.reload();
            }
        }
    });
}

// Next action when editProfile function is called from Delete button
function editProfile() {
    // gotta hide the upload box, as you cannot edit the file from the main form once uploaded.
    $("#profileFile").hide();
    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        //retrieve profile details to display on form from getRow GET variable
        $.getJSON("lib/crud/profiles.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var profileName = data.profileName;
                var profileLocation = data.profileLocation;
                var profileDescription = data.profileDescription;
                var deviceAccessMethodId = data.deviceAccessMethodId;
                var vendorId = data.vendorId;
                if (profileName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="profileName"]').val(profileName);
                    $('input[name="profileLocation"]').val(profileLocation);
                    $('input[name="profileDescription"]').val(profileDescription);
                    $("#accessMeth").val(data.deviceAccessMethodId);
                    $("#vendorId").val(data.vendorId);
                    $('input[name="editid"]').val(rowid);
                } else {
                    errorDialog("Could not load Data!");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Profile!");
    }
}

function expandAll() {
    // Hide all subfolders at startup
    $(".php-file-tree").find("UL").show();
    $("#bottomButtons").show();
}

function hideAll() {
    // Hide all subfolders at startup
    $(".php-file-tree").find("UL").hide();
    $("#bottomButtons").hide();
}

// Open File by ajax
function openFile(filePath) {
    var filename = filePath.replace(/^.*[\\\/]/, '')
    $("#profileDetailsFieldset").show();
    $("#filename").text(filename);
    $("#filepath").val(filePath);

// get filename meta data from DB to populate the about section
    $.getJSON("lib/ajaxHandlers/ajaxGetProfilesFileData.php?filePath=" + filePath, function (data) {
        //loop through all items in the JSON array  
        $.each(data, function (i, data) {
            console.log(data.profileName);
            var profileName = data.profileName;
            var profileDescription = data.profileDescription;
            var deviceAccessMethodId = data.deviceAccessMethodId;
            var connText = data.deviceAccessMethodId == 1 ? 'telnet' : 'ssh';
            var profileDateAdded = data.profileDateAdded;
            var profileAddedBy = data.profileAddedBy;
            var profileLastEditBy = data.profileLastEditBy;
            var profileLastEdit = data.profileLastEdit;
//            $("#about").text(data.profileName);
            $("#desc").text(data.profileDescription);
            $("#connection").text(connText);
            $("#uploaded").text(profileDateAdded);
            $("#uploadedBy").text(profileAddedBy);
            $("#profileLastEdit").text(profileLastEdit);
            $("#profileLastEditBy").text(profileLastEditBy);
        });
    });

    if (filePath) {
        $.ajaxSetup({cache: false});
        var html = '';
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            $.each(data, function (key, value) {
                html += value + "<br />"; // add break as tinymce will add new lines 
            });
            tinyMCE.activeEditor.setContent(html);
        });
    } else {
        errorDialog('File not Selected!');
    }
}


function showDevices(el) {
    var rowid = el.getAttribute('id');
    if (rowid) {
        var devices = '';
        $.ajaxSetup({cache: false});
        //retrieve task details to display on form from getRow GET variable
        $.getJSON("lib/ajaxHandlers/ajaxGetProfileDevices.php?id=" + rowid, function (data) {
            $.each(data, function (key, value) {
                deviceName = value.deviceName;
                devices += deviceName + ', ';
            });
            bootbox.dialog({
                title: '<h3 class="h3">Device using this profile:</h3>',
                size: 'medium',
                message: devices,
                buttons: {
                    main: {
                        label: "close",
                        className: "btn"
                    }
                }
            });
        });
    }
}