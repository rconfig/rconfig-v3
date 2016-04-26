$(function () {

});

function getServerStatus() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;

    $.getJSON("lib/ajaxHandlers/ajaxDbServerReachable.php?server=" + server + "&port=" + port + "", function (data) {

        if ($.isEmptyObject(data) != true) {

            var phpVersion = data
            $('#dbServerPortTest').html(phpVersion);

        } else {
            $('#dbServerPortTest').append('<font color="#F7492E">Cannot get PHP version or is not installed</font>');
        }
    })
}

function getStatus() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;
    var dbName = document.getElementById('dbName').value;
    var dbUsername = document.getElementById('dbUsername').value;
    var dbPassword = document.getElementById('dbPassword').value;

    $.getJSON("lib/ajaxHandlers/ajaxDbTests.php?server=" + server + "&port=" + port + "&dbName=" + dbName + "&dbUsername=" + dbUsername + "&dbPassword=" + dbPassword + "", function (data) {

        if ($.isEmptyObject(data) != true) {

            var connTest = data.connTest
            var credTest = data.credTest
            var dbTest = data.dbTest
            var siteUrl = data.siteUrl
            var installDir = data.installDir

            $('#dbServerPortTest').html(connTest);
            $('#dbNameTest').html(credTest);
            $('#dbCredTest').html(dbTest);

        } else {
            $('#dbServerPortTest').append('<font class="bad">Unable to test</font>');
        }
    })
}

function installConfig() {
    var server = document.getElementById('dbServer').value;
    var port = document.getElementById('dbPort').value;
    var dbName = document.getElementById('dbName').value;
    var dbUsername = document.getElementById('dbUsername').value;
    var dbPassword = document.getElementById('dbPassword').value;

    $.getJSON("lib/ajaxHandlers/ajaxDbInstall.php?server=" + server + "&port=" + port + "&dbName=" + dbName + "&dbUsername=" + dbUsername + "&dbPassword=" + dbPassword + "", function (data) {

        if ($.isEmptyObject(data) != true) {

            var error = data.error
            var success = data.success

            $('#msg').html(error);
            $('#msg').html(success);

        } else {
            $('#msg').append('<font class="bad">A problem occurred!/font>');
        }
    })
}