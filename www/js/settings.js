$(function () {

    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDebugStatus", function (data) {
        $('#debugNoticeDiv').html(data);
    })
    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getPhpLoggingStatus", function (data) {
        $('#getPhpLoggingStatusDiv').html(data);
    })    
	$.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getTimeZone", function (dataTime) {
        $("#timeZone").val(dataTime)
    })    
	$.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDefaultCredsManualSet", function (dataCredSet) {
        $("#defaultCredsManualSet").val(dataCredSet)
    })
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/logs/debugging/&ext=txt", function (data) {

        if ($.isEmptyObject(data) != true) {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var filename = obj.filename
                var filepath = obj.filepath
                var filesize = obj.filesize

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="#noLink" onclick="javascript:openFile(\'' + filepath + '\');">' + filename + ' - ' + filesize + '</td>');
                "onclick=javascript:openFile('[link]');"
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''))
            })
            $('#debugLogFiles tbody').html(html.join(''));
        } else {
            $('#debugLogFiles tbody').append('<tr><td><font color="red">Turn on debugging to collect debug files</font></td></tr>');
        }
    })


    //retrieve vendor details to display on form from getRow GET variable
    $.getJSON("lib/ajaxHandlers/ajaxGetSMTPSettings.php", function (data) {
        //loop through all items in the JSON array  
        $.each(data, function (key, obj) {
            var smtpServerAddr = obj.smtpServerAddr
            var smtpFromAddr = obj.smtpFromAddr

            if (smtpServerAddr) {
                //output data to fields
                $('input[name="smtpServerAddr"]').val(smtpServerAddr)
                $('input[name="smtpFromAddr"]').val(obj.smtpFromAddr)
                $("#smtpRecipientAddr").val(obj.smtpRecipientAddr)
                if (obj.smtpAuth == "1") {
                    $('#smtpAuth').attr('checked', 'checked')
                    $('#authDiv').show();
                    $('input[name="smtpAuthUser"]').val(obj.smtpAuthUser)
                    $('input[name="smtpAuthPass"]').val(obj.smtpAuthPass)
                }
                if (obj.smtpLastTest.substring(0, 6) === "Passed") {
                    // alert(obj.smtpLastTest)
                    $("#smtpLastTest").html("<font color=\"green\">" + obj.smtpLastTest + "  - " + obj.smtpLastTestTime + "</font>")
                } else {
                    $("#smtpLastTest").html("<font color=\"red\">" + obj.smtpLastTest + " -  " + obj.smtpLastTestTime + "</font>")
                }
                $("#smtpUpdateButton").show();
                $("#smtpSaveButton").hide();


            } else {

                $("#smtpUpdateButton").hide();
                $("#smtpSaveButton").show();
            }
        })
    })

    // show/hide SMTP auth details based on checkbox
    $('#smtpAuth').live('change', function () {
        if ($(this).attr("checked")) {
            $('#authDiv').show();
        } else {
            $('#authDiv').hide();
        }
    });
	
// when pressing Enter on text box, auto-click relevant Update button
	$(document).ready(function(){
// LDAP Server text box
		$('#ldapServer').keypress(function(e){
		  if(e.keyCode==13)
		  $('#ldapServerGo').click();
		});
//Connection Timeout text box
		$('#deviceTout').keypress(function(e){
		  if(e.keyCode==13)
		  $('#deviceToutGo').click();
		});
//Default Credentials text boxes (all 3)
//Default Node Username text box
		$('#defaultNodeUsername').keypress(function(e){
		  if(e.keyCode==13)
		  $('#updateDefaultPass').click();
		});
//Default Node Password text box
		$('#defaultNodePassword').keypress(function(e){
		  if(e.keyCode==13)
		  $('#updateDefaultPass').click();
		});
//Default Node Enable Mode Password text box
		$('#defaultNodeEnable').keypress(function(e){
		  if(e.keyCode==13)
		  $('#updateDefaultPass').click();
		});
	});
});

// Open File by ajax
function openFile(filePath) {

    if (filePath) {
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            writeConsole(data.join('<br/>'), filePath);
        })
    } else {
        alert('File not Selected!')
    }
}


function deleteDebugFiles(filePath, ext) {

    $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success == true) {
            alert("Debug files deleted successfully")
        } else {
            alert("Some files could not be deleted")
        }
        window.location.reload()
    })
}

function timeZoneChange() {
    var timeZone = $('#timeZone').val();
    if (timeZone != '') {
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?timeZoneChange=" + timeZone, function (data) {

            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getTimeZone", function (data2) {
                var timeZoneNotice = data2
                $('#timeZoneNoticeDiv').html(data);
            })
        })
    } else {
        alert('Could not set timeZone')
    }
}

function debugOnOff() {
    var debugOnOff = $('#debugOnOff').val();
    // alert(debugOnOff)	
    if (debugOnOff != '') {
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?debugOnOff=" + debugOnOff, function (data) {

            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDebugStatus", function (data2) {
                var debugNotice = data2
                $('#debugNoticeDiv').html(data + "<div class=\"break\"></div>" + debugNotice);
            })
        })
    } else {
        alert('Could not set debug')
    }
}


function phpLoggingOnOff() {
    var phpLoggingOnOff = $('#phpLoggingOnOff').val();

    if (phpLoggingOnOff != '') {
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?phpLoggingOnOff=" + phpLoggingOnOff, function (data) {
            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getPhpLoggingStatus", function (data2) {
                var getPhpLoggingStatus = data2
                $('#getPhpLoggingStatusDiv').html(data + "<div class=\"break\"></div>" + getPhpLoggingStatus);
            })
        })
    } else {
        alert('Could not set debug')
    }
}

function deviceToutGo() {
    var deviceToutVal = $('#deviceTout').val();
    // alert(deviceToutVal)	

    if (deviceToutVal == null || deviceToutVal == '' || deviceToutVal == '0' || deviceToutVal == '00' || deviceToutVal == '000') {
        // if throw error
        alert('Device Connection Timeout must be a value between 1-999')
    } else {
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?deviceToutVal=" + deviceToutVal, function (data) {
            $('#deviceToutInfoDiv').html(data);
            $('#updated').slideDown('fast');
        })
    }
} // end deviceToutGo()


// function to open new window based on content passed to the function
function writeConsole(content, filePath) {
    top.consoleRef = window.open('', 'myconsole', 'width=750,height=600' + ',menubar=0' + ',toolbar=0' + ',status=0' + ',scrollbars=1' + ',resizable=1')
    top.consoleRef.document.writeln('<html><head><title>rConfig Debugging Logs</title></head>' + '<body bgcolor=white onLoad="self.focus()">' + filePath + '<br/>' + '<hr/>' + '<div STYLE="font-family: Courier, \'Courier New\', monospace; font-size:11px">' + '<pre style="line-height:.7;">' + content + '</pre>' + '</div>' + '</body></html>')
    top.consoleRef.document.close()
}

function purgeDevice() {
    var answer = confirm("Are you sure you want to purge deleted items from the Database? ")
    if (answer) {
        // call ajax purge script and return either success or fail msg
        $.getJSON("lib/ajaxHandlers/ajaxPurgeSqlData.php", function (data) {

            if (data) {
                var response = data.response
                alert(response)
            }
        });
    }
}


// Next action when delCategory function is called from Delete button
function smtpClearSettings() {

    var answer = confirm("Are you sure you want to clear SMTP Settings?")
    if (answer) {
        $.post('lib/settingsEmail.crud.php', {
            del: "delete"
        }, function (result) {
            if (result.success) {
                window.location.reload(); // reload the user current page
            } else {
                window.location.reload();
            }
        }, 'json');
    } else {
        window.location.reload();
    }
}

//  test SMTP mail sending and return success or fail 
function smtpTest() {
    $('#pleaseWait').slideDown('fast');
    $.getJSON("lib/ajaxHandlers/ajaxSmtpTest.php", function (data) {
        if (data.success == true) {
            alert("Email sent successfully")
            $('#pleaseWait').slideUp('fast');
        } else {
            alert("Email failed to send - check logs for errors")
        }
        window.location.reload()
    })
}

function updateDefaultPass(defaultNodeUsername, defaultNodePassword, defaultNodeEnable){
	var defaultNodeUsername = defaultNodeUsername
	var defaultNodePassword = defaultNodePassword
	var defaultNodeEnable = defaultNodeEnable

        $.getJSON('lib/ajaxHandlers/ajaxUpdateDefaultUserPass.php?defaultNodeUsername=' + defaultNodeUsername + '&defaultNodePassword=' + defaultNodePassword + '&defaultNodeEnable=' + defaultNodeEnable, function (data) {
            if (data) {
                var response = data
                    document.getElementById('updatedDefault').innerHTML = response;
                    $('#updatedDefault').slideDown('fast');
            }
        });
        $.getJSON('lib/ajaxHandlers/ajaxUpdateDefaultUserPassNode.php?defaultNodeUsername=' + defaultNodeUsername + '&defaultNodePassword=' + defaultNodePassword + '&defaultNodeEnable=' + defaultNodeEnable, function (data) {
            if (data) {
                var response = data
                    document.getElementById('updatedDefault').innerHTML = response;
                    $('#updatedDefault').slideDown('fast');
            }
        });

}

function defaultCredsManualSet() {
    var defaultCredsManualSet = $('#defaultCredsManualSet').val();

    if (defaultCredsManualSet != '') {
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?defaultCredsManualSet=" + defaultCredsManualSet, function (data) {
            if (data) {
                var response = data
                  document.getElementById('updatedDefaultCredsManualSet').innerHTML = response;
                  $('#updatedDefaultCredsManualSet').slideDown('fast');
            }
        });
    } else {
        alert('Could not set default credentials setting when manually uploading & downloading configs')
    }
}
