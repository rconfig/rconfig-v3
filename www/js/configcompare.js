function compare() {
    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    var linepadding = $('#linepadding').val();
    if (linepadding == '') {
        linepadding = 'null'
    }
    var path_a = $("#tree_a_Div input:checkbox:checked").attr("value")
    var path_b = $("#tree_b_Div input:checkbox:checked").attr("value")
    // Check of more than one checkbox selected for each side
    var countChecked_a = $("#tree_a_Div input:checked").length;
    var countChecked_b = $("#tree_b_Div input:checked").length;

    // error if more than one check box selected for both sides
    if (countChecked_a > 1) {
        alert("You selected more than 1 configuration on the left side")
		return;
    }
    if (countChecked_b > 1) {
        alert("You selected more than 1 configuration on the right side")
		return;
    }

    // validate path_a & path_b
    if (path_a == null || path_b == null) { // check is not empty i.e. a node is actually selected
        alert('Please make Selections!')
    }

    // ajax logic below
    if (path_a) {

        $('#resultsDiv').load("lib/crud/configcompare.crud.php?path_a=" + path_a + "&path_b=" + path_b + "&linepadding=" + linepadding);

    } else {
        alert("Please make selections!")
    }
} // end compare function

function reloadPage() {
    window.location.reload(true) // true to reload with out POSTBACK warning in browser
}