<?php
/* Includes */
require_once("../classes/db2.class.php");
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
$db2 = new db2();

/* Get Row count from profiles where NOT deleted */
$db2->query('SELECT COUNT(*) AS total FROM profiles WHERE status = 1');
$row = $db2->resultsetCols();
$result["total"] = $row[0];
/* Instantiate Paginator Class */
$pages = new Paginator;
$pages->items_total = $result['total'];
$pages->mid_range = 7; // Number of pages to display. Must be odd and > 3
$pages->paginate();
echo $pages->display_pages();
echo "<span class=\"\">" . $pages->display_jump_menu() . $pages->display_items_per_page() . "</span>";
echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

$db2->query("SELECT p.id, p.profileName, p.profileDescription, p.profileLocation, p.vendorId, v.vendorName, p.deviceAccessMethodId, d.devicesAccessMethod FROM profiles p
        LEFT OUTER JOIN vendors v ON p.vendorId = v.id
        LEFT OUTER JOIN devicesaccessmethod d ON p.deviceAccessMethodId = d.id
        WHERE p.`status` = 1 $pages->limit");
$queryResult = $db2->resultset();
// push rows to $items array
$items = array();
foreach ($queryResult as $row) {
    array_push($items, $row);
}

/* Create Multidimensional array for use later */
$result["rows"] = $items;

$i = 0; # row counter  to enable alternate row coloring
?>

<table id="profilesTbl" class="tableSimple">
    <thead>
    <th><input type="checkbox" disabled="disabled"/></th>
    <th align="left">Profile Name</th>
    <th align="left">Profile Descriptions</th>
    <th align="left">Profile Protocol</th>
    <th align="left">Profile Vendor</th>
    <th align="left">Devices</th>
</thead>
<tbody>
    <?php
    /* do a foreach on the $result['rows'] array */
    foreach ($result['rows'] as $rows):
        $id = $rows['id'];
        /* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring */
        echo '<tr class="row' . ($i++ % 2) . '">';
        ?>
    <td align="center"><input type="checkbox" name="tablecheckbox" id="<?php echo $id; ?>"/></td>
    <td ><?php echo $rows['profileName'] ?></td>
    <td ><?php echo $rows['profileDescription'] ?></td>
    <td ><?php echo $rows['devicesAccessMethod'] ?></td>
    <td ><?php echo $rows['vendorName'] ?></td>
    <td ><a href="#" onclick="showDevices(this);" id="<?php echo $id; ?>">View Devices</a></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php
echo $pages->display_pages();
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";