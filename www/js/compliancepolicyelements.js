$(document).ready(function () {
    if (location.href.match(/\error/)) { 
        $('mainformDiv').show(); 
        $(".show_hide").show(); 
		

	// else run a default page load		
    } else {
        $(".mainformDiv").hide();
        $(".show_hide").show(); 

    }
    $('.show_hide').click(function () {
        $(".mainformDiv").toggle();
    });
	
}); 

// next script is for row highlighting and selection of table rows	
$("#polElemTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#polElemTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#polElemTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delPolElem() {
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this Policy Element?")
        if (answer) {
            $.post('lib/crud/compliancepolicyelements.crud.php', {
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
        alert("Please select a Policy Element to delete!")
    }
}

function editPolElem() {

    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.getJSON("lib/crud/compliancepolicyelements.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var elementName = data.elementName
                var elementDesc = data.elementDesc
                var singleParam1 = data.singleParam1
                var singleLine1 = data.singleLine1
                if (elementName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="elementName"]').val(elementName)
                    $('input[name="elementDesc"]').val(elementDesc)
					$('#singleParam1').val(singleParam1).change();
                    $('input[name="singleLine1"]').val(singleLine1)
                    $('input[name="editid"]').val(rowid) // used to populate id input so that edit script will insert
                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        alert("Please select a Policy Element to edit!")
    }
}



// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "compliancepolicyelements.php"
}
