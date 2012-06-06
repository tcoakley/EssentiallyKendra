<?php
// #################################################################################
//	require_onces
// #################################################################################
require_once("_includes/constants.php");
require_once("_includes/functions.php");
require_once("_includes/functions_site.php");

// #################################################################################
//	Validate Login
// #################################################################################
//ValidateUserLogin();


// #################################################################################
//	Initialization
// #################################################################################
$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
$AddAnother = RequestBool("AddAnother");
$Zip = RequestString("Zip");

// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

}


// #################################################################################
//	Display
// #################################################################################
$PageTitle = "info";
$JavaLibraries = "Moobox007.js,swfobject/swfobject.js";
$StyleSheets = "Moobox007.css";
$OnPageLoad = "";
?>
<?php require_once("_includes/OpenPage.php"); ?>
<?php require_once("_includes/PageHeader.php"); ?>
<?php require_once("_includes/MainNav.php"); ?>
<?php require_once("_includes/Messages.php"); ?>



	<!-- Content Area -->
	<div id="ContentContainer" style="background: #fff;padding:30px; min-height: 400px;">
		<!-- Messages -->
		<?php require_once("_includes/Messages.php"); ?>
		<!-- /Messages -->
		<?php
		debug("Session",$_SESSION);
		?>

		<div class="clear"></div>
	</div>
	<!-- /Content Area -->
			
<?php require_once("_includes/ClosePage.php"); ?>


<?php
// #################################################################################
//	Functions
// #################################################################################


?>