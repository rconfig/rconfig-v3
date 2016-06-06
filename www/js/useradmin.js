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

$("#userAddTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#userAddTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#userAddTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delUser() {
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this User?")
        if (answer) {
            $.post('lib/crud/userAdmin.crud.php', {
                id: rowid,
                delete: "delete"
            }, function (result) {
                if (result.success) {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            }, 'json');
        } else {
            window.location.reload();
        }
    } else {
        errorDialog("Please select a User!")
    }
}

function editUser() {
    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/userAdmin.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var username = data.username
                if (username) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="username"]').val(username)
                    $('input[name="email"]').val(data.email)
                    $("#ulevelid").val(data.userlevel);
                    $('input[name="editid"]').val(rowid)
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show();
            });
        });
    } else {
        errorDialog("Please select a User!")
    }
}