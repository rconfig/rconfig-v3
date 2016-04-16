function purgeDevice() {

    // call ajax purge script and return either success or fail msg
    $.getJSON("lib/ajaxHandlers/ajaxPurgeSqlData.php", function (data) {

        if (data) {
            var response = data.response
            alert(response)
        }
    });
}