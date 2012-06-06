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
ValidateLogin("Featured");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Featured";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Featured List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$FeaturedID = RequestInt("FeaturedID");
$arrFeaturedID = RequestArray("FeaturedID");

$FeaturedImageID = RequestInt("FeaturedImageID");
$arrFeaturedImageID = RequestArray("FeaturedImageID");

$arrSortFeaturedImageID = RequestArray("SortFeaturedImageID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Featured", "Featured.php?PageFunction=Add+Featured", "Featured");
$cQuickNav->AddNav("Featured List", "Featured.php?PageFunction=Featured+List", "Featured");
$cQuickNav->AddNav("Featured Sort", "Featured.php?PageFunction=Featured+Sort", "Featured");


// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Featured
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Featured" || $PageFunction == "Modify Featured" || $PageFunction == "Modify Spanish") {
		$cFeatured = new cFeatured();
		$cFeatured->LoadFieldsFromForm();
		if ($FeaturedID > 0) {
			$cFeatured->SetTableID($FeaturedID);
			if ($cFeatured->ModifyRecord()) {
				AddIncomingMessage("Featured item modified");
			}
		} else {
			$FeaturedID = $cFeatured->InsertRecord();
			if ($FeaturedID) {
				AddIncomingMessage("Featured item created");
			}
		}
		if ($AddAnother) {
			RedirectPage("Featured.php?PageFunction=Add+Featured");
		} else {
			if ($PageFunction == "Modify Spanish") {
				RedirectPage("Featured.php?PageFunction=Modify+Spanish&FeaturedID=$FeaturedID");
			} else {
				RedirectPage("Featured.php?PageFunction=Modify+Featured&FeaturedID=$FeaturedID");
			}
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Sort Featured
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Featured Sort") {
		$DisplayOrder = count($arrFeaturedID) * 10;
		for ($looper = 0; $looper < count($arrFeaturedID); $looper ++) {
			$FeaturedID = $arrFeaturedID[$looper];
			$sqlSelect = "update cms_featured set DisplayOrder = $DisplayOrder where FeaturedID = $FeaturedID";
			ExecuteQuery($sqlSelect);
			$DisplayOrder -= 10;
		}
		$cFeatured = new cFeatured();
		$cFeatured->SaveFile();
		AddIncomingMessage("Display Order has been updated.");
		RedirectPage("Featured.php?PageFunction=Featured+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Featured
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Featured") {
		$cFeatured = new cFeatured();
		$cFeatured->DeleteRecord($arrFeaturedID);
		RedirectPage("Featured.php?PageFunction=Featured+List");
	}
	
	

		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Featured History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Featured":
	case "Modify Featured":
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
		if ($PageFunction == "Add Featured") {
			$cTabTable->AddTab("Featured", "Add Featured");
		} else {
			$cTabTable->AddTab("Featured", "Modify Featured", "FeaturedID=$FeaturedID");

			$cTabTable->AddTab("History", "Featured History", "FeaturedID=$FeaturedID");
		}
		
		
		switch ($PageFunction) {
			case "Add Featured":
				$cTabTable->DisplayTabs("DisplayFeaturedForm");
				break;
			
			case "Modify Featured":
				$cTabTable->DisplayTabs("DisplayFeaturedForm");
				break;
				
			case "Featured Sort":
				$cTabTable->DisplayTabs("DisplayFeaturedSort");
				break;
				
			case "Featured List":
				DisplayFeaturedList();
				break;
				
			case "Featured History":
				$cTabTable->DisplayTabs("DisplayFeaturedHistory");
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
//	DisplayFeaturedForm
// ---------------------------------------------------------------------------------
function DisplayFeaturedForm() {
	global $FeaturedID, $PageFunction;
	if ($FeaturedID > 0) {
		$cFeatured = new cFeatured($FeaturedID);
	} else {
		$cFeatured = new cFeatured();
		$cFeatured->DelayTime = 5;
	}
	?>
	
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Featured.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="FeaturedID" value="<?php print $FeaturedID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			<?php if ($FeaturedID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $FeaturedID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Title</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Title" value="<?php print $cFeatured->Title ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField" style="width: 550px;">
				<textarea name="Description" style="width: 250px; height: 30px;"><?php print $cFeatured->Description ?></textarea>
			</div>
			<div class="clear"></div>

			<div class="FormTitle">Video</div>
			<div class="FormField"><?php DisplayVideoSelect($cFeatured->Video); ?></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Auto Start Video</div>
			<div class="FormField"><input type="checkbox" name="AutoStart" value="1" <?php if ($cFeatured->AutoStart) { print "checked"; } ?>></div>
			<div class="clear"></div>
			

			<div class="FormTitle">Image<br>641x360</div>
			<div class="FormField"><input type="file" style="width: 250px;" name="Image"></div>
			<div class="clear"></div>
			
			<?php if (strlen($cFeatured->Image) > 4) { ?>
				<div class="FormTitle">Current Image</div>
				<div class="FormField"><?php $cFeatured->DisplayImageLink("Image", "PageFunction=Modify+Featured&FeaturedID=$FeaturedID");?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Link</div>
			<div class="FormField"><input type="text" class="opt" alt="ml-1" style="width: 250px;" name="Link" value="<?php print $cFeatured->Link ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Delay in Seconds</div>
			<div class="FormField"><input type="text" class="req" alt="ml-1,nm-0" style="width: 250px;" name="DelayTime" value="<?php print $cFeatured->DelayTime ?>"></div>
			<div class="clear"></div>
			
			
			<?php if ($FeaturedID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cFeatured->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cFeatured->ModifiedDate)?></div>
				<div class="clear"></div>


			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($FeaturedID > 0) { ?>
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
				DeleteRecord = confirm('Are you certain you wish to delete this Featured?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Featured";
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
//	DisplayFeaturedSort
// ---------------------------------------------------------------------------------
function DisplayFeaturedSort() {
	global $PageFunction;

	?>

	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Featured.php" enctype="multipart/form-data" class="MooValidator">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">

			<div><strong>Drag blue ball to sort</strong></div>
			<br>
			<div>
				<ol id="FeaturedSort" class="SortList">
					<?php
						$sqlSelect = "select FeaturedID, Title, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_featured order by DisplayOrder desc";
						$tbl = ExecuteQuery($sqlSelect);
						while ($row = mysql_fetch_object($tbl)) {
							$FeaturedID = $row->FeaturedID;
							$Title = $row->Title;
							$CreatedDate = $row->CreatedDate;
							print "<li><div class=\"DragHandle\">&nbsp;</div><div class=\"FloatLeft\">$Title</div><div class=\"FloatRight\">" . FormatDate($CreatedDate, "%m/%d/%y %H:%M") . "</div><div class=\"clear\"></div><input type=\"hidden\" name=\"FeaturedID[]\" value=\"$FeaturedID\"></li>\n";
						}
					?>
				</ol>
			</div>
			<div class="clear"></div>
			
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
	
			

		
		</form>
		<script type="text/javascript">
			var mySortables = new Sortables('FeaturedSort', {
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
		print "<select style=\"width: 250px;\" name=\"Video\">\n" . 
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
//	DisplayFeaturedList
// ---------------------------------------------------------------------------------
function DisplayFeaturedList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select FeaturedID, Title, DisplayOrder, UNIX_TIMESTAMP(CreatedDate) as CreatedDate, UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate" .
		" from cms_featured Featured";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Featured");
	$cDataGrid->SetSelectFunction("Delete Featured");
	$cDataGrid->SetModifyFunction("Modify Featured");
	$cDataGrid->SetPrimaryKey("FeaturedID");
	$cDataGrid->SetSortBy("DisplayOrder");
	$cDataGrid->SetSortDirection("desc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Featured? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Featured? This action cannot be undone.");
	$cDataGrid->SetFiltering(true);	

	$cDataGrid->AddColumn("ID", "FeaturedID", null, "int");
	$cDataGrid->AddColumn("Title", "Title", null, "string");
	$cDataGrid->AddColumn("Order", "DisplayOrder", null, "int");
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");
	$cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%Y"), "date");

	$cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//	DisplayFeaturedHistory
// ---------------------------------------------------------------------------------
function DisplayFeaturedHistory() {
	global $FeaturedID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("FeaturedID");
	$cHistoryDisplay->SetTabValue($FeaturedID);
	
	$cHistoryDisplay->SetTableName("cms_featured");
	$cHistoryDisplay->SetTableID($FeaturedID);
	$cHistoryDisplay->DisplayHistory();
}
?>