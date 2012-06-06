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
ValidateLogin("Groups");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Users";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Group List");
$AddAnother = RequestBool("AddAnother");


$GroupID = RequestInt("GroupID");
$arrGroupID = RequestArray("GroupID");
//$arrPermissions = RequestArray("Permission");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add User", "Users.php?PageFunction=Add+User", "Users");
$cQuickNav->AddNav("User List", "Users.php?PageFunction=User+List", "Users");
$cQuickNav->AddNav("Add Group", "Groups.php?PageFunction=Add+Group", "Groups");
$cQuickNav->AddNav("Group List", "Groups.php?PageFunction=Group+List", "Groups");



// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Group
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Group" || $PageFunction == "Modify Group") {
		$cGroups = new cGroups();
		$cGroups->LoadFieldsFromForm();
		if ($GroupID > 0) {
			$cGroups->SetTableID($GroupID);
			if ($cGroups->ModifyRecord()) {
				AddIncomingMessage("Group modified");
			}
		} else {
			$GroupID = $cGroups->InsertRecord();
			if ($GroupID) {
				AddIncomingMessage("Group created");
			}
		}
		if ($AddAnother) {
			RedirectPage("Groups.php?PageFunction=Add+Group");
		} else {
			RedirectPage("Groups.php?PageFunction=Modify+Group&GroupID=$GroupID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Group
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Group") {
		$cGroups = new cGroups();
		$cGroups->DeleteRecord($arrGroupID);
		RedirectPage("Groups.php?PageFunction=Group+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Group Permissions
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Group Permissions") {
		$cGroups = new cGroups($GroupID);
		$cGroups->AddAssociation("cms_grouppermissions", "Permission", RequestArray("Permission"));
		if ($cGroups->ModifyRecord()) {
			AddIncomingMessage("Group permissions modified");
		}
		RedirectPage("Groups.php?PageFunction=Group+Permissions&GroupID=$GroupID");
	}
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "";  // Add any additional style sheets you want require_onced here

switch ($PageFunction) {
	case "Group History":
		$OnPageLoad = "PrepSlides();";
		break;
		
	case "Add Group":
	case "Modify Group":
		$OnPageLoad = "";
		break;
	
	default:
		$OnPageLoad = ""; 
		break;
}

$JavaLibraries = ""; // Add any java libraries you want require_onced here
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
		if ($PageFunction == "Add Group") {
			$cTabTable->AddTab("Group", "Add Group");
		} else {
			$cTabTable->AddTab("Group", "Modify Group", "GroupID=$GroupID");
			$cTabTable->AddTab("Permissions", "Group Permissions", "GroupID=$GroupID");
			$cTabTable->AddTab("History", "Group History", "GroupID=$GroupID");
		}
		
		
		switch ($PageFunction) {
			case "Add Group":
				$cTabTable->DisplayTabs("DisplayGroupForm");
				break;
			
			case "Modify Group":
				$cTabTable->DisplayTabs("DisplayGroupForm");
				break;
				
			case "Group List":
				DisplayGroupList();
				break;
				
			case "Group Permissions":
				$cTabTable->DisplayTabs("DisplayPermissionsForm");
				break;
				
			case "Group History":
				$cTabTable->DisplayTabs("DisplayHistory");
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
//	DisplayGroupForm
// ---------------------------------------------------------------------------------
function DisplayGroupForm() {
	global $GroupID, $PageFunction;
	if ($GroupID > 0) {
		$cGroups = new cGroups($GroupID);
	} else {
		$cGroups = new cGroups();
	}
	?>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Groups.php">
			<input type="hidden" name="FormComplete" value="1" />
			<input type="hidden" name="GroupID" value="<?php print $GroupID?>" />
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>" />
			<input type="hidden" name="AddAnother" value="0" />

			<?php if ($GroupID > 0 ) { ?>
				<div class="FormTitle">ID</div>
				<div class="FormField"><?php print $GroupID ?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">Name</div>
			<div class="FormField"><input type="text" name="GroupName" class="req" alt="ml-2" style="width: 250px;" value="<?php print $cGroups->GroupName ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField"><textarea style="width: 250px; height:100px;" name="GroupDescription"><?php print $cGroups->GroupDescription ?></textarea></div>
			<div class="clear"></div>
			
			
			<?php if ($GroupID > 0 ) { ?>
				<div class="FormTitle">Created Date</div>
				<div class="FormField"><?php print FormatDate($cGroups->CreatedDate)?></div>
				<div class="clear"></div>
				
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cGroups->ModifiedDate)?></div>
				<div class="clear"></div>
			<?php } ?>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($GroupID > 0) { ?>
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
			frm.GroupName.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Group?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Group";
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
//	DisplayGroupList
// ---------------------------------------------------------------------------------
function DisplayGroupList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select GroupID, GroupName, GroupDescription, UNIX_TIMESTAMP(CreatedDate) as CreatedDate, UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate from cms_groups";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Group");
	$cDataGrid->SetSelectFunction("Delete Group");
	$cDataGrid->SetModifyFunction("Modify Group");
	$cDataGrid->SetPrimaryKey("GroupID");
	$cDataGrid->SetSortBy("GroupName");
	$cDataGrid->SetSortDirection("asc");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Groups? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Group? This action cannot be undone.");
	

	$cDataGrid->AddColumn("GroupID", "GroupID");
	$cDataGrid->AddColumn("Group Name", "GroupName");
	$cDataGrid->AddColumn("Description", "GroupDescription", array("trunc", 45));
	$cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"));
	$cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%Y"));

	$cDataGrid->DisplayGrid();
}


// ---------------------------------------------------------------------------------
//	DisplayPermissionsForm
// ---------------------------------------------------------------------------------
function DisplayPermissionsForm() {
	global $GroupID, $PageFunction;
	$cGroups = new cGroups($GroupID);
	?>
	<div id="FormCanisterMaster">
		<strong><?php print $cGroups->GroupName?></strong><br><br>
		<form name="frmMain" method="post" action="Groups.php">
			<input type="hidden" name="FormComplete" value="1" />
			<input type="hidden" name="GroupID" value="<?php print $GroupID?>" />
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>" />
			<?php
			$sqlSelect = "select * from cms_permissions";
			$tbl = ExecuteQuery($sqlSelect);
			while ($row = mysql_fetch_object($tbl)) {
				$Permission = $row->Permission;
				$Description = $row->Description;
				print("<div class=\"FormTitle\">$Permission</div>\n");
				if (in_array($Permission, $cGroups->arrPermissions)) {
					print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"Permission[]\" value=\"$Permission\" checked></div>\n");
				} else {
					print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"Permission[]\" value=\"$Permission\"></div>\n");
				}
				print("<div class=\"FormField\">$Description</div>\n");
				print ("<div class=\"clear\"></div>");
			}
			
			?>
	
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField">
				<img src="_img/arrow_ltr.png" width="38" height="22" style="padding: 0 5px 0 0;" />
				<a href="javascript:;" onClick="SetChecks(1)">Check All</a> / 
				<a href="javascript:;" onClick="SetChecks(0)">Uncheck All</a>
			</div>
			<div class="clear"></div>

			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save"></div>

			<div class="clear"></div>
		</form>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
		//SetChecks
		function SetChecks(inValue) {
			$$("input.CheckBox").each(function(el) {
				el.setProperty("checked", inValue);
			});
		}
	</script>
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayHistory
// ---------------------------------------------------------------------------------
function DisplayHistory() {
	global $GroupID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetPrimaryKey("HistoryID");
	$cHistoryDisplay->SetTabKey("GroupID");
	$cHistoryDisplay->SetTabValue($GroupID);
	
	$cHistoryDisplay->SetTableName("cms_groups");
	$cHistoryDisplay->SetTableID($GroupID);
	$cHistoryDisplay->DisplayHistory();
}
?>