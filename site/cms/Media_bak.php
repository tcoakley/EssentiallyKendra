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
ValidateLogin("Media");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Media";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Media List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$MediaID = RequestInt("MediaID");
$arrMediaID = RequestArray("MediaID");

$MediaImageID = RequestInt("MediaImageID");
$arrMediaImageID = RequestArray("MediaImageID");

$arrSortMediaImageID = RequestArray("SortMediaImageID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Media", "Media.php?PageFunction=Add+Media", "Media");
$cQuickNav->AddNav("Media List", "Media.php?PageFunction=Media+List", "Media");
$cQuickNav->AddNav("Media Sort", "Media.php?PageFunction=Media+Sort", "Media");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Media
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Media" || $PageFunction == "Modify Media" || $PageFunction == "Modify Spanish") {
		$cMedia = new cMedia();
		$cMedia->LoadFieldsFromForm();
		$cMedia->SetAutoCrop($AutoCrop);
		if ($MediaID > 0) {
			$cMedia->SetTableID($MediaID);
			if ($cMedia->ModifyRecord()) {
				AddIncomingMessage("Media modified");
			}
		} else {
			$MediaID = $cMedia->InsertRecord();
			if ($MediaID) {
				AddIncomingMessage("Media created");
			}
		}
		if ($AddAnother) {
			RedirectPage("Media.php?PageFunction=Add+Media");
		} else {
			if ($PageFunction == "Modify Spanish") {
				RedirectPage("Media.php?PageFunction=Modify+Spanish&MediaID=$MediaID");
			} else {
				RedirectPage("Media.php?PageFunction=Modify+Media&MediaID=$MediaID");
			}
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Sort Media
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Media Sort") {
		$DisplayOrder = count($arrMediaID) * 10;
		for ($looper = 0; $looper < count($arrMediaID); $looper ++) {
			$MediaID = $arrMediaID[$looper];
			$sqlSelect = "update cms_media set DisplayOrder = $DisplayOrder where MediaID = $MediaID";
			ExecuteQuery($sqlSelect);
			$DisplayOrder -= 10;
		}
		AddIncomingMessage("Display Order has been updated.");
		RedirectPage("Media.php?PageFunction=Media+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Media Image Sort
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Media Image Sort") {
		$DisplayOrder = count($arrSortMediaImageID) * 10;
		//debug("arrSortMediaImageID", $arrSortMediaImageID);
		for ($looper = 0; $looper < count($arrSortMediaImageID); $looper ++) {
			$MediaImageID = $arrSortMediaImageID[$looper];
			$sqlSelect = "update cms_mediaimages set DisplayOrder = $DisplayOrder where MediaImageID = $MediaImageID";
			//debug("sqlSelect", $sqlSelect);
			ExecuteQuery($sqlSelect);
			$DisplayOrder -= 10;
		}
		//debug("DisplayOrder", $DisplayOrder, true);
		AddIncomingMessage("Display Order has been updated.");
		RedirectPage("Media.php?PageFunction=Media+Images&MediaID=$MediaID");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Media
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Media") {
		$cMedia = new cMedia();
		$cMedia->DeleteRecord($arrMediaID);
		RedirectPage("Media.php?PageFunction=Media+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Media Image
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Media Image") {
		$cMediaImages = new cMediaImages();
		$cMediaImages->DeleteRecord($arrMediaImageID);
		RedirectPage("Media.php?PageFunction=Media+Images&MediaID=$MediaID");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Edit Images
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Edit Image") {
		$cMediaImages = new cMediaImages($MediaImageID);
		$cMediaImages->LoadFieldsFromForm();
		$cMediaImages->SetAutoCrop($AutoCrop);
		if ($cMediaImages->ModifyRecord()) {
			AddIncomingMessage($ImageName . " modified");
		}
		RedirectPage("Media.php?PageFunction=Media+Images&MediaID=$MediaID");
		
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add Images
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Images") {
		$arrImages = $_FILES['Image'];
		$arrThumbs = $_FILES['Thumb'];
		$DisplayOrder = 0;
		for($looper = 0; $looper < count($arrImages); $looper++) {
			$DisplayOrder += 10;
			$ImageName = $arrImages["name"][$looper];
			$ImageTempName = $arrImages["tmp_name"][$looper];
			$ThumbName = $arrThumbs["name"][$looper];
			$ThumbTempName = $arrThumbs["tmp_name"][$looper];
			$Type = $arrImages["type"][$looper];
			$Size = $arrImages["size"][$looper];
			
			/*
			debug("--------Name------------", $Name);
			debug("ImageName", $ImageName);
			debug("ImageTempName", $ImageTempName);
			debug("ThumbName", $ThumbName);
			debug("ThumbTempName", $ThumbTempName);
			debug("Type", $Type);
			debug("Size", $Size);
			*/
			
			if (strlen($ImageName) > 3) {
				$cMediaImages = new cMediaImages();
				$cMediaImages->SetAutoCrop($AutoCrop);
				$cMediaImages->AddFieldValue("MediaID", $MediaID);
				$cMediaImages->AddFieldValue("DisplayOrder", $DisplayOrder);
				$cMediaImages->AddFieldValue("Image", $ImageName, $ImageTempName);
				$cMediaImages->AddFieldValue("Thumb", $ThumbName, $ThumbTempName);
				if ($cMediaImages->InsertRecord()) {
					AddIncomingMessage($ImageName . " added");
				}
			}
		}
		RedirectPage("Media.php?PageFunction=Media+Images&MediaID=$MediaID");


	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Media History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Media":
	case "Modify Media":
		$OnPageLoad = "";
		break;
	
	default:
		$OnPageLoad = ""; 
		break;
}
$JavaLibraries = "milkbox.js,MooDate007.js,MooCalendar007.js"; // Add any java libraries you want  here  
?>
<?php require_once("_includes/OpenPage.php"); ?>
<?php require_once("_includes/PageHeader.php"); ?>
<?php require_once("_includes/MainNav.php"); ?>
<?php $cQuickNav->DisplayNav(); ?>
<?php require_once("_includes/Messages.php"); ?>

<!-- Content area -->
<div id="ContentDiv">

	<?php
		$cTabTable = new cTabTable(120);
		if ($PageFunction == "Add Media") {
			$cTabTable->AddTab("Media", "Add Media");
		} else {
			$cTabTable->AddTab("English Media", "Modify Media", "MediaID=$MediaID");
			$cTabTable->AddTab("Spanish Media", "Modify Spanish", "MediaID=$MediaID");
			$cTabTable->AddTab("Add Images", "Add Images", "MediaID=$MediaID");
			if (!is_null($MediaImageID) && $MediaImageID > 0) {
				$cTabTable->AddTab("Edit Image", "Edit Image", "MediaID=$MediaID");
			}
			$cTabTable->AddTab("Images", "Media Images", "MediaID=$MediaID");
			$cTabTable->AddTab("History", "Media History", "MediaID=$MediaID");
		}
		
		
		switch ($PageFunction) {
			case "Add Media":
				$cTabTable->DisplayTabs("DisplayMediaForm");
				break;
			
			case "Modify Media":
				$cTabTable->DisplayTabs("DisplayMediaForm");
				break;
				
			case "Modify Spanish":
				$cTabTable->DisplayTabs("DisplaySpanishMediaForm");
				break;
				
			case "Media Sort":
				$cTabTable->DisplayTabs("DisplayMediaSort");
				break;
				
			case "Add Images":
				$cTabTable->DisplayTabs("DisplayImagesForm");
				break;
				
			case "Edit Image":
				$cTabTable->DisplayTabs("DisplayEditImageForm");
				break;
				
			case "Media Images":
				$cTabTable->DisplayTabs("DisplayImagesList");
				break;
				
			case "Media List":
				DisplayMediaList();
				break;
				
			case "Media History":
				$cTabTable->DisplayTabs("DisplayMediaHistory");
				break;
		}
	?>

</div>
<!-- /Content area -->


<?php require_once("_includes/ClosePage.php"); ?>

<?php
// #################################################################################
//	Functions
// #################################################################################


// ---------------------------------------------------------------------------------
//	DisplayMediaForm
// ---------------------------------------------------------------------------------
function DisplayMediaForm() {
	global $MediaID, $PageFunction;
	if ($MediaID > 0) {
		$cMedia = new cMedia($MediaID);
	} else {
		$cMedia = new cMedia();
		$cMedia->MediaDate = GetTime();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="MediaID" value="<?php print $MediaID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($MediaID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $MediaID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="enTitle" value="<?php print $cMedia->enTitle ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField" style="width: 550px;">
				<?php
				$oFCKeditor = new FCKeditor("enDescription");
				$oFCKeditor->BasePath = "fckeditor/";
				$oFCKeditor->Value = $cMedia->enDescription;
				$oFCKeditor->Height = 300;
				$oFCKeditor->Create();
				?>			
			</div>
			<div class="clear"></div>

			<div class="FormTitle">Video</div>
			<div class="FormField"><?php DisplayVideoSelect($cMedia->VideoFile); ?></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Video Embed</div>
			<div class="FormField"><textarea name="Video" rows="5" style="width: 350px;"><?php print $cMedia->Video?></textarea></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Video Thumb<br>641x390</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="VideoThumb"></div>
			<div class="FloatLeft" style="padding: 8px 0 0 0;"><input type="checkbox" name="AutoCrop" value="1" checked> &nbsp;Check to Auto Crop</div>
			<div class="clear"></div>
			
			<?php if (strlen($cMedia->VideoThumb) > 4) { ?>
				<div class="FormTitle">Current Video Thumb</div>
				<div class="FormField"><?php $cMedia->DisplayImageLink("VideoThumb", "PageFunction=Modify+Media&MediaID=$MediaID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<div class="FormTitle">Thumb<br>70 x 53</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="PrimaryThumb"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cMedia->PrimaryThumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cMedia->DisplayImageLink("PrimaryThumb", "PageFunction=Modify+Media&MediaID=$MediaID");?></div>
				<div class="clear"></div>
			<?php } ?>
			

			
			<?php if ($MediaID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cMedia->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cMedia->ModifiedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Views</div>
				<div class="FormField"><?php print number_format($cMedia->Views)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($MediaID > 0) { ?>
				<div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
			<?php } else { ?>
				<div class="FormField"><input type="submit" onClick="SubmitAdd();" class="button" value="Save & Add" title="Save and Add Another" /></div>
			<?php } ?>
			<div class="clear"></div>
		</form>


		
		<script type="text/javascript" src="_js/SmartHover.js"></script>
		<script type="text/javascript" src="_js/MooValidator007.js"></script>
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			var Submitting = false;
			frm.enTitle.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Media?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Media";
					frm.submit();
				}
			}
			
			function SubmitAdd() {
				frm.AddAnother.value = "1";
			}


		</script>
	</div>
	<?php
}


// ---------------------------------------------------------------------------------
//	DisplaySpanishMediaForm
// ---------------------------------------------------------------------------------
function DisplaySpanishMediaForm() {
	global $MediaID, $PageFunction;
	if ($MediaID > 0) {
		$cMedia = new cMedia($MediaID);
	} else {
		$cMedia = new cMedia();
		$cMedia->MediaDate = GetTime();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="MediaID" value="<?php print $MediaID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($MediaID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $MediaID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="esTitle" value="<?php print $cMedia->esTitle ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField" style="width: 550px;">
				<?php
				$oFCKeditor = new FCKeditor("esDescription");
				$oFCKeditor->BasePath = "fckeditor/";
				$oFCKeditor->Value = $cMedia->esDescription;
				$oFCKeditor->Height = 300;
				$oFCKeditor->Create();
				?>			
			</div>
			<div class="clear"></div>

			<div class="FormTitle">Video</div>
			<div class="FormField"><?php DisplayVideoSelect($cMedia->VideoFile); ?></div>
			<div class="clear"></div>
			
			
			<div class="FormTitle">Video Embed</div>
			<div class="FormField"><textarea name="Video" rows="5" style="width: 350px;"><?php print $cMedia->Video?></textarea></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Video Thumb<br>641x390</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="VideoThumb"></div>
			<div class="FloatLeft" style="padding: 8px 0 0 0;"><input type="checkbox" name="AutoCrop" value="1" checked> &nbsp;Check to Auto Crop</div>
			<div class="clear"></div>
			
			<?php if (strlen($cMedia->VideoThumb) > 4) { ?>
				<div class="FormTitle">Current Video Thumb</div>
				<div class="FormField"><?php $cMedia->DisplayImageLink("VideoThumb", "PageFunction=Modify+Media&MediaID=$MediaID");?></div>
				<div class="clear"></div>
			<?php } ?>			
			
			<div class="FormTitle">Thumb<br>70 x 53</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="PrimaryThumb"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cMedia->PrimaryThumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cMedia->DisplayImageLink("PrimaryThumb", "PageFunction=Modify+Media&MediaID=$MediaID");?></div>
				<div class="clear"></div>
			<?php } ?>
			

			
			
			<?php if ($MediaID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cMedia->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cMedia->ModifiedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Views</div>
				<div class="FormField"><?php print number_format($cMedia->Views)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($MediaID > 0) { ?>
				<div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
			<?php } else { ?>
				<div class="FormField"><input type="submit" onClick="SubmitAdd();" class="button" value="Save & Add" title="Save and Add Another" /></div>
			<?php } ?>
			<div class="clear"></div>
		</form>


		
		<script type="text/javascript" src="_js/SmartHover.js"></script>
		<script type="text/javascript" src="_js/MooValidator007.js"></script>
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			var Submitting = false;
			frm.enTitle.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Media?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Media";
					frm.submit();
				}
			}
			
			function SubmitAdd() {
				frm.AddAnother.value = "1";
			}


		</script>
	</div>
	<?php
}


// ---------------------------------------------------------------------------------
//	DisplayMediaSort
// ---------------------------------------------------------------------------------
function DisplayMediaSort() {
	global $PageFunction;

	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">

			<div><strong>Drag blue ball to sort</strong></div>
			<br>
			<div>
				<ol id="MediaSort" class="SortList">
					<?php
						$sqlSelect = "select MediaID, enTitle, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_media order by DisplayOrder desc";
						$tbl = ExecuteQuery($sqlSelect);
						while ($row = mysql_fetch_object($tbl)) {
							$MediaID = $row->MediaID;
							$enTitle = $row->enTitle;
							$CreatedDate = $row->CreatedDate;
							print "<li><div class=\"DragHandle\">&nbsp;</div><div class=\"FloatLeft\">$enTitle</div><div class=\"FloatRight\">" . FormatDate($CreatedDate, "%m/%d/%y %H:%M") . "</div><div class=\"clear\"></div><input type=\"hidden\" name=\"MediaID[]\" value=\"$MediaID\"></li>\n";
						}
					?>
				</ol>
			</div>
			<div class="clear"></div>
			
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
	
			

		
		</form>
		<script type="text/javascript">
			var mySortables = new Sortables('MediaSort', {
			    revert: { duration: 500, transition: 'elastic:out' },
		  	  handle: '.DragHandle'
			});

		</script>
	</div>
	<?php
}


// ---------------------------------------------------------------------------------
//	DisplayVideoSelect
// ---------------------------------------------------------------------------------
function DisplayVideoSelect($CurrentSelection) {
	$MainPath = "../_flv/";

	$arrFD = array();
	$arrFS = array();
	$arrDD = array();

	if (is_dir($MainPath)) {
		if ($dh = opendir($MainPath)) {
			// Prepare arrays of folders/files
			while (($file = readdir($dh)) !== false) {
				$arrStats = stat($MainPath . $file);
				if($file != "." && $file != "..") {
					if (filetype($MainPath . $file) == "dir") {
						$arrDD[$file] = $arrStats["mtime"];
					} else {
						if ($file != ".htaccess") {
							$arrFD[$file] = $arrStats["mtime"];
							$arrFS[$file] = $arrStats["size"];
						}
					}
				}
			}

		}
		arsort($arrFD);
	}
	if (count($arrFD) < 1) {
		print "No videos currently available.";
	} else {
		print "<select name=\"VideoFile\" style=\"width: 250px;\">\n" . 
			"<option value=\"\"></option>\n";
		foreach ($arrFD as $key => $val) {
			print "<option value=\"" . $key . "\"";
			if ($CurrentSelection == $key) {
				print " selected";
			}
			print ">" . $key . "</option>\n";
		}
		print "</select>";
	}

}


// ---------------------------------------------------------------------------------
//	DisplayEditImageForm
// ---------------------------------------------------------------------------------
function DisplayEditImageForm() {
	global $PageFunction, $MediaID, $MediaImageID;
	$cMediaImages = new cMediaImages($MediaImageID);
	//debug("MediaImageID", $MediaImageID);
	$Thumb = "<img src=\"../_uploads/media/Thumb$MediaImageID."  . GetFileExtension($cMediaImages->Thumb) . "\" style=\"margin: 5px; border: 0;\">";
	$ImagePath = "_uploads/media/Image$MediaImageID."  . GetFileExtension($cMediaImages->Image);
?>
	<style>
		.ImageCanister {
			margin: 0 0 4px 0;
			padding: 0 0 4px 0;
			border: 0;
			border-bottom: 1px solid #ccc;
			width: 600px;
		}
	</style>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="MediaID" value="<?php print $MediaID?>">
			<input type="hidden" name="MediaImageID" value="<?php print $MediaImageID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<div style="margin: 15px;">
				<strong><?php print $cMediaImages->Image; ?><br></strong>
				<a href="/<?php print $ImagePath?>" rel="milkbox[gal1]" title=""><?php print $Thumb ?> </a>
				<div class="clear"></div>
			</div>
			<div class="FloatLeft"><input type="checkbox" name="AutoCrop" value="1">&nbsp;</div>
			<div class="FloatLeft">&nbsp; Check to have the thumbnail automatically cropped to the correct size</div>
			<div class="clear" style="height: 20px;"></div>
			
			<div class="FloatLeft">Note: The thumb file already exists so you it will not automatically copy the Image to the thumb.</div>
			<div class="clear" style="height: 20px;"></div>

			<div style="height: 25px;">
				<div class="FloatLeft" style="width: 300px;"><strong>Image</strong></div>
				<div class="FloatLeft" style="width: 300px;"><strong>Thumb [70 x 53]</strong></div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb">
				</div>
				<div class="clear"></div>
			</div>
			<div style="padding: 15px 0 0 0;"><input type="submit" class="button" value="Save" /></div>
		</form>
	</div>
<?php
}

// ---------------------------------------------------------------------------------
//	DisplayImagesForm
// ---------------------------------------------------------------------------------
function DisplayImagesForm() {
	global $PageFunction, $MediaID;
?>
	<style>
		.ImageCanister {
			margin: 0 0 4px 0;
			padding: 0 0 4px 0;
			border: 0;
			border-bottom: 1px solid #ccc;
			width: 600px;
		}
	</style>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="MediaID" value="<?php print $MediaID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<div style="height: 30px;">If thumb is not uploaded the full image will be used to create a thumb.</div>
			<div class="FloatLeft"><input type="checkbox" name="AutoCrop" value="1">&nbsp;</div>
			<div class="FloatLeft">&nbsp; Check to have the thumbnail automatically cropped to the correct size</div>
			<div class="clear" style="height: 20px;"></div>
			<div style="height: 25px;">
				<div class="FloatLeft" style="width: 300px;"><strong>Image</strong></div>
				<div class="FloatLeft" style="width: 300px;"><strong>Thumb [70 x 53]</strong></div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image[]">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb[]">
				</div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image[]">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb[]">
				</div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image[]">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb[]">
				</div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image[]">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb[]">
				</div>
				<div class="clear"></div>
			</div>
			<div class="ImageCanister">
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Image[]">
				</div>
				<div class="FloatLeft" style="width: 300px;">
					<input type="file" style="width: 250px;" name="Thumb[]">
				</div>
				<div class="clear"></div>
			</div>
			<div style="padding: 15px 0 0 0;"><input type="submit" class="button" value="Save" /></div>

			
		</form>
	</div>
<?php
}

// ---------------------------------------------------------------------------------
//	DisplayImageList
// ---------------------------------------------------------------------------------
function DisplayImagesList() {
	global $PageFunction, $MediaID;
	?>
	<style>
		.ImageSort {
			list-style-type: none;
			margin-left: 0px;
			padding-left 0px;
			list-style-type: none;
		}
		.ImageSort li {
			background-color: #fff;
			margin: 0 0 2px 0;
			padding: 0 10px 0 10px;
			width: 690px;
			height: 34px;
			line-height: 34px;
			border-top: 1px solid #ccc;
			border-left:  1px solid #ccc;
			border-right: 1px solid #333;
			border-bottom: 1px solid #333;		
		}
		
		.ImageSort li .Thumb {
			float: left;
			display: inline;
			width: 45px;
			height: 34px;
			overflow: hidden;
			margin: 0 5px 0 0;
		}
		.ImageSort li .Thumb img {
			border: 0;
			padding: 0;
			margin: 0;
			height: 34px;
			
		}
		.ImageSort li .Name {
			float: left;
			display: inline;
			width: 200px;
			overflow: hidden;
			margin: 0 5px 0 0;
		}
		.ImageSort li .Dimensions {
			float: left;
			display: inline;
			width: 80px;
			margin: 0 5px 0 0;
		}
		.ImageSort li .ThumbDimensions {
			float: left;
			display: inline;
			width: 80px;
			margin: 0 5px 0 0;
		}
		.ImageSort li .Datestamp {
			float: left;
			display: inline;
			width: 120px;
			margin: 0 5px 0 0;
		}
		.ImageSort li .Divider {
			float: left;
			display: block;
			background-color: #333;
			width: 2px;
			margin: 0 5px 0 0;
			border: 0;
			padding: 0;
			height: 34px;
		}
		.ThumbHover {
			display: none;
			background-color: #ccc;
			border: 1px solid #000;
			width: 70px;
			height: 53px;
		}
		.ThumbHover img {
			padding: 0;
			border: 0;
			margin: 0;
			width: 70px;
			height: 53px;
		}
	</style>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Media.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="MediaID" value="<?php print $MediaID?>">	
			<div><strong>
				Drag blue ball to sort. Click the save button to save the new sort order.
				<br>
				To delete check the boxes of the ones to delete and click the red X at the column top.
			</strong></div>
			<br>
			<div>
				<ul id="ImageSort" class="ImageSort">
					<li>
						<div class="FloatLeft" style="width: 70px;">&nbsp;</div>
						<div class="Divider"></div>
						<div class="Name"><strong>Name</strong></div>
						<div class="Divider"></div>
						<div class="Dimensions"><strong>Dimensions</strong></div>
						<div class="Divider"></div>
						<div class="ThumbDimensions"><strong>Thumb</strong></div>
						<div class="Divider"></div>
						<div class="Datestamp"><strong>Created</strong></div>
						<div class="Divider"></div>
						<div class="FloatLeft">
							<div class="FloatLeft"><strong>Tools</strong></div>
							<div class="FloatLeft" style="padding: 5px 0 0 30px; height: 28px;"><a href="javascript:;" onClick="ConfirmDelete();return false;"><img src="_img/b_drop.png" style="border: 0;"></a></div>
						</div>
					</li>
				<?php
					$sqlSelect = "select MediaImageID, Image, Thumb, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_mediaimages where MediaID = $MediaID order by DisplayOrder desc";
					$tbl = ExecuteQuery($sqlSelect);
					while ($row = mysql_fetch_object($tbl)) {
						$MediaImageID = $row->MediaImageID;
						$Image = $row->Image;
						$Thumb = $row->Thumb;
						$CreatedDate = $row->CreatedDate;
						$ImagePath = "_uploads/media/Image$MediaImageID."  . GetFileExtension($Image);
						$ThumbPath = "_uploads/media/Thumb$MediaImageID."  . GetFileExtension($Thumb);
						list($ImageWidth, $ImageHeight, $type, $attr) = getimagesize(GetWebRoot() . $ImagePath);	
						list($ThumbWidth, $ThumbHeight, $type, $attr) = getimagesize(GetWebRoot() . $ThumbPath);
						if ($ThumbWidth > 70 || $ThumbHeight > 53) { 
							$CropLink = "<a href=\"CropTool.php?FileName=" . urlencode("../$ThumbPath") .
							"&DestFile="  . urlencode("../$ThumbPath") .
							"&CropWidth=70&CropHeight=53\">";
							$ThumbDimensions = "<span class=\"Warning\">$ThumbWidth x $ThumbHeight</span>";
						} else {
							$CropLink = "<a href=\"CropTool.php?FileName=" . urlencode("../$ImagePath") .
							"&DestFile="  . urlencode("../$ThumbPath") .
							"&CropWidth=70&CropHeight=53\">";
							$ThumbDimensions = "$ThumbWidth x $ThumbHeight";
						}
						$_SESSION["ReturnPage"] = "Media.php?MediaID=$MediaID&PageFunction=" . urlencode($PageFunction);
						print "<li>" .
								"<div class=\"DragHandle\">&nbsp;</div>\n" .
								"<div class=\"Thumb\" id=\"thumb$MediaImageID\"><a href=\"/$ImagePath\" rel=\"milkbox[gal1]\" title=\"\"><img src=\"/$ThumbPath?" . CacheBuster() . "\"></a></div>\n" .
								"<div class=\"ThumbHover\" id=\"thumb" . $MediaImageID . "_smarthbox\"><img src=\"/$ThumbPath?" . CacheBuster() . "\">\n</div>" .
								"<div class=\"Divider\"></div>\n" .
								"<div class=\"Name\" title=\"$Image\">$Image</div>\n" .
								"<div class=\"Divider\"></div>\n" .
								"<div class=\"Dimensions\">$ImageWidth x $ImageHeight</div>\n" .
								"<div class=\"Divider\"></div>\n" .
								"<div class=\"ThumbDimensions\">$ThumbDimensions</div>\n" .
								"<div class=\"Divider\"></div>\n" .
								"<div class=\"Datestamp\">" . FormatDate($CreatedDate) . "</div>\n" .
								"<div class=\"Divider\"></div>\n" .
								"<div class=\"FloatLeft\">" .
									"<div class=\"FloatLeft\">$CropLink<img src=\"_img/b_crop.png\" style=\"padding: 8px 15px 0 5px;border: 0;\"></a></div>" .
									"<div class=\"FloatLeft\"><a href=\"Media.php?MediaID=$MediaID&MediaImageID=$MediaImageID&PageFunction=Edit+Image\"><img src=\"_img/b_edit.png\" style=\"padding: 5px 15px 0 0;border: 0;\"></a></div>" .
									"<div class=\"FloatRight\" style=\"padding: 8px 0 0 0;height: 28px;\"><input type=\"checkbox\" name=\"MediaImageID[]\" value=\"$MediaImageID\"></div>" .
									"<input type=\"hidden\" name=\"SortMediaImageID[]\" value=\"" . $MediaImageID . "\">\n" .
								"</div>" .
							"</li>\n";
					}
				?>
				</ul>
			</div>
			<div class="clear"></div>
			
			<div class="FloatLeft" style="width: 35px;">&nbsp;</div>
			<div class="FormField"><input type="button" class="button" onClick="ChangeSort(); return false;" value="Save" /></div>
			<div class="clear"></div>
	
			<div style="padding: 5px 5px 0 35px;">
				Dimenions in <span class="Warning">red</span> will crop the thumb file.
				If the thumb file is not in red the full size image will be used to crop a new thumb.
			</div>

		
		</form>
		<script type="text/javascript" src="_js/SmartHover.js"></script>
		<script type="text/javascript">
		
		window.addEvent('domready', 
			function() {
				var mySortables = new Sortables('ImageSort', {
				    revert: { duration: 500, transition: 'elastic:out' },
				  handle: '.DragHandle'
				});
				smartHoverBox(
					   0, //delay before vanishing
					   45, //x offset
					   0,  //y offset
					   '_smarthbox', //smart hover box suffix
					   'smarthbox_close' //hover box close class
				  );

				
			}
		);

		function ConfirmDelete() {
			var frm = document.forms["frmMain"];
			IsConfirmed = confirm('Are you certain you wish to delete the selected images?');
			if (IsConfirmed) {
				frm.PageFunction.value = "Delete Media Image";
				frm.FormComplete.value = 1;
				frm.submit();
			}
		}

		function ChangeSort() {
			var frm = document.forms["frmMain"];
			frm.PageFunction.value = "Media Image Sort";
			frm.FormComplete.value = 1;
			frm.submit();
		}

		</script>
	</div>
	<?php
}


// ---------------------------------------------------------------------------------
//	DisplayMediaList
// ---------------------------------------------------------------------------------
function DisplayMediaList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select MediaID, enTitle, DisplayOrder, UNIX_TIMESTAMP(CreatedDate) as CreatedDate, UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate" .
		" from cms_media media";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Media");
	$cDataGrid->SetSelectFunction("Delete Media");
	$cDataGrid->SetModifyFunction("Modify Media");
	$cDataGrid->SetPrimaryKey("MediaID");
	$cDataGrid->SetSortBy("DisplayOrder");
	$cDataGrid->SetSortDirection("desc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Media? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Media? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("ID", "MediaID", null, "int");
	$cDataGrid->AddColumn("Title", "enTitle", null, "string");
	$cDataGrid->AddColumn("Order", "DisplayOrder", null, "int");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");
	$cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayMediaHistory
// ---------------------------------------------------------------------------------
function DisplayMediaHistory() {
	global $MediaID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("MediaID");
	$cHistoryDisplay->SetTabValue($MediaID);
	
	$cHistoryDisplay->SetTableName("cms_media");
	$cHistoryDisplay->SetTableID($MediaID);
	$cHistoryDisplay->DisplayHistory();
}
?>