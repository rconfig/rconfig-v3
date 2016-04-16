<?php include("includes/head.inc.php"); ?>
<body>
<!-- Headwrap Include -->
<?php include("includes/masthead.inc.php"); ?>
<div id="mainwrap">
	<!-- TopNav Include -->
	<?php include("includes/topnav.inc.php"); ?>
	<div id="main">
		<?php 
			/* Custom Devices Custom Form Functions */
			require_once("lib/crud/devices.frm.func.php");
			// PHP File Tree
			// For documentation and updates, visit http://abeautifulsite.net/notebook.php?article=21
			// Main function file
			include("../classes/php_file_tree.php");
		?>
		<!-- Breadcrumb Include -->
		<?php include("includes/breadcrumb.inc.php"); ?>
		<!-- Announcement Include -->
		<?php include("includes/announcement.inc.php"); ?>
		<div id="content">
			<?php 
				// echo error message if is sent back in GET from CRUD
				if(isset($_SESSION['errors'])){
				// move nested errors array to new array
				$errors = $_SESSION['errors'];	
				}
				/* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
				$_SESSION['errors'] = array();
			?>
			<fieldset id="tableFieldset">
				<legend>Compare</legend>
				<div class="mainformDiv">
					<p>
					
						Please select a configuration file on the right hand side and one from the left hand side to run a comparison
					</p>
					<div id="spacer">
					</div>
					<br/>
					<form id="compareForm" method="post" enctype="multipart/form-data">
						<div id="comapareTreeLeft">
							<label>Left Configuration</label>
							
							<div id="tree_a_Div" style="display:none;">
								<?php 
									$allowed_a = array("txt");
									$chkOn_a = true; // set option to return lines with checkboxes in class output
									echo php_file_tree("/home/rconfig/data/", "[link]", $allowed_a, $chkOn_a );   
								?>
							</div>
							<!-- end tree_aDiv -->
						</div>
						<!-- end comapareTreeLeft -->
						<div id="comapareTreeLeft">
							<label>Right Configuration</label>
						
							<div id="tree_b_Div" style="display:none;">
								<?php 
									$allowed_b = array("txt");
									$chkOn_b = true; // set option to return lines with checkboxes in class output
									echo php_file_tree("/home/rconfig/data/", "[link]", $allowed_b, $chkOn_b);  
								?>
							</div>
							<!-- end tree_aDiv -->
						</div>
						<!-- end comapareTreeLeft -->
						</form>
					</div>
					<!-- End mainformDiv -->
					<div style="clear:both;"></div>
					<div id="spacer">
					</div>
						<div id ="linepaddingDiv">
							<label for="linepadding">Line Padding:</label>
							<input name="linepadding" id="linepadding" title="number of lines to display before/after each difference" size="1" maxlength="2" tabindex='5'>
						</div>
					
					<div id="toolbar">
						<button id="search" onclick="compare()" tabindex='7'>Compare</button>
						<button id="reset" onclick="reloadPage()" tabindex='8'>Reset Page</button>
					</div>
<div id="resultsDiv" name="resultsDiv">
			</div>
			</fieldset>
				
		</div>
		<!-- End Content -->
		<div style="clear:both;">
		</div>
	</div>
	<!-- End Main -->
	<!-- JS script Include -->
	<script type="text/JavaScript" src="js/configcompare.js"></script>
	<!-- Footer Include -->
	<?php include("includes/footer.inc.php"); ?>
</div>
<!-- End Mainwrap -->
</body>
</html>