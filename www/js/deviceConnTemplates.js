// create namespace for codemirror.editor use in functions later
var cm = {};

$(document).ready(function () {
    // setup code mirror
    cm.editor = "";
    cm.editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
      styleActiveLine: true,
      matchBrackets: true,
      scrollbarStyle: null
    });    
    $(cm.editor.getWrapperElement()).hide();
    $("#toolbar2").hide();
    
    // Throw alert dialog if create pass or failed
    if (getParameter('status') == 'success') {
        errorDialog('Template created successfully')
    } else if(getParameter('status') == 'failed') {
        errorDialog('Template failed. Try again')
    } else if(getParameter('status') == 'duplicateFile') {
        errorDialog('The filename already exists. Try editing file instead, or choose a different filename. Your template has been erased.')
    }
})

function invalid(str){
    var regex = /[ !@#$%^&*()+\=\[\]{};':"\\|,<>\/?]/g;
	return regex.test(str);
}
function hasWhiteSpace(str) {
    return str.indexOf(' ') >= 0;
}

// single row selector from rconfigFunctions.js
tblRowSelector('templatesTbl');

function createTemplate(){
    $(cm.editor.getWrapperElement()).show();
    $("#toolbar2").show();
    $("#templateTitle").show();
}

function saveCreate(){
    var fileName = $('#fileName').val();
    var code = cm.editor.doc.getValue();
    
    if(fileName && !invalid(fileName) && !hasWhiteSpace(fileName)){
        request = $.ajax({
            url: "/lib/ajaxHandlers/ajaxAddTemplate.php",
            type: "post",
            async:false,
            cache:false,
            data: { fileName: fileName, code: code }
        });    
        if(request.responseText === '"success"'){
            window.location.href = "deviceConnTemplates.php?status=success";
        } else if(request.responseText === '"failed"') {
            window.location.href = "deviceConnTemplates.php?status=failed";
        } else if(request.responseText === '"duplicateFile"') {
            window.location.href = "deviceConnTemplates.php?status=duplicateFile";
        }
    } else {
        errorDialog('Please enter a valid filename. Do not use spaces, and only "_" or "-" as special characters')
    }
}

function editTemplate(id){
    var getRow = "getRow";
//    var rowid = $(".fileNameRow").attr("id");
    if (id) {
        $.ajaxSetup({cache: false});
        //retrieve vendor details to display on form from getRow GET variable
        $.getJSON("lib/crud/deviceConnTemplates.crud.php?id=" + id + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var vendorName = data.vendorName;
                if (vendorName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="vendorName"]').val(vendorName);
                    $('input[name="editid"]').val(rowid);
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

function cancelCreate(){
    window.location.href = "deviceConnTemplates.php";
}