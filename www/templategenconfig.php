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
				<legend>Generated Configs</legend>
				<?php if(isset($errors['Success'])){echo "<span class=\"error\">".$errors['Success']."</span><br/>";}?> 
				<?php if(isset($errors['Fail'])){echo "<span class=\"error\">".$errors['Fail']."</span><br/>";}?> 
				<?php if(isset($errors['hostfolder'])){echo "<span class=\"error\">".$errors['hostfolder']."</span><br/>";}?> 
				<?php if(isset($errors['fileCreateError'])){echo "<span class=\"error\">".$errors['fileCreateError']."</span><br/>";}?>
				<?php if(isset($errors['crontab'])){echo "<span class=\"error\">".$errors['crontab']."</span><br/>";}?>
				<div id="toolbar">
					<button onclick="viewGenConfig()">View Config</button>
					<button onclick="delGenConfig()">Remove Config</button>
				</div>
				<div id="table">
					<?php 
					/* full table stored off in different script */
					include("templategenconfig.inc.php"); 
					?>
				</div>
			</fieldset>
			<div class="mainformDiv">
				<form id="templateAddForm" name="templateAddForm" method="post" action="lib/crud/templategen.crud.php" enctype="multipart/form-data" class="myform stylizedForm stylized" style="width:100%;">
					
					<div id="formDiv" style="width:600px; margin-bottom:10px;">
					<div id="templateNameDiv">
						<label for="templateName"> Template Name:</label>
						<input name="templateName" id="templateName" size="75" tabindex='1' <?php if(isset($errors['templateNameVal'])){ echo 'value="'.$errors['templateNameVal'].'"';}?>>
						<div  id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
						<?php if(isset($errors['templateName'])){echo "<span class=\"error\">".$errors['templateName']."</span>";}?>
						</div>
					</div>
					<div style="float:left;" id="templateDescDiv">
						<label for="templateDesc"> Template Description:</label>
						<input name="templateDesc" id="templateDesc" size="150" tabindex='2'  <?php if(isset($errors['templateDescVal'])){ echo 'value="'.$errors['templateDescVal'].'"';}?>>
						<div id="errorDiv" style="float:left;margin-left:220px; margin-top:-10px; margin-bottom:10px;">
						<?php if(isset($errors['templateDesc'])){echo "<span class=\"error\">".$errors['templateDesc']."</span>";}?>
						</div>
					</div>

					<div style="clear:both" id="templateDiv">
						<label for="template"> Config Template:</label>
						<textarea rows="8" cols="100" wrap="off" id="template" name="template" tabindex='6' style="width:500px;margin-left:10px;float:clear;" <?php if(isset($errors['templateVal'])){ echo 'value="'.$errors['templateVal'].'"';}?>/>
						</textarea> 
						<div class="spacer"></div>
						<div  id="errorDiv" style="float:left;margin-left:77px; margin-top:-10px; margin-bottom:10px;">
							<?php if(isset($errors['template'])){echo "<span class=\"error\">".$errors['template']."</span>";}?>
						</div>
					</div>

					<div style="clear:both" id="templateVarsDiv">
						<label for="templateVars"> Extracted Variables:</label>
						<textarea rows="8" cols="100" wrap="off" id="templateVars" name="templateVars" tabindex='7' style="width:500px;margin-left:10px;float:clear;" readonly <?php if(isset($errors['templateVarsVal'])){ echo 'value="'.$errors['templateVarsVal'].'"';}?>/>
						</textarea>
						<br />
						<font size="1">For each extracted variable above, enter in the value to be substituted, for each displayed variable, in the same order as above.</font>
						<div class="spacer"></div>
						<div  id="errorDiv" style="float:left;margin-left:77px; margin-top:-10px; margin-bottom:10px;">
							<?php if(isset($errors['templateVars'])){echo "<span class=\"error\">".$errors['templateVars']."</span>";}?>
						</div>
					</div>

					<div style="clear:both" id="templateVarSubsDiv">
						<label for="templateVarSubs"> Variable Substitution:</label>
						<textarea rows="8" cols="100" wrap="off" id="templateVarSubs" name="templateVarSubs" tabindex='8' style="width:500px;margin-left:10px;float:clear;" <?php if(isset($errors['templateVarSubsVal'])){ echo 'value="'.$errors['templateVarSubsVal'].'"';}?>/>
						</textarea>
						<div class="spacer"></div>
						<div  id="errorDiv" style="float:left;margin-left:77px; margin-top:-10px; margin-bottom:10px;">
							<?php if(isset($errors['templateVarSubs'])){echo "<span class=\"error\">".$errors['templateVarSubs']."</span>";}?>
						</div>
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
		<script type="text/JavaScript" src="js/templategenconfig.js"></script>
		<!-- Modal  -->
		<div id="genConfigDetails" style="display:none;">
			<h2>Generated Config Details</h2>
			<hr/>
			<p>
				<strong>Configuration name: </strong>
			<br/>
				<span id="configNameView" name="configNameView"></span>
			</p>
			<br/>
			<p>
				<strong>Template config is based on: </strong>
			<br/>
				<span id="templateNameView" name="templateNameView"></span>
			</p>
			<br/>
			<p>
				<strong>Config link: </strong>
			<br/>
				<a id="newConfigLink" name="newConfigLink" onClick="downloadNewConfigFile()"><span id="newConfigView" name="newConfigView"></span></a>
			</p>
			<br/>
			<p>
				<strong>New config filename: </strong>
			<br/>
				<span id="newConfigFileView" name="newConfigFileView"></span>
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