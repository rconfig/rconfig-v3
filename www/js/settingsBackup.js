$(function () {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/backups/&ext=zip", function (data) {

        if ($.isEmptyObject(data) != true) {
            var html = [];
            $.each(data, function (key, obj) {
                var filename = obj.filename
                var filepath = obj.filepath
                var filesize = obj.filesize
                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to download" alt="click to download">' + filename + '</a>  ' + filesize + '</td>');
                "onclick=javascript:openFile('[link]');"
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''))
            })
            $('#backupsDiv tbody').html(html.join(''));
        } else {
            $('#backupsDiv tbody').append('<tr><td><font color="red">Cannot display backup files</font></td></tr>');
        }
    })
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/backups/syslogs/&ext=zip", function (data) {

        if ($.isEmptyObject(data) != true) {
            var html = [];
            $.each(data, function (key, obj) {
                var filename = obj.filename
                var filepath = obj.filepath
                var filesize = obj.filesize

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to download" alt="click to download">' + filename + '</a>  ' + filesize + '</td>');
                "onclick=javascript:openFile('[link]');"
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''))
            })
            $('#syslogsDiv tbody').html(html.join(''));
        } else {
            $('#syslogsDiv tbody').append('<tr><td><font color="red">Cannot display system log archives</font></td></tr>');
        }
    })
});


// Open File by ajax
function createBackup() {
    $('#pleaseWait1').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxBackupFull.php", function (data) {

        if (data.success == true) {
            alert("Backup created successfully")
            window.location.reload()
        } else {
            alert("Could not create backup")
            window.location.reload()
        }
    })
}

// Open File by ajax
function createBackupSyslog() {
    $('#pleaseWait2').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxBackupSyslog.php", function (data) {

        if (data.success == true) {
            alert("Systems Logs archive created successfully")
            window.location.reload()
        } else {
            alert("Could not create Systems Logs archive")
            window.location.reload()
        }

    })
}

function deleteFiles(filePath, ext) {
    var answer = confirm("Are you sure you want to remove all archives?")
    if (answer) {
        $('#pleaseWait').slideDown('fast');
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
            if (data.success == true) {
                alert("Archives deleted successfully")
            } else {
                alert("Some Archives could not be deleted")
            }
            window.location.reload()
        })
    } else {
        window.location.reload();
    }
}