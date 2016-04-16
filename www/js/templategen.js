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
	
	// delete whitespace from Textarea
	var templateTxtArea = document.getElementById('template');
	templateTxtArea.value = templateTxtArea.value.replace(/^\s*|\s*$/g,'');
	
	document.getElementById('newConfigNameDiv').style.display = 'none';
	document.getElementById('newConfigName').disabled = true;
	document.getElementById('templateVarsDiv').style.display = 'none';
	document.getElementById('templateVars').disabled = true;
	document.getElementById('templateVarSubsDiv').style.display = 'none';
	document.getElementById('templateVarSubs').disabled = true;
}); 

// next script is for row highlighting and selection of table rows	
$("#templateTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#templateTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#templateTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delTemplate() {
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this Template?")
        if (answer) {
            $.post('lib/crud/templategen.crud.php', {
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
        alert("Please select a Template to delete!")
    }
}

function editTemplate() {

    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.getJSON("lib/crud/templategen.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var templateName = data.templateName
                var templateDesc = data.templateDesc
                var template = data.template
                if (templateName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="templateName"]').val(templateName)
                    $('input[name="templateDesc"]').val(templateDesc)
                    $('textarea[name="template"]').val(template)
                    $('input[name="editid"]').val(rowid) // used to populate id input so that edit script will insert
					
					document.getElementById('templateVarsDiv').style.display = 'none';
					document.getElementById('templateVars').disabled = true;
					document.getElementById('templateVarSubsDiv').style.display = 'none';
					document.getElementById('templateVarSubs').disabled = true;
                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        alert("Please select a template to edit!")
    }
}

function genTemplate() {

    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        $.getJSON("lib/crud/templategen.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var templateName = data.templateName
				var templateVars = data.templateVars
				var templateVarSubs = data.templateVarSubs
				if (templateName) {
					if ($('.mainformDiv').is(':hidden')) {
						$('.mainformDiv').slideToggle();
					}
                    $('input[name="templateName"]').val(templateName)
					$('input[name="templateName"]').blur()
					$('input[name="templateName"]').css({'background-color' : '#DFD8D1'})
					$('textarea[name="templateVars"]').val(templateVars)
					$('textarea[name="templateVars"]').blur()
					$('textarea[name="templateVars"]').css({'background-color' : '#DFD8D1'})
					$('textarea[name="templateVarSubs"]').val("")
                    $('input[name="genid"]').val(rowid) // used to populate id input so that edit script will insert
					
					document.getElementById('templateNameDiv').style.display = 'block';
					document.getElementById('templateName').disabled = false;
					document.getElementById('templateName').readOnly = true;
					document.getElementById('templateDescDiv').style.display = 'none';
					document.getElementById('templateDesc').disabled = true;
					document.getElementById('templateDiv').style.display = 'none';
					document.getElementById('template').disabled = true;
					document.getElementById('templateVarsDiv').style.display = 'block';
					document.getElementById('templateVars').disabled = false;
					document.getElementById('newConfigNameDiv').style.display = 'block';
					document.getElementById('newConfigName').disabled = false;
					document.getElementById('templateVarSubsDiv').style.display = 'block';
					document.getElementById('templateVarSubs').disabled = false;
                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        alert("Please select an existing template to generate from!")
    }
}



// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "templategen.php"
}