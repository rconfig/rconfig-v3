	<!-- Head Include -->    
	<?php include("includes/head_login.inc.php"); ?>
	<body>
		<div id="forgotPasswordForm" class="myform stylizedForm stylized">
			<?php
				/**
				 * Forgot Password form has been submitted and no errors
				 * were found with the form (the username is in the database)
				 */
				if(isset($_SESSION['forgotpass'])){
				   /**
					* New password was generated for user and sent to user's
					* email address.
					*/
				   if($_SESSION['forgotpass']){
					  echo "<h2>New Password Generated</h2>";
					  echo "<p>Your new password has been generated "
						  ."and sent to the email associated with your account. "
						  ."<a href=\"#\" onClick=\"javascript:window.close();\">Close</a></p>";
				      $log->Info("Success: Password generated and sent to email for account -  Error:(File: ".$_SERVER['PHP_SELF'].")");
				   }
				   /**
					* Email could not be sent, therefore password was not
					* edited in the database.
					*/
				   else{
					  echo "<h3>New Password Failure</h3>";
					  echo "<p>There was an error sending you the "
						  ."email with the new password,<br> so your password has not been changed. 
						  <br/> Please contact system administrator"
						  ."<br/><a href=\"#\" onClick=\"javascript:window.close();\">Close</a></p>";
				      $log->Fatal("Failure: Could not send password to email for account - System Mail Error - Error:(File: ".$_SERVER['PHP_SELF'].")");
				   }
					   
				   unset($_SESSION['forgotpass']);
				} else {
				
					// echo error message if is sent back in GET from lib/crud/ userproess.php
					if(isset($_SESSION['errors'])){
					// move nested errors array to new array
					$errors = $_SESSION['errors'];	
					}
					/* "Do NOT unset the whole $_SESSION with unset($_SESSION) as this will disable the registering of session variables through the $_SESSION superglobal." */
					$_SESSION['errors'] = array();
				/**
				 * Forgot password form is displayed, if error found
				 * it is displayed.
				 */
				?>

				<h1>Forgot Password</h1>
				<p>A new password will be generated for you and sent to the email address
				associated with your account, all you have to do is enter your
				username.<br><br></p>

				<form action="lib/crud/userprocess.php" method="POST">
					<div class="spacer"></div>
					<label style="font-size:0.9em">Username:
					<div class="spacer"></div>
					<?php	// echo error message if is sent back in GET from CRUD
					if(isset($errors['user'])){echo "<span class=\"error\">".$errors['user']."</span>";} 
					 else {
						echo "<span class=\"small\">Type your username</span>";
					 }
					?>
					</label> 
					<input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>" tabindex="1">
					<input type="hidden" name="subforgot" value="1">
					<button type="submit" value="Get New Password" tabindex="2" class="forgotBtn">Submit</button>
				</form>

				<?php
				}
				?>
			</div>
	</body>
</html>