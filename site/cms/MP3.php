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
ValidateLogin("MP3");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "MP3";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Audio List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$MP3ID = RequestInt("MP3ID");
$arrMP3ID = RequestArray("MP3ID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Audio", "MP3.php?PageFunction=Add+Audio", "MP3");
$cQuickNav->AddNav("Audio List", "MP3.php?PageFunction=Audio+List", "MP3");
$cQuickNav->AddNav("Audio Sort", "MP3.php?PageFunction=MP3+Sort", "MP3");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Audio
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Audio" || $PageFunction == "Modify Audio") {
		$cMP3s = new cMP3s();
		$cMP3s->LoadFieldsFromForm();
		$cMP3s->SetAutoCrop($AutoCrop);
		if ($MP3ID > 0) {
			$cMP3s->SetTableID($MP3ID);
			if ($cMP3s->ModifyRecord()) {
				AddIncomingMessage("Audio modified");
			}
		} else {
			$MP3ID = $cMP3s->InsertRecord();
			if ($MP3ID) {
				AddIncomingMessage("Audio created");
			}
		}
		if ($AddAnother) {
			RedirectPage("MP3.php?PageFunction=Add+Audio");
		} else {
			RedirectPage("MP3.php?PageFunction=Modify+Audio&MP3ID=$MP3ID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Sort MP3
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "MP3 Sort") {
		$DisplayOrder = count($arrMP3ID) * 10;
		for ($looper = 0; $looper < count($arrMP3ID); $looper ++) {
			$MP3ID = $arrMP3ID[$looper];
			$sqlSelect = "update cms_mp3s set DisplayOrder = $DisplayOrder where MP3ID = $MP3ID";
			ExecuteQuery($sqlSelect);
			$DisplayOrder -= 10;
		}
		AddIncomingMessage("Display Order has been updated.");
		RedirectPage("MP3.php?PageFunction=Audio+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete MP3
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete MP3") {
		$cMP3s = new cMP3s();
		$cMP3s->DeleteRecord($arrMP3ID);
		RedirectPage("MP3.php?PageFunction=Audio+List");
	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Audio History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Audio":
	case "Modify Audio":
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
		if ($PageFunction == "Add Audio") {
			$cTabTable->AddTab("MP3", "Add Audio");
		} else {
			$cTabTable->AddTab("Modify Audio", "Modify Audio", "MP3ID=$MP3ID");
			$cTabTable->AddTab("History", "Audio History", "MP3ID=$MP3ID");
		}
		
		
		switch ($PageFunction) {
			case "Add Audio":
				$cTabTable->DisplayTabs("DisplayMP3Form");
				break;
			
			case "Modify Audio":
				$cTabTable->DisplayTabs("DisplayMP3Form");
				break;
				
			case "MP3 Sort":
				$cTabTable->DisplayTabs("DisplayMP3Sort");
				break;
				
			case "Audio List":
				DisplayMP3List();
				break;
				
			case "Audio History":
				$cTabTable->DisplayTabs("DisplayMP3History");
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
//	DisplayMP3Form
// ---------------------------------------------------------------------------------
function DisplayMP3Form() {
	global $MP3ID, $PageFunction;
	if ($MP3ID > 0) {
		$cMP3s = new cMP3s($MP3ID);
	} else {
		$cMP3s = new cMP3s();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MP3.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="MP3ID" value="<?php print $MP3ID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($MP3ID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $MP3ID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Title" value="<?php print $cMP3s->Title ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Active</div>
			<div class="FormField"><input type="checkbox" name="Active" value="1"<?php if ($cMP3s->Active) { print " checked"; } ?>></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Album</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Album" value="<?php print $cMP3s->Album ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Download Link</div>
			<div class="FormField"><input type="text" class="opt" alt="ml-1" style="width: 250px;" name="BuyLink" value="<?php print $cMP3s->BuyLink ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">iTunes Buy Link</div>
			<div class="FormField"><input type="text" class="opt" alt="ml-1" style="width: 250px;" name="BuyLink2" value="<?php print $cMP3s->BuyLink2 ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Audio FLV</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="MP3"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cMP3s->MP3) > 4) { ?>
				<div class="FormTitle">Current Audio</div>
				<div class="FormField"><?php print $cMP3s->MP3;?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">AutoCrop</div>
			<div class="FormField"><input type="checkbox" name="AutoCrop" value="1" checked> &nbsp;Check to Auto Crop Image and Thumb</div>
			<div class="clear"></div>
			

			<div class="FormTitle">Image<br>200x200</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Image"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cMP3s->Image) > 4) { ?>
				<div class="FormTitle">Current Image</div>
				<div class="FormField"><?php $cMP3s->DisplayImageLink("Image", "PageFunction=Modify+Audio&MP3ID=$MP3ID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Thumb<br>75 x 75</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Thumb"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cMP3s->Thumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cMP3s->DisplayImageLink("Thumb", "PageFunction=Modify+Audio&MP3ID=$MP3ID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<?php if ($MP3ID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cMP3s->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cMP3s->ModifiedDate)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($MP3ID > 0) { ?>
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
			frm.Title.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this MP3?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete MP3";
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
//	DisplayMP3Sort
// ---------------------------------------------------------------------------------
function DisplayMP3Sort() {
	global $PageFunction;

	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MP3.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">

			<div><strong>Drag blue ball to sort</strong></div>
			<br>
			<div>
				<ol id="MediaSort" class="SortList">
					<?php
						$sqlSelect = "select MP3ID, Title, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_mp3s order by DisplayOrder desc";
						$tbl = ExecuteQuery($sqlSelect);
						while ($row = mysql_fetch_object($tbl)) {
							$MP3ID = $row->MP3ID;
							$Title = $row->Title;
							$CreatedDate = $row->CreatedDate;
							print "<li><div class=\"DragHandle\">&nbsp;</div><div class=\"FloatLeft\">$Title</div><div class=\"FloatRight\">" . FormatDate($CreatedDate, "%m/%d/%y %H:%M") . "</div><div class=\"clear\"></div><input type=\"hidden\" name=\"MP3ID[]\" value=\"$MP3ID\"></li>\n";
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
//	DisplayMP3List
// ---------------------------------------------------------------------------------
function DisplayMP3List() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select MP3ID, Title, Album, DisplayOrder, UNIX_TIMESTAMP(CreatedDate) as CreatedDate" .
		" from cms_mp3s";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete MP3");
	$cDataGrid->SetSelectFunction("Delete MP3");
	$cDataGrid->SetModifyFunction("Modify Audio");
	$cDataGrid->SetPrimaryKey("MP3ID");
	$cDataGrid->SetSortBy("DisplayOrder");
	$cDataGrid->SetSortDirection("desc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected MP3? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this MP3? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("ID", "MP3ID", null, "int");
	$cDataGrid->AddColumn("Title", "Title", null, "string");
	$cDataGrid->AddColumn("Album", "Album", null, "string");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayMP3History
// ---------------------------------------------------------------------------------
function DisplayMP3History() {
	global $MP3ID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("MP3ID");
	$cHistoryDisplay->SetTabValue($MP3ID);
	
	$cHistoryDisplay->SetTableName("cms_mp3");
	$cHistoryDisplay->SetTableID($MP3ID);
	$cHistoryDisplay->DisplayHistory();
}
?>