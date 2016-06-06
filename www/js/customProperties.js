$(document).ready(function () {
    if (location.href.match(/\error/)) {
        $('mainformDiv').show();
        $(".show_hide").show();
    } else {
        $(".mainformDiv").hide();
        $(".show_hide").show();
    }
    $('.show_hide').click(function () {
        $(".mainformDiv").toggle();
    });
});

// next func is for row highlighting and selection of table rows	
$("#customPropTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#customPropTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#customPropTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delCustProp() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Custom Property, and all data for this property?", 'lib/crud/customProperties.crud.php', "Please select a Custom Property!")
}