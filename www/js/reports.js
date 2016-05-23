$(function () {
    outputReports('connectionReports')
    outputReports('compareReports')
    outputReports('complianceReports')
    outputReports('configSnippetReports')

});

function outputReports(reportName) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/reports/" + reportName + "/&ext=html", function (data) {

        if ($.isEmptyObject(data) != true) {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var filename = obj.filename
                var filepath = obj.filepath

                var rowHTML = ['<tr class="">'];
                rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + filepath + '" rel="nofollow" title="click to view" alt="click to view">' + filename + '</td>');
                "onclick=javascript:openFile('[link]');"
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''))
            })
            $('#' + reportName + ' tbody').html(html.join(''));
        } else {
            $('#' + reportName + ' tbody').append('<tr><td><font color="red">Cannot display reports files or folder is empty</font></td></tr>');
        }
    })

}

function deleteFiles(filePath, ext, id) {
    $('#pleaseWait' + id).slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success == true) {
            alert("Reports deleted successfully")
        } else {
            alert("Some Reports could not be deleted")
        }
        window.location.reload()
    })
}