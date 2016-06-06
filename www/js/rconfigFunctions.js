// function to open new window based on content passed to the function
function writeConsole(content, filePath) {
    top.consoleRef = window.open('', 'myconsole', 'width=960,height=600' + ',menubar=0' + ',toolbar=0' + ',status=0' + ',scrollbars=1' + ',resizable=1')
    top.consoleRef.document.writeln(
            '<html><head><title>' + filePath +
            '</title><link rel="stylesheet" type="text/css" href="css/fileOutput.css" /></head>' +
            '<body onLoad="self.focus()"><div id="topDiv"><a href="lib/crud/downloadFile.php?download_file=' + filePath + '" rel="nofollow" title="click to download" alt="click to download"><img src="images/download.png" alt="download file..." title="download file..."/></a><strong>' + filePath +
            '</strong></div><div class="spacer"></div><br/>' + '<div style="font-family: Courier, \'Courier New\', monospace; font-size:11px">' +
            '<pre>' + content + '</pre>' + '</div>' + '</body></html>'
            )
    top.consoleRef.document.close()
}

function openHelp() {

    window.open('help/index.php',
            'rConfig - Documentation',
            'width=, \
			   height=800, \
			   width=1000, \
			   directories=no, \
			   location=no, \
			   menubar=no, \
			   resizable=no, \
			   scrollbars=0, \
			   status=no, \
			   toolbar=no');
    return false;

}


// enter key function
// http://stackoverflow.com/questions/979662/how-to-detect-pressing-enter-on-keyboard-using-jquery
$.fn.enterKey = function (fnc) {
    return this.each(function () {
        $(this).keypress(function (ev) {
            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
            if (keycode == '13') {
                fnc.call(this, ev);
            }
        })
    })
}

function errorDialog(text) {
    var dialog = bootbox.dialog({
        size: 'small',
        title: "Notice!",
        backdrop: false,
        message: text,
        buttons: {
            main: {
                label: "close",
                className: "btn",
            }

        }
    });
    return dialog;
}