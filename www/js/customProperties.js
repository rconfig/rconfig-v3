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

    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this Custom Property, and all data for this property?")
        if (answer) {
            $.post('lib/crud/customProperties.crud.php', {
                id: rowid,
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
    } else {
        alert("Please select a Custom Property!")
    }
}