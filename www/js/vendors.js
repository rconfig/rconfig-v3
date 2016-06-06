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

// next script is for row highlighting and selection of table rows	
$("#vendorsTbl tbody tr").click(function (e) {
    // get the rowId below
    var rowid = $(this).attr('setid');

    $("#vendorsTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#vendorsTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

// Next action when delVendor function is called from Delete button
function delVendor() {
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {

        bootbox.confirm({
            message: "Are you sure you want to remove this Vendor?",
            backdrop: false,
            size: 'small',
            title: "Notice!",
            callback: function (result) {
                if (result) {
                    $.post('lib/crud/vendors.crud.php', {
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
            }
        });
    } else {
        errorDialog("Please select a Vendor!");
    }
}
// Next action when editVendor function is called from Delete button
function editVendor() {
    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.ajaxSetup({cache: false});
        //retrieve vendor details to display on form from getRow GET variable
        $.getJSON("lib/crud/vendors.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var vendorName = data.vendorName
                if (vendorName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="vendorName"]').val(vendorName)
                    $('input[name="editid"]').val(rowid)
                } else {
                    errorDialog("Could not load Data!");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Vendor!");
    }
}