<?php
// #################################################################################
//	Includes
// #################################################################################
require_once("_includes/constants.php");
require_once("_includes/functions.php");
require_once("_includes/functions_site.php");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Password Reminder";

$Email = RequestString("Email");
$FormComplete = RequestBool("FormComplete");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {
	$sqlSelect = "Select UserName, Password, FirstName, LastName from cms_users where Email = '" . mysql_escape_string($Email) . "'";
	$tbl = ExecuteQuery($sqlSelect);
	if(!$tbl) {
		AddErrorMessage("No accounts found for the email address '$Email'");
		RedirectPage("ForgotPassword.php");
	} else {
		while ($row = mysql_fetch_object($tbl)) {
			$cEmails = new cEmails();
			$cEmails->AddReplacementField("UserName", $row->UserName);
			$cEmails->AddReplacementField("FirstName", $row->FirstName);
			$cEmails->AddReplacementField("LastName", $row->LastName);
			$cEmails->AddReplacementField("Password", $row->Password);
			$cEmails->SendEmail("Forgot Password", $Email, $row->FirstName . " " . $row->LastName);
		}
		AddIncomingMessage("Your account information has been sent to your email address.");
		RedirectPage("Login.php");
	}


}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "login.css";  // Add any additional style sheets you want included here
$OnPageLoad = "";	// Add any javascript you want executed on page load/domready here
$JavaLibraries = ""; // Add any java libraries you want included here
?>
<?php include("_includes/OpenPage.php"); ?>
<?php require_once("_includes/PageHeader.php"); ?>



<!-- Content area -->
<div id="LoginDiv">
	<?php include("_includes/Messages.php"); ?>
	<div class="info">
		Please enter the email address you signed up with in the field below. Your Username and Password
		will be emailed to you.
	</div>
	<div id="FormCanisterMaster">
		<form name="frmPassword" method="post" action="ForgotPassword.php">
			<input type="hidden" name="FormComplete" value="1">
			<div class="FormTitle">Email</div>
			<div class="FormField"><input type="text" class="req" alt="em-0" name="Email" ></div>
			<div class="clear"></div>

			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Submit" /></div>
			<div class="clear"></div>

		</form>
	</div>
	<div>
		<a href="Login.php">Return to login form</a>
	</div>
	<script type="text/javascript" src="_js/SmartHover.js"></script>
	<script type="text/javascript" src="_js/MooValidator007.js"></script>
	<script type="text/javascript">
		var frm = document.forms["frmPassword"];
		frm.Email.focus();
	</script>
	
	<div class="clear"></div>
</div>
<!-- /Content area -->


<?php include("_includes/ClosePage.php"); ?>