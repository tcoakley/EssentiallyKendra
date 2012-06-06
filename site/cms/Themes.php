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
ValidateLogin("Theme");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Theme";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Theme List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$ThemeID = RequestInt("ThemeID");
$arrThemeID = RequestArray("ThemeID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Theme", "Themes.php?PageFunction=Add+Theme", "Theme");
$cQuickNav->AddNav("Theme List", "Themes.php?PageFunction=Theme+List", "Theme");
$cQuickNav->AddNav("Theme Sort", "Themes.php?PageFunction=Theme+Sort", "Theme");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Theme
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Theme" || $PageFunction == "Modify Theme") {
		$cThemes = new cThemes();
		$cThemes->LoadFieldsFromForm();
		$cThemes->SetAutoCrop($AutoCrop);
		if ($ThemeID > 0) {
			$cThemes->SetTableID($ThemeID);
			if ($cThemes->ModifyRecord()) {
				AddIncomingMessage("Theme modified");
			}
		} else {
			$ThemeID = $cThemes->InsertRecord();
			if ($ThemeID) {
				AddIncomingMessage("Theme created");
			}
		}
		if ($AddAnother) {
			RedirectPage("Themes.php?PageFunction=Add+Theme");
		} else {
			RedirectPage("Themes.php?PageFunction=Modify+Theme&ThemeID=$ThemeID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Sort Theme
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Theme Sort") {
		$DisplayOrder = count($arrThemeID) * 10;
		for ($looper = 0; $looper < count($arrThemeID); $looper ++) {
			$ThemeID = $arrThemeID[$looper];
			$sqlSelect = "update cms_themes set DisplayOrder = $DisplayOrder where ThemeID = $ThemeID";
			ExecuteQuery($sqlSelect);
			$DisplayOrder -= 10;
		}
		AddIncomingMessage("Display Order has been updated.");
		$cThemes = new cThemes();
		$cThemes->SaveFile();
		RedirectPage("Themes.php?PageFunction=Theme+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Theme
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Theme") {
		$cThemes = new cThemes();
		$cThemes->DeleteRecord($arrThemeID);
		RedirectPage("Themes.php?PageFunction=Theme+List");
	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Theme History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Theme":
	case "Modify Theme":
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
		if ($PageFunction == "Add Theme") {
			$cTabTable->AddTab("Theme", "Add Theme");
		} else {
			$cTabTable->AddTab("Modify Theme", "Modify Theme", "ThemeID=$ThemeID");
			$cTabTable->AddTab("History", "Theme History", "ThemeID=$ThemeID");
		}
		
		
		switch ($PageFunction) {
			case "Add Theme":
				$cTabTable->DisplayTabs("DisplayThemeForm");
				break;
			
			case "Modify Theme":
				$cTabTable->DisplayTabs("DisplayThemeForm");
				break;
				
			case "Theme Sort":
				$cTabTable->DisplayTabs("DisplayThemeSort");
				break;
				
			case "Theme List":
				DisplayThemeList();
				break;
				
			case "Theme History":
				$cTabTable->DisplayTabs("DisplayThemeHistory");
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
//	DisplayThemeForm
// ---------------------------------------------------------------------------------
function DisplayThemeForm() {
	global $ThemeID, $PageFunction;
	if ($ThemeID > 0) {
		$cThemes = new cThemes($ThemeID);
	} else {
		$cThemes = new cThemes();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Themes.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="ThemeID" value="<?php print $ThemeID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($ThemeID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $ThemeID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Submitter</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Submitter" value="<?php print $cThemes->Submitter ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Email</div>
			<div class="FormField"><input type="text" class="req" alt="em-0" style="width: 250px;" name="Email" value="<?php print $cThemes->Email ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Website</div>
			<div class="FormField"><input type="text" class="opt" alt="" style="width: 250px;" name="Website" value="<?php print $cThemes->Website ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Active</div>
			<div class="FormField"><input type="checkbox" name="Active" value="1" <?php if($cThemes->Active) { print "checked";} ?>></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Default</div>
			<div class="FormField"><input type="checkbox" name="DefaultTheme" value="1" <?php if($cThemes->DefaultTheme) { print "checked";} ?>></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Thumb Only</div>
			<div class="FormField"><input type="checkbox" name="ThumbOnly" value="1" <?php if($cThemes->ThumbOnly) { print "checked";} ?>></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Background Image</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Background"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Background Color</div>
			<div class="FormField"><input type="text" class="req" alt="ml-6,mx-6" style="width: 250px;" name="BGColor" value="<?php print $cThemes->BGColor ?>"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cThemes->Background) > 4) { ?>
				<div class="FormTitle">Current Background</div>
				<div class="FormField"><?php $cThemes->DisplayImageLink("Background", "PageFunction=Modify+Theme&ThemeID=$ThemeID");?></div>
				<div class="clear"></div>
			<?php } ?>
			

			<div class="FormTitle">Thumb<br>100x75</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Thumb"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cThemes->Thumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cThemes->DisplayImageLink("Thumb", "PageFunction=Modify+Theme&ThemeID=$ThemeID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<?php if ($ThemeID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cThemes->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cThemes->ModifiedDate)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($ThemeID > 0) { ?>
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
			frm.Submitter.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Theme?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Theme";
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
//	DisplayThemeSort
// ---------------------------------------------------------------------------------
function DisplayThemeSort() {
	global $PageFunction;

	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Themes.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">

			<div><strong>Drag blue ball to sort</strong></div>
			<br>
			<div>
				<ol id="MediaSort" class="SortList">
					<?php
						$sqlSelect = "select ThemeID, Submitter, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_themes order by DisplayOrder desc";
						$tbl = ExecuteQuery($sqlSelect);
						while ($row = mysql_fetch_object($tbl)) {
							$ThemeID = $row->ThemeID;
							$Submitter = $row->Submitter;
							$CreatedDate = $row->CreatedDate;
							print "<li><div class=\"DragHandle\">&nbsp;</div><div class=\"FloatLeft\">$Submitter</div><div class=\"FloatRight\">" . FormatDate($CreatedDate, "%m/%d/%y %H:%M") . "</div><div class=\"clear\"></div><input type=\"hidden\" name=\"ThemeID[]\" value=\"$ThemeID\"></li>\n";
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
//	DisplayThemeList
// ---------------------------------------------------------------------------------
function DisplayThemeList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select ThemeID, Submitter, DisplayOrder, Active, DefaultTheme, UNIX_TIMESTAMP(CreatedDate) as CreatedDate" .
		" from cms_themes";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Theme");
	$cDataGrid->SetSelectFunction("Delete Theme");
	$cDataGrid->SetModifyFunction("Modify Theme");
	$cDataGrid->SetPrimaryKey("ThemeID");
	$cDataGrid->SetSortBy("DisplayOrder");
	$cDataGrid->SetSortDirection("desc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Theme? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Theme? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("ID", "ThemeID", null, "int");
	$cDataGrid->AddColumn("Submitter", "Submitter", null, "string");
	$cDataGrid->AddColumn("Active", "Active", "bool", "bool");
	$cDataGrid->AddColumn("Default", "DefaultTheme", "bool", "bool");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayThemeHistory
// ---------------------------------------------------------------------------------
function DisplayThemeHistory() {
	global $ThemeID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("ThemeID");
	$cHistoryDisplay->SetTabValue($ThemeID);
	
	$cHistoryDisplay->SetTableName("cms_Theme");
	$cHistoryDisplay->SetTableID($ThemeID);
	$cHistoryDisplay->DisplayHistory();
}
?>