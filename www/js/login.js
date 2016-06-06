// from http://www.javascripter.net/faq/browsern.htm

var nVer = navigator.appVersion;
var nAgt = navigator.userAgent;
var browserName = navigator.appName;
var fullVersion = '' + parseFloat(navigator.appVersion);
var majorVersion = parseInt(navigator.appVersion, 10);
var nameOffset, verOffset, ix;



// In Opera, the true version is after "Opera" or after "Version"
if ((verOffset = nAgt.indexOf("Opera")) != -1) {
    browserName = "Opera";
    fullVersion = nAgt.substring(verOffset + 6);
    if ((verOffset = nAgt.indexOf("Version")) != -1)
        fullVersion = nAgt.substring(verOffset + 8);
}
// In MSIE, the true version is after "MSIE" in userAgent
else if ((verOffset = nAgt.indexOf("MSIE")) != -1) {
    browserName = "Microsoft Internet Explorer";
    fullVersion = nAgt.substring(verOffset + 5);
}
// In Chrome, the true version is after "Chrome" 
else if ((verOffset = nAgt.indexOf("Chrome")) != -1) {
    browserName = "Chrome";
    fullVersion = nAgt.substring(verOffset + 7);
}
// In Safari, the true version is after "Safari" or after "Version" 
else if ((verOffset = nAgt.indexOf("Safari")) != -1) {
    browserName = "Safari";
    fullVersion = nAgt.substring(verOffset + 7);
    if ((verOffset = nAgt.indexOf("Version")) != -1)
        fullVersion = nAgt.substring(verOffset + 8);
}
// In Firefox, the true version is after "Firefox" 
else if ((verOffset = nAgt.indexOf("Firefox")) != -1) {
    browserName = "Firefox";
    fullVersion = nAgt.substring(verOffset + 8);
}
// In most other browsers, "name/version" is at the end of userAgent 
else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
        (verOffset = nAgt.lastIndexOf('/')))
{
    browserName = nAgt.substring(nameOffset, verOffset);
    fullVersion = nAgt.substring(verOffset + 1);
    if (browserName.toLowerCase() == browserName.toUpperCase()) {
        browserName = navigator.appName;
    }
}
// trim the fullVersion string at semicolon/space if present
if ((ix = fullVersion.indexOf(";")) != -1)
    fullVersion = fullVersion.substring(0, ix);
if ((ix = fullVersion.indexOf(" ")) != -1)
    fullVersion = fullVersion.substring(0, ix);

majorVersion = parseInt('' + fullVersion, 10);
if (isNaN(majorVersion)) {
    fullVersion = '' + parseFloat(navigator.appVersion);
    majorVersion = parseInt(navigator.appVersion, 10);
}

var noticeText = '<img src="images/exclamation.png" style="vertical-align: text-bottom;"/>Your browser (' + browserName + ' ' + fullVersion + ') is <b>out of date</b>. It may not render this site correctly. Learn how to update your browser <a href="compatibility.php"style="cursor:pointer;">here<a/>'
var notice = $("#broswerNotice p").html(noticeText);

// from http://www.tvidesign.co.uk/blog/CSS-Browser-detection-using-jQuery-instead-of-hacks.aspx
// Is this a version of IE?
if ($.browser.msie && $.browser.version < 6.99) {
    $('#announcement').hide();
    $("#broswerNotice").show();
}
// Is this a version of Mozilla?
if ($.browser.mozilla && $.browser.version < 3.5) {
    $('#announcement').hide();
    $("#broswerNotice").show();
}

// Is this a version of Chrome?
if ($.browser.chrome && $.browser.version < 11) {
    $('#announcement').hide();
    $("#broswerNotice").show();
}

// Is this a version of Safari?
if ($.browser.safari && $.browser.version < 3) {
    $('#announcement').hide();
    $("#broswerNotice").show();
}
// Is this a version of Safari?
if ($.browser.opera && $.browser.version < 9.4) {
    $('#announcement').hide();
    $("#broswerNotice").show();
}
