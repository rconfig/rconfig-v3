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
}); 

// next script is for row highlighting and selection of table rows	
$("#configTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#configTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#configTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delGenConfig() {
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this configuration file?")
        if (answer) {
            $.post('lib/crud/templategenconfig.crud.php', {
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
        alert("Please select a config to delete!")
    }
}

function downloadNewConfigFile() {
    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.getJSON("lib/crud/templategenconfig.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
			$.each(data.rows, function (i, data) {
				var configLocation = data.configLocation;
				var loc = configLocation.split("/");
				var parsedPath = "";
				var len = loc.length - 1;
				for (i=0; i < len; i++) {
					if (!(loc[i] == "home") && !(loc[i] == "rconfig") && !(loc[i] == "www")) {
						parsedPath = parsedPath.concat(loc[i]).concat("/");
					}
					//.concat("/")
				}	
				var configFilename = data.configFilename;
				
				window.location=parsedPath.concat(configFilename);
				
			});
		});
	}
}

function viewGenConfig() {
    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        //retrieve task details to display on form from getRow GET variable
        $.getJSON("lib/crud/templategenconfig.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var id = data.id
                var configNameView = data.configName
                var templateNameView = data.templateName
                var newConfigView = data.configLocation
                var newConfigFileView = data.configFilename
				
                if (configNameView) {

                    $("#genConfigDetails").modal({
                        overlayClose: true,
                        position: ['10%', ]
                    });

                    $('#configNameView').text(configNameView)
                    $('#templateNameView').text(templateNameView)
                    $('#newConfigView').text(newConfigView.concat(newConfigFileView))
                    $('#newConfigFileView').text(newConfigFileView)

                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        alert("Please select a config!")
    }
}



// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "templategenconfig.php"
}
