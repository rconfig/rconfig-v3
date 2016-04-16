<?php
/* Includes */
require_once('../classes/db.class.php');
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
$db = new db();

/* Get Row count from nodes where NOT deleted*/
$rs             = $db->q('SELECT COUNT(*) AS total FROM categories WHERE status = 1');
$row             = mysql_fetch_row($rs);
$result["total"] = $row[0];

/* Instantiate Paginator Class */
$pages              = new Paginator;
$pages->items_total = $result['total'];
$pages->mid_range   = 7; // Number of pages to display. Must be odd and > 3
$pages->paginate();
echo $pages->display_pages();
echo "<span class=\"\">" . $pages->display_jump_menu() . $pages->display_items_per_page() . "</span>";
echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

/* GET all nodes records from DB */
$q    	= $db->q("SELECT id, categoryName FROM categories WHERE status = 1 $pages->limit");
// push rows to $itesm array
$items = array();
while ($row = mysql_fetch_assoc($q)) {
    array_push($items, $row);
}

/* Create Multidimensional array for use later */
$result["rows"] = $items;
//row counter  to enable alternate row coloring
$i              = 0;
?>

<table id="categoryTbl" class="tableSimple">
	<thead>
		<th><input type="checkbox" disabled="disabled"/></th>
		<th align="left">Category Name</th>
	</thead>
	<tbody>
	<?php 
	/* do a foreach on the $result['rows'] array*/
	foreach ($result['rows'] as $rows):
		$id = $rows['id'];
		$categoryName =  $rows['categoryName'];
		/* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring*/
		echo '<tr class="row' . ($i++ % 2) . '">'; 
	?>
    <td align="center"><input type="checkbox" id="<?php echo $id; ?>"/></td>
	<td align="left"><?php echo $categoryName; ?></td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>

<?php 
echo $pages->display_pages(); 
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";
?>
