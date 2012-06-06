<?php
// #################################################################################
//	require_onces
// #################################################################################
require_once("_includes/constants.php");
require_once("_includes/functions.php");
require_once("_includes/functions_site.php");

// #################################################################################
//	Validation
// #################################################################################
ValidateLogin("Welcome");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Crop Tool";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Crop Image");



// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {
	
	// Crop Image
	$FileName = RequestString("FileName");
	$DestFile = RequestString("DestFile");
	$CropWidth = RequestInt("CropWidth");
	$CropHeight = RequestInt("CropHeight");
	$Top = RequestInt("Top");
	$Left = RequestInt("Left");
	$Width = RequestInt("Width");
	$Height = RequestInt("Height");
	

	
	
	$arrFileName = explode('.', $FileName);
	$extension = array_pop($arrFileName);
	
	$FileName = $FileName;
	$DestFile = $DestFile;
	//debug("FileName", $FileName, true);
	switch(strtolower($extension)) {
		case "gif":
			$objImage = imagecreatefromgif($FileName);
			break;
		case "png":
			$objImage = imagecreatefrompng($FileName);
			break;
		default:
			$objImage = imagecreatefromjpeg($FileName);
			break;
	}
	//debug("objImage", $objImage);
	
	if($Width > $Height) {
		$DestWidth = $CropWidth;
		$DestHeight = round($CropHeight * $Height / $Width);
	} else {
		$DestWidth = round($CropWidth * $Width / $Height);
		$DestHeight = $CropHeight;
	}
	//debug("DestWidth", $DestWidth);
	//debug("DestHeight", $DestHeight);
	
	$DestImage = imagecreatetruecolor($CropWidth, $CropHeight);
	imagecopyresampled($DestImage, $objImage, 0, 0, $Left, $Top, $CropWidth, $CropHeight, $Width, $Height);	
	$arrFileName = explode('.', $DestFile);
	$extension = array_pop($arrFileName);
	switch(strtolower($extension)) {
		case "gif":
			imagegif($DestImage, $DestFile);
			break;
		case "png":
			imagepng($DestImage, $DestFile,100);
			break;
		default:
			imagejpeg($DestImage,$DestFile,100);
			break;
	}
	/*
	debug("FileName", $FileName);
	debug("DestFile", $DestFile);
	debug("CropWidth", $CropWidth);
	debug("CropHeight", $CropHeight);
	debug("Top", $Top);
	debug("Left", $Left);
	debug("Width", $Width);
	debug("Height", $Height);
	debug("FileName", $FileName);
	debug("DestFile", $DestFile);
	debug("DestImage", $DestImage,true);
	*/
	AddIncomingMessage("Image cropped");
	RedirectPage($_SESSION["ReturnPage"]);

}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "crop/uvumi-crop.css"; 
$OnPageLoad = "InitCrop()"; 
$JavaLibraries = "UvumiCrop-compressed.js";
?>
<?php require_once("_includes/OpenPage.php"); ?>
<?php require_once("_includes/PageHeader.php"); ?>
<?php require_once("_includes/MainNav.php"); ?>
<?php //$cQuickNav->DisplayNav(); ?>
<?php require_once("_includes/Messages.php"); ?>

<!-- Content area -->
<div id="ContentDiv">

	<?php
		$cTabTable = new cTabTable(120);
		$cTabTable->AddTab("Crop Image", "Crop Image");
		$cTabTable->DisplayTabs("DisplayCropForm");
		
	?>

</div>
<!-- /Content area -->


<?php require_once("_includes/ClosePage.php"); ?>

<?php
// #################################################################################
//	Functions
// #################################################################################


// ---------------------------------------------------------------------------------
//	DisplayCropForm
// ---------------------------------------------------------------------------------
function DisplayCropForm() {
	$FileName = RequestString("FileName");
	$CropWidth = RequestInt("CropWidth");
	$CropHeight = RequestInt("CropHeight");
	$DestFile = RequestString("DestFile");
	DefaultString($DestFile, $FileName);
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="CropTool.php" enctype="multipart/form-data">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="FileName" value="<?php print $FileName?>">
			<input type="hidden" name="DestFile" value="<?php print $DestFile?>">
			<input type="hidden" name="CropWidth" value="<?php print $CropWidth?>">
			<input type="hidden" name="CropHeight" value="<?php print $CropHeight?>">
			<input type="hidden" id="Top" name="Top" value="">
			<input type="hidden" id="Left" name="Left" value="">
			<input type="hidden" id="Width" name="Width" value="">
			<input type="hidden" id="Height" name="Height" value="">
			
			<div style="text-align: center">
				<h1>Preview</h1>
				<div style="width: <?php print $CropWidth?>px;height: <?php print $CropHeight?>px;">
					<div id="ImagePreview"></div>
				</div>
				<p>
				<div class="FloatLeft" style="margin: 0 20px 0 20px;">
					<input type="submit" class="button" value="Save" />
				</div>
				<div class="FloatLeft">
					<input type="button" class="button" value="Cancel" onClick="Return();" />
				</div>
				 <p>
				 <h1>Original Image</h1>
				 <img src="<?php print $FileName . "?" . CacheBuster() ?>" id="CropImage">
			</div>

		</form>
	</div>
	<script type="text/javascript">
		function Return() {
			document.location.href = "<?php print $_SESSION['ReturnPage']?>";
		}
		function InitCrop() {
			new uvumiCropper('CropImage',{
				keepRatio:true,
				resizable:true,
				preview: "ImagePreview",
				mini: {
					x: <?php print $CropWidth ?>,
					y: <?php print $CropHeight ?>
				},
				onComplete:function(top,left,width,height){
					$('Top').set('value', top);
					$('Left').set('value', left);
					$('Width').set('value', width);
					$('Height').set('value', height);
				} 
			}); 
		}
	</script>
	<?php
}


?>