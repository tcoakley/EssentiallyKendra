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
$PageTitle = "Login";

$UserName = RequestString("UserName");
$Password = RequestString("Password");
$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {
	$cUsers = new cUsers();
	if ($cUsers->Login($UserName, $Password)) {
		RedirectPage("Home.php");
	} else {
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

<style>

</style>


<!-- Content area -->
<div id="LoginDiv">
	<?php include("_includes/Messages.php"); ?>
	<div class="info">
		Please enter your Username and Password in the fields below to log into this secure system.
		If you have forgotten your credentials use the Forgot Password link to have them emailed
		to you.
	</div>
	<div id="FormCanisterMaster">
		<form name="frmLogin" method="post" action="Login.php">
			<input type="hidden" name="FormComplete" value="1" />
			<div class="FormTitle">Username</div>
			<div class="FormField"><input type="text" name="UserName" value="<?php print HtmlPrepare($UserName)?>" /></div>
			<div class="clear"></div>

			<div class="FormTitle">Password</div>
			<div class="FormField"><input type="password" name="Password" /></div>
			<div class="clear"></div>

			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Login"></div>
			<div class="clear"></div>

			<div class="forgot">
				<a href="ForgotPassword.php">Forgot Password?</a>
			<div>
		</form>
	</div>
	<script type="text/javascript">
		var frm = document.forms["frmLogin"];
		frm.UserName.focus();
	</script>
	
	<div class="clear"></div>
</div>
<!-- /Content area -->


<?php include("_includes/ClosePage.php"); ?>