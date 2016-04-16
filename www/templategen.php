<?php include("includes/head.inc.php"); ?>
<body>
<!-- Masthead Include -->
<?php include("includes/masthead.inc.php"); ?>
<div id="mainwrap">
	<!-- TopNav Include -->
	<?php include("includes/topnav.inc.php"); ?>
	<div id="main">
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
				<legend>Current Templates</legend>
				<?php if(isset($errors['Success'])){echo "<span class=\"error\">".$errors['Success']."</span><br/>";}?> 
				<?php if(isset($errors['Fail'])){echo "<span class=\"error\">".$errors['Fail']."</span><br/>";}?> 
				<?php if(isset($errors['hostfolder'])){echo "<span class=\"error\">".$errors['hostfolder']."</span><br/>";}?> 
				<?php if(isset($errors['fileCreateError'])){echo "<span class=\"error\">".$errors['fileCreateError']."</span><br/>";}?>
				<?php if(isset($errors['crontab'])){echo "<span class=\"error\">".$errors['crontab']."</span><br/>";}?>
				<div id="toolbar">
					<button class="show_hide">Add Config Template</button>
					<button onclick="genTemplate()">Generate Config</button>
					<button onclick="editTemplate()">Edit Config Template</button>
					<button onclick="delTemplate()">Remove Config Template</button>
				</div>
				<div id="table">
					<?php 
					/* full table stored off in different script */
					include("templategen.inc.php"); 
					?>
				</div>
			</fieldset>
			<div class="mainformDiv">
				<form id="templateAddForm" name="templateAddForm" method="post" action="lib/crud/templategen.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">
					
					<div id="formDiv" style="width:600px; margin-bottom:10px;">
					<div id="templateNameDiv">
						<label for="templateName"><font color="red">*</font> Template Name:</label>
						<input name="templateName" id="templateName" size="75" tabindex='1' title="Template name" required />
					</div>
					<div style="float:left;" id="templateDescDiv">
						<label for="templateDesc"> Template Description:</label>
						<input name="templateDesc" id="templateDesc" size="150" tabindex='2' title="Template description" />
					</div>

					<div style="clear:both" id="templateDiv">
						<label for="template"><font color="red">*</font> Config Template:</label>
						<textarea rows="8" cols="100" wrap="off" id="template" name="template" tabindex='6' style="width:500px;margin-left:10px;float:clear;" title="Base config with variables surrounded by an @ symbol to replace changed variables (eg. @IPADDR@)  See help files for more info" required />
						</textarea> 
						<div class="spacer"></div>
					</div>
					
					<div id="newConfigNameDiv">
						<label for="newConfigName"><font color="red">*</font> New Config Name:</label>
						<input name="newConfigName" id="newConfigName" size="75" tabindex='1' title="Name for new config file generated based off the selected Template" required />
					</div>

					<div style="clear:both" id="templateVarsDiv">
						<label for="templateVars"><font color="red">*</font> Extracted Variables:</label>
						<textarea rows="8" cols="100" wrap="off" id="templateVars" name="templateVars" tabindex='7' style="width:500px;margin-left:10px;float:clear;" title="All unique variables extracted from the Template"readonly />
						</textarea>
						<br />
						<font size="1">For each extracted variable above, enter in the value to be substituted, for each displayed variable, in the same order as above.</font>
						<div class="spacer"></div>
					</div>

					<div style="clear:both" id="templateVarSubsDiv">
						<label for="templateVarSubs"><font color="red">*</font> Variable Substitution:</label>
						<textarea rows="8" cols="100" wrap="off" id="templateVarSubs" name="templateVarSubs" tabindex='8' style="width:500px;margin-left:10px;float:clear;" title="For each extracted variable, enter in the data you want to substitute it for when generating this config file.  One data substitution per line.  See help files for more info" required />
						</textarea>
						<div class="spacer"></div>
					</div>

					<input type="hidden" id="add" name="add" value="add" tabindex='3'>
					<input type="hidden" id="editid" name="editid" value="" tabindex='4'>
					
					<input type="hidden" id="editid" name="genid" value="" tabindex='5'>
					<div class="spacer"></div>
					<button id="save" type="submit" tabindex='21'>Save</button>
					<button class="show_hide" type="button" tabindex='22'>Close</button><?php /* type="button" to remove default form submit function which when pressed can cause the form action attr to take place */ ?>
					</div>
				</form>
			</div>
			<!-- End mainformDiv -->
			<!-- End Content -->
			<div style="clear:both;">
			</div>
		</div>
		<!-- End Main -->
		<!-- JS script Include -->
		<script type="text/JavaScript" src="js/templategen.js"></script>
		<!-- Modal  -->
		<div id="taskDetails" style="display:none;">
			<h2>Task Details</h2>
			<hr/>
			<p>
				<strong>Switch config type: </strong>
			<br/>
				<span id="switchTypeView" name="switchTypeView"></span>
			</p>
			<br/>
			<p>
				<strong>Hostname: </strong>
			<br/>
				<span id="hostNameView" name="hostNameView"></span>
			</p>
			<br/>
			<p>
				<strong>Switch config link: </strong>
			<br/>
				<a id="switchConfigLink" name="switchConfigLink" onClick="downloadConfigFile()"><span id="switchConfigView" name="switchConfigView"></span></a>
			</p>
			<br/>
			<p>
				<strong>SW-VSS config link: </strong>
			<br/>
				<a id="switchPeerOneConfigLink" name="switchPeerOneConfigLink" onClick="downloadPeerOneConfigFile()"><span id="switchPeerOneConfigView" name="switchPeerOneConfigView"></span></a>
			</p>
			<br />
			<p>
				<strong>Switch config filename: </strong>
			<br/>
				<span id="switchConfigFileView" name="switchConfigFileView"></span>
			</p>
			<br/>
			<br/><br/><br/>
			<br/><a href="#" class="simplemodal-close">Close</a>
		</div>
		<!-- Modal -->
		<!-- Footer Include -->
		<?php include("includes/footer.inc.php"); ?>
	</div>
	<!-- End Mainwrap -->
</div>
</body>
</html>