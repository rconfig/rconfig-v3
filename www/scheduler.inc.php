<?php
/* Includes */
require_once("../classes/db.class.php");
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
// $db = new db(); -> not calling this here because it's already called in the /scheduler.php master file

/* Get Row count from nodes where NOT deleted*/
	$rs = $db->q('SELECT COUNT(*) AS total FROM tasks WHERE status = 1');  
	$row = mysql_fetch_row($rs);  
	$result["total"] = $row[0];  

/* Instantiate Paginator Class */
	$pages = new Paginator;
	$pages->items_total = $result['total'];
	$pages->mid_range = 7; // Number of pages to display. Must be odd and > 3
	$pages->paginate();
	echo $pages->display_pages();
	echo "<span class=\"\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
	echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

/* GET all nodes records from DB */
	$q = $db->q("SELECT * FROM tasks 
				WHERE status = 1 
				ORDER BY id ASC
				$pages->limit");  
	// push rows to $items array
	unset($db); // need to close DB connection cause interferes with chooseCatDiv function call and throws errors
	$items = array();  
	while($row = mysql_fetch_assoc($q)){  
		array_push($items, $row);  
	}  

/* Create Multidimensional array for use later */
	$result["rows"] = $items;   

	$i = 0; # row counter  to enable alternate row coloring
?>

<table id="taskTbl" class="tableSimple">
	<thead>
		<th><input type="checkbox" disabled="disabled"/></th>
		<th align="left">Task Name</th>
		<th align="left">Task Description</th>
		<th align="left">Created</th>
	</thead>
	<tbody>
	<?php 
	/* do a foreach on the $result['rows'] array*/
	foreach ($result['rows'] as $rows):
	$id = $rows['id'];
	/* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring*/
	echo '<tr class="row' . ($i++ % 2) . '">'; 
	?>
    <td align="center"><input type="checkbox" id="<?php echo $id; ?>"/></td>
	<td align="left"><?php echo $rows['taskname'] ?></td>
	<td align="left"><?php echo $rows['taskDescription'] ?></td>
	<td align="left"><?php echo $rows['dateAdded'] ?></td>
  </tr>
<?php endforeach;?>
	</tbody>
</table>

<?php 
echo "<div class=\"spacer\"></div>";
echo $pages->display_pages(); 
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";
?>
