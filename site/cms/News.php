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
ValidateLogin("News & Events");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "News";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Article List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$NewsID = RequestInt("NewsID");
$arrNewsID = RequestArray("NewsID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Article", "News.php?PageFunction=Add+Article", "News");
$cQuickNav->AddNav("Article List", "News.php?PageFunction=Article+List", "News");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Article
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Article" || $PageFunction == "Modify Article" || $PageFunction == "Modify Spanish") {
		$cNews = new cNews();
		$cNews->LoadFieldsFromForm();
		$cNews->SetAutoCrop($AutoCrop);
		if ($NewsID > 0) {
			$cNews->SetTableID($NewsID);
			if ($cNews->ModifyRecord()) {
				AddIncomingMessage("Article modified");
			}
		} else {
			$NewsID = $cNews->InsertRecord();
			if ($NewsID) {
				AddIncomingMessage("Article created");
			}
		}
		if ($AddAnother) {
			RedirectPage("News.php?PageFunction=Add+Article");
		} else {
			if ($PageFunction == "Modify Spanish") {
				RedirectPage("News.php?PageFunction=Modify+Spanish&NewsID=$NewsID");
			} else {
				RedirectPage("News.php?PageFunction=Modify+Article&NewsID=$NewsID");
			}
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Article
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Article") {
		$cNews = new cNews();
		$cNews->DeleteRecord($arrNewsID);
		RedirectPage("News.php?PageFunction=Article+List");
	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Article History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Article":
	case "Modify Article":
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
		if ($PageFunction == "Add Article") {
			$cTabTable->AddTab("Article", "Add Article");
		} else {
			$cTabTable->AddTab("English Article", "Modify Article", "NewsID=$NewsID");
			$cTabTable->AddTab("Spanish Article", "Modify Spanish", "NewsID=$NewsID");
			$cTabTable->AddTab("History", "Article History", "NewsID=$NewsID");
		}
		
		
		switch ($PageFunction) {
			case "Add Article":
				$cTabTable->DisplayTabs("DisplayArticleForm");
				break;
			
			case "Modify Article":
				$cTabTable->DisplayTabs("DisplayArticleForm");
				break;
				
			case "Modify Spanish":
				$cTabTable->DisplayTabs("DisplaySpanishArticleForm");
				break;
				
			case "Article List":
				DisplayArticleList();
				break;
				
			case "Article History":
				$cTabTable->DisplayTabs("DisplayArticleHistory");
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
//	DisplayArticleForm
// ---------------------------------------------------------------------------------
function DisplayArticleForm() {
	global $NewsID, $PageFunction;
	if ($NewsID > 0) {
		$cNews = new cNews($NewsID);
	} else {
		$cNews = new cNews();
		$cNews->NewsDate = GetTime();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="News.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="NewsID" value="<?php print $NewsID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($NewsID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $NewsID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="enTitle" value="<?php print $cNews->enTitle ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Date</div>
			<div class="FormField"><input type="text" rel="moocalendar" class="req" alt="dt-0" style="width: 250px;" name="NewsDate" value="<?php print FormatDate($cNews->NewsDate,'%m/%d/%y'); ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Body</div>
			<div class="FormField" style="width: 550px;">
				<?php
				$oFCKeditor = new FCKeditor("enBody");
				$oFCKeditor->BasePath = "fckeditor/";
				$oFCKeditor->Value = $cNews->enBody;
				$oFCKeditor->Height = 300;
				$oFCKeditor->Create();
				?>			
			</div>
			<div class="clear"></div>

			<div class="FormTitle">Image</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Image"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cNews->Image) > 4) { ?>
				<div class="FormTitle">Current Image</div>
				<div class="FormField"><?php $cNews->DisplayImageLink("Image", "PageFunction=Modify+Article&NewsID=$NewsID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Thumb<br>70 x 53</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Thumb"></div>
			<div class="FloatLeft" style="padding: 8px 0 0 0;"><input type="checkbox" name="AutoCrop" value="1" checked> &nbsp;Check to Auto Crop</div>
			<div class="clear"></div>
			
			<?php if (strlen($cNews->Thumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cNews->DisplayImageLink("Thumb", "PageFunction=Modify+Article&NewsID=$NewsID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<?php if ($NewsID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cNews->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cNews->ModifiedDate)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($NewsID > 0) { ?>
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
				DeleteRecord = confirm('Are you certain you wish to delete this Article?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Article";
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
//	DisplaySpanishArticleForm
// ---------------------------------------------------------------------------------
function DisplaySpanishArticleForm() {
	global $NewsID, $PageFunction;
	if ($NewsID > 0) {
		$cNews = new cNews($NewsID);
	} else {
		$cNews = new cNews();
		$cNews->NewsDate = GetTime();
	}
	include_once("fckeditor/fckeditor_php5.php");
	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="News.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="NewsID" value="<?php print $NewsID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($NewsID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $NewsID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="esTitle" value="<?php print $cNews->esTitle ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Date</div>
			<div class="FormField"><input type="text" rel="moocalendar" class="req" alt="dt-0" style="width: 250px;" name="NewsDate" value="<?php print FormatDate($cNews->NewsDate,'%m/%d/%y'); ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Body</div>
			<div class="FormField" style="width: 550px;">
				<?php
				$oFCKeditor = new FCKeditor("esBody");
				$oFCKeditor->BasePath = "fckeditor/";
				$oFCKeditor->Value = $cNews->esBody;
				$oFCKeditor->Height = 300;
				$oFCKeditor->Create();
				?>			
			</div>
			<div class="clear"></div>

			<div class="FormTitle">Image</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Image"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cNews->Image) > 4) { ?>
				<div class="FormTitle">Current Image</div>
				<div class="FormField"><?php $cNews->DisplayImageLink("Image", "PageFunction=Modify+Article&NewsID=$NewsID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Thumb<br>70 x 53</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Thumb"></div>
			<div class="FloatLeft" style="padding: 8px 0 0 0;"><input type="checkbox" name="AutoCrop" value="1" checked> &nbsp;Check to Auto Crop</div>
			<div class="clear"></div>
			
			<?php if (strlen($cNews->Thumb) > 4) { ?>
				<div class="FormTitle">Current Thumb</div>
				<div class="FormField"><?php $cNews->DisplayImageLink("Thumb", "PageFunction=Modify+Article&NewsID=$NewsID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<?php if ($NewsID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cNews->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cNews->ModifiedDate)?></div>
				<div class="clear"></div>

			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($NewsID > 0) { ?>
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
			frm.esTitle.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Article?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Article";
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
//	DisplayArticleList
// ---------------------------------------------------------------------------------
function DisplayArticleList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select NewsID, enTitle, UNIX_TIMESTAMP(NewsDate) as NewsDate, UNIX_TIMESTAMP(CreatedDate) as CreatedDate" .
		" from cms_news news";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Article");
	$cDataGrid->SetSelectFunction("Delete Article");
	$cDataGrid->SetModifyFunction("Modify Article");
	$cDataGrid->SetPrimaryKey("NewsID");
	$cDataGrid->SetSortBy("NewsDate");
	$cDataGrid->SetSortDirection("desc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Article? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Article? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("ID", "NewsID", null, "int");
	$cDataGrid->AddColumn("Title", "enTitle", null, "string");
	$cDataGrid->AddColumn("Date", "NewsDate", array("Date","%m/%d/%Y"), "date");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayArticleHistory
// ---------------------------------------------------------------------------------
function DisplayArticleHistory() {
	global $NewsID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("NewsID");
	$cHistoryDisplay->SetTabValue($NewsID);
	
	$cHistoryDisplay->SetTableName("cms_news");
	$cHistoryDisplay->SetTableID($NewsID);
	$cHistoryDisplay->DisplayHistory();
}
?>