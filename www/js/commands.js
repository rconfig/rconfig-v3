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
$("#commandsTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#commandsTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#commandsTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delCommand() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Command?", 'lib/crud/commands.crud.php', "Please select a Command!")
}

function editCommand() {
    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/commands.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {

            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var command = data.command
                if (command) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="command"]').val(command)
                    $('input[name="editid"]').val(rowid) // used to populate id input so that edit script will insert
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Command!");
    }
}

// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "commands.php"
}

