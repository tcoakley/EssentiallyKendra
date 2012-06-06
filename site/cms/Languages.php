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
ValidateLogin("Languages");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Languages";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Language List");
$AddAnother = RequestBool("AddAnother");

$LanguageID = RequestInt("LanguageID");
$arrLanguageID = RequestArray("LanguageID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Language", "Languages.php?PageFunction=Add+Language", "Languages");
$cQuickNav->AddNav("Language List", "Languages.php?PageFunction=Language+List", "Languages");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Language
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Language" || $PageFunction == "Modify Language") {
		$cLanguages = new cLanguages();
		$cLanguages->LoadFieldsFromForm();
		if ($LanguageID > 0) {
			$cLanguages->SetTableID($LanguageID);
			if ($cLanguages->ModifyRecord()) {
				AddIncomingMessage("Language modified");
			}
		} else {
			$LanguageID = $cLanguages->InsertRecord();
			if ($LanguageID) {
				AddIncomingMessage("Language created");
			}
		}
		if ($AddAnother) {
			RedirectPage("Languages.php?PageFunction=Add+Language");
		} else {
			RedirectPage("Languages.php?PageFunction=Modify+Language&LanguageID=$LanguageID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Language
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Language") {
		$cLanguages = new cLanguages();
		$cLanguages->DeleteRecord($arrLanguageID);
		RedirectPage("Languages.php?PageFunction=Language+List");
	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Language History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Language":
	case "Modify Language":
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
		if ($PageFunction == "Add Language") {
			$cTabTable->AddTab("Language", "Add Language");
		} else {
			$cTabTable->AddTab("Language", "Modify Language", "LanguageID=$LanguageID");
			$cTabTable->AddTab("History", "Language History", "LanguageID=$LanguageID");
		}
		
		
		switch ($PageFunction) {
			case "Add Language":
				$cTabTable->DisplayTabs("DisplayLanguageForm");
				break;
			
			case "Modify Language":
				$cTabTable->DisplayTabs("DisplayLanguageForm");
				break;
				
			case "Language List":
				DisplayLanguageList();
				break;
				
			case "Language History":
				$cTabTable->DisplayTabs("DisplayLanguageHistory");
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
//	DisplayLanguageForm
// ---------------------------------------------------------------------------------
function DisplayLanguageForm() {
	global $LanguageID, $PageFunction;
	if ($LanguageID > 0) {
		$cLanguages = new cLanguages($LanguageID);
	} else {
		$cLanguages = new cLanguages();
	}
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Languages.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="LanguageID" value="<?php print $LanguageID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($LanguageID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $LanguageID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Language</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Language" value="<?php print $cLanguages->Language ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Language Prefix</div>
			<div class="FormField"><input type="text" class="req" alt="ml-2,mx-2" style="width: 250px;" name="LanguagePrefix" value="<?php print $cLanguages->LanguagePrefix ?>"></div>
			<div class="clear"></div>


			
			<?php if ($LanguageID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cLanguages->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cLanguages->ModifiedDate)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($LanguageID > 0) { ?>
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
			frm.Language.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Language?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Language";
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
//	DisplayLanguageList
// ---------------------------------------------------------------------------------
function DisplayLanguageList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select LanguageID, Language, LanguagePrefix, UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_languages";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Language");
	$cDataGrid->SetSelectFunction("Delete Language");
	$cDataGrid->SetModifyFunction("Modify Language");
	$cDataGrid->SetPrimaryKey("LanguageID");
	$cDataGrid->SetSortBy("Language");
	$cDataGrid->SetSortDirection("asc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Languages? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Language? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("LanguageID", "LanguageID", null, "int");
	$cDataGrid->AddColumn("Language", "Language", null, "string");
	$cDataGrid->AddColumn("Prefix", "LanguagePrefix", null, "string");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");
	$cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayLanguageHistory
// ---------------------------------------------------------------------------------
function DisplayLanguageHistory() {
	global $LanguageID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("LanguageID");
	$cHistoryDisplay->SetTabValue($LanguageID);
	
	$cHistoryDisplay->SetTableName("cms_languages");
	$cHistoryDisplay->SetTableID($LanguageID);
	$cHistoryDisplay->DisplayHistory();
}
?>