$(document).ready(function () {
    // Open div and display error per above PHP next to input field
    // Check if 'message' GET is set in URL - if it is, display form and PHP error next to field
    if (location.href.match(/\error/)) { // check url for 'message'
        $('schedulerForm').show(); // show form (which will also display error msg in php after input field)
        $(".show_hide").show(); // show show_hide class 
    } else {
        $(".schedulerForm").hide(); //hide form
        $(".show_hide").show(); // show show_hide class 
    }

    $('.show_hide').click(function () { // show_hide function on click
        $(".schedulerForm").toggle(); //speed set to 1ms so that div does not render on side of page first
    });

    // disable 'mailErrorsOnly' chkbox by default
    document.getElementById('mailErrorsOnly').disabled = true;

    document.getElementById('reportTypeSlctDiv').style.display = 'none';
    document.getElementById('reportTypeSlct').disabled = true;

    document.getElementById('snippetSlctDiv').style.display = 'none';
    document.getElementById('snippetSlct').disabled = true;

    document.getElementById('mailErrorsOnlyDiv').style.display = 'none';
    document.getElementById('mailErrorsOnly').disabled = true;

    document.getElementById('deviceSelectRadioDiv').style.display = 'none';
    document.getElementById('catSelectRadioDiv').style.display = 'none';
    document.getElementById('selectRadio').disabled = true;

    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;

    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;

});

// next script is for row highlighting and selection of table rows	
$("#taskTbl tbody tr").click(function (e) {
    // get the rowId below
    var rowid = $(this).attr('setid');

    $("#taskTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#taskTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type == "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

/* Next function is to allow selection of devices or categories only select lists */
function deviceOrCatSelect() {
    chosen = ""
    len = document.taskForm.selectRadio.length

    for (i = 0; i < len; i++) { // get values from selectRadio selection
        if (document.taskForm.selectRadio[i].checked) {
            chosen = document.taskForm.selectRadio[i].value
        }
    }
    if (chosen == "deviceSelectRadio") { // if value = "deviceSelectRadio"
        document.taskForm.deviceSelect.disabled = false; //disable 'disabled' attr for 'deviceSelect' selection list
        document.taskForm.catSelect.disabled = true; //enable 'disabled' attr for 'catSelect' selection list
        document.getElementById("catSelect").selectedIndex = -1; // disable any selections made in the 'catSelect' selection list
    } else if (chosen == "catSelectRadio") { // if value = "catSelectRadio"
        document.taskForm.catSelect.disabled = false; //disable 'disabled' attr for 'catSelect' selection list
        document.taskForm.deviceSelect.disabled = true; //enable 'disabled' attr for 'deviceSelect' selection list
        document.getElementById("deviceSelect").selectedIndex = -1; // disable any selections made in the 'deviceSelect' selection list
    } else { // else both are disabled by default
        document.taskForm.deviceSelect.disabled = true;
        document.taskForm.catSelect.disabled = true;
    }
}

// Next action when delTask function is called from Delete button
function delTask() {

    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {
        var answer = confirm("Are you sure you want to remove this Task?")
        if (answer) {
            $.post('lib/crud/scheduler.crud.php', {
                id: rowid,
                del: "delete"
            }, function (result) {
                if (result.success) {
                    window.location.reload(); // reload the current page
                } else {
                    window.location.reload();
                }
            }, 'json');
        } else {
            window.location.reload();
        }
    } else {
        alert("Please select a task!")
    }
}


// Next action when getTask function is called from edit button
function getTask() {
    var getRow = "getRow"
    var rowid = $("input:checkbox:checked").attr("id")
    if (rowid) {

        //retrieve task details to display on form from getRow GET variable
        $.getJSON("lib/crud/scheduler.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var id = data.id
                var taskTypeView = data.taskType
                var taskNameView = data.taskname
                var taskDescView = data.taskDescription
                var addedByView = data.addedBy
                var dateAddedView = data.dateAdded

                // get crontime from DB and split to array
                var crontimeView = data.crontime
                var n = crontimeView.split(" ");

                if (n[0] == "*") {
                    var minutesView = "Every Minute"
                } else {
                    var minutesView = n[0]
                }
                if (n[1] == "*") {
                    var hoursView = "Every Hour"
                } else {
                    var hoursView = n[1] + "hrs"
                }
                if (n[2] == "*") {
                    var dayOfMonthView = "Every Day of the Month"
                } else {
                    var dayOfMonthView = n[2]
                }
                if (n[3] == "*") {
                    var MonthView = "Every Month"
                } else {
                    var MonthView = n[3]
                }
                if (n[4] == "*") {
                    var dayOfWeekView = "Every Day"
                } else {
                    var dayOfWeekView = n[4]
                }

                if (taskTypeView == "1") {
                    var taskTypeView = "Download Configurations"
                } else if (taskTypeView == "2") {
                    var taskTypeView = "Run Report"
                } else if (taskTypeView == "3") {
                    var taskTypeView = "Schedule Config Snippet"
                }
                if (taskNameView) {

                    $("#taskDetails").modal({
                        overlayClose: true,
                        position: ['10%', ]
                    });

                    $('#taskIdView').text(id)
                    $('#taskTypeView').text(taskTypeView)
                    $('#taskNameView').text(taskNameView)
                    $('#taskDescView').text(taskDescView)
                    $('#addedByView').text(addedByView)
                    $('#dateAddedView').text(dateAddedView)
                    $('#minutesView').text(minutesView)
                    $('#hoursView').text(hoursView)
                    $('#dayOfMonthView').text(dayOfMonthView)
                    $('#MonthView').text(MonthView)
                    $('#dayOfWeekView').text(dayOfWeekView)

                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });

            // get devices and list in deviceNameView DIV
            $.each(data.devices, function (i, data) {
                var deviceNameView = data.deviceName

                if (deviceNameView) {

                    $('#deviceNameView').append(deviceNameView + ", ")

                } else {
                    alert("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });


        });
    } else {
        alert("Please select a task!")
    }
} // end getTask Function



function mailErrorsChkBox() {
    var mailConnectionReportChkBox = document.getElementById('mailConnectionReport')
    if (mailConnectionReportChkBox.checked)
        document.getElementById('mailErrorsOnly').disabled = false;
    else
        document.getElementById('mailErrorsOnly').disabled = true;
}

function displayDownloadElements() {
    document.getElementById("reportTypeSlct").disabled = true;
    document.getElementById("reportTypeSlctDiv").style.display = 'none';

    document.getElementById("snippetSlct").disabled = true;
    document.getElementById("snippetSlctDiv").style.display = 'none';

    document.getElementById('mailErrorsOnlyDiv').style.display = 'block';
    document.getElementById('mailErrorsOnly').disabled = false;

    document.getElementById('deviceSelectRadioDiv').style.display = 'block';
    document.getElementById('catSelectRadioDiv').style.display = 'block';
    document.getElementById('selectRadio').disabled = false;

    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;

    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;
}

function displayReportElements() {
    document.getElementById("reportTypeSlct").disabled = false;
    document.getElementById("reportTypeSlctDiv").style.display = 'block';

    document.getElementById("snippetSlct").disabled = true;
    document.getElementById("snippetSlctDiv").style.display = 'none';

    document.getElementById('mailErrorsOnlyDiv').style.display = 'none';
    document.getElementById('mailErrorsOnly').disabled = true;

    document.getElementById('deviceSelectRadioDiv').style.display = 'none';
    document.getElementById('catSelectRadioDiv').style.display = 'none';
    document.getElementById('selectRadio').disabled = true;

    document.getElementById('chooseCatDiv').style.display = 'block';
    document.getElementById('catId').disabled = false;

    document.getElementById('catCommandDiv').style.display = 'block';
    document.getElementById('catCommand').disabled = false;
}

function displaySnippetElements() {
    document.getElementById("snippetSlct").disabled = false;
    document.getElementById("snippetSlctDiv").style.display = 'block';

    document.getElementById('reportTypeSlctDiv').style.display = 'none';
    document.getElementById('reportTypeSlct').disabled = true;

    document.getElementById('chooseCatDiv').style.display = 'none';
    document.getElementById('catId').disabled = true;

    document.getElementById('deviceSelectRadioDiv').style.display = 'block';
    document.getElementById('catSelectRadioDiv').style.display = 'block';
    document.getElementById('selectRadio').disabled = false;

    document.getElementById('catCommandDiv').style.display = 'none';
    document.getElementById('catCommand').disabled = true;
}

// change list of commands in select box based on categories selection - only when run report is selected
function changeType() {
    var catCommandDiv = document.getElementById('catCommandDiv');
    var catCommandSel = document.getElementById('catCommand');
    var catIdSelect = document.getElementById('catId');
    var catId = $('#catId').val();
    var taskTypeVal = $('#taskType').val()

    catCommandDiv.style.display = catIdSelect.selectedIndex != '' ? 'block' : 'none'; // check that anything other than '' for the catIdSelect is selected and display commands dropdown
    catCommandDiv.style.display = taskTypeVal === '3' ? 'none' : 'block'; // however, if the 'config snippet' task type is selected. Hide the Commands Div
    catCommandSel.disabled = taskTypeVal === '3' ? true : false; // however, if the 'config snippet' task type is selected. Disable the Commands Select

    if (taskTypeVal == 2) { // if taskType is Config Compare Report, then run next code to update various selects after selecting a category - this is not relevant for snippet cat select
        if (catId != '') { // if catId is not equal to '' i.e. catId is selected then carry on
            $.getJSON("lib/ajaxHandlers/ajaxGetCommandsByCat.php?catId=" + catId, function (data) {
                var command = '';
                command += '<option value="">Please select</option>';
                for (var i = 0; i < data.length; i++) {
                    command += '<option value="' + data[i].command + '">' + data[i].command + '</option>'; // need to specify command as value for option as this is what will populate the grep
                }
                $("select#catCommand").html(command);
            })
        }

        if (catId != '') { // if catId is not equal to '' i.e. catId is selected then carry on
            $.getJSON("lib/ajaxHandlers/ajaxGetNodesByCat.php?catId=" + catId, function (data) {
                var options = '';
                options += '<option value="">Please select</option>';
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].deviceName + '">' + data[i].deviceName + '</option>'; // need to specify deviceName as value for option as this is what will populate the grep
                }
                $("select#nodeId").html(options);
            })
        }
    } // end if taskType
}

// function to take sample cron string and populate fields
function selectSample(cronValue) {

    var array = cronValue.split(' ');

    var minute = array[0];
    var hour = array[1];
    var day = array[2];
    var month = array[3];
    var weekday = array[4];

    if (array != '' || array != ' ' || array != null) {

        // foreach section below - 
        // update the relevant field with the cron value
        // switch for the value, and update the relevant fields select for the output text related i.e. */5 = '*/5 every 5 minutes'
        $('#minute').val(minute)
        switch (minute)
        {
            case minute:
                $('#minuteSelect').val(minute)
                break;
            default:
                $('#minuteSelect').val('--')
        }

        $('#hour').val(hour)
        switch (hour)
        {
            case hour:
                $('#hourSelect').val(hour)
                break;
            default:
                $('#hourSelect').val('--')
        }

        $('#day').val(day)
        switch (day)
        {
            case day:
                $('#daySelect').val(day)
                break;
            default:
                $('#daySelect').val('--')
        }

        $('#month').val(month)
        switch (month)
        {
            case month:
                $('#monthSelect').val(month)
                break;
            default:
                $('#monthSelect').val('--')
        }

        $('#weekday').val(weekday)
        switch (weekday)
        {
            case weekday:
                $('#weekdaySelect').val(weekday)
                break;
            default:
                $('#weekdaySelect').val('--')
        }


    } else {
        alert('cron details not found')
    }
}

// function to add option value to relevant field
function selectOption(field, value) {
// chnage sample selction back to default
    $('#sampleOptions').val('--')

    if (value != '' || value != ' ' || value != null) {
        $('#' + field).val(value)
    } else {
        alert('Details not found for field' + field)
    }

}
