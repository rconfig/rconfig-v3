$(function () {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";

// check if install directory is still present and show an error on the dashboard if it is
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxCheckInstallDir.php", function (data) {
        if (data.result == 'present') {
            showNotification({
                message: "The \"install\" directory has not been deleted or renamed - <a href=\"javascript:void(0)\" onclick=\"removeInstallDir();\">Remove it!</a>",
                type: "warning",
                autoClose: true,
                duration: 5
            });
        }
    });

    // check for online update with ajax
    $('#pleaseWait1').slideDown('fast');
    $('#noticeGood').hide();
    $('#noticeNoUpdate').hide();
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxGetLatestVersion.php", function (data) {

        if (data.success == true) {
            $('#noticeGood').show();
            $('#pleaseWait1').hide();
        } else {
            $('#noticeGood').hide();
            $('#noticeNoUpdate').show().fadeOut(2000);
            $('#pleaseWait1').hide();
        }
    });

    $("#refreshPubIp").click(function (evt) {
        $.ajax({
            url: 'lib/ajaxHandlers/ajaxGetPublicIP.php',
            type: 'POST',
            async: false,
            data: {},
            dataType: 'json',
            error: function () {
                errorDialog('Error');
            },
            success: function (data) {
                //check error
                $("#pubIp").html(ajax_load).load("lib/ajaxHandlers/publicIp.txt")
            }
        })
    })

    //retrieve vendor details to display on form from getRow GET variable
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxGetLast5NodesAdded.php", function (nodesData) {
        if (nodesData) {
            // next iterate over the JSON array for searchResult:line and then append each line to the Div
            var html = [];
            $.each(nodesData, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var id = obj.id
                var deviceName = obj.deviceName
                var deviceDateAdded = obj.deviceDateAdded
                var nodeAddedBy = obj.nodeAddedBy

                var rowHTML = ['<tr class="row_' + id + '">'];
                rowHTML.push('<td><a href="devicemgmt.php?deviceId=' + id + '&device=' + deviceName + '" alt="Click to view Device Details"  title="Click to view Device Details">' + deviceName + '</td>');
                rowHTML.push('<td>' + deviceDateAdded + '</td>');
                rowHTML.push('<td>' + nodeAddedBy + '</td>');
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''))
            })
            $('#last5NodesAdded tbody').html(html.join(''));
        }

    });

});

function removeInstallDir() {
    $('#pleaseWait').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxRemoveInstallDir.php", function (data) {
        if (data == 'success') {
            errorDialog("install directory deleted successfully")
        } else if (data == 'failure') {
            errorDialog("install directory could not be deleted - check logs for errors or remove/rename it manually")
        } else {
            errorDialog("something broke")
        }
    })
}