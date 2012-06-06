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
ValidateLogin("Master Settings");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Master Settings";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Master Settings");
$AddAnother = RequestBool("AddAnother");

$PermissionID = RequestInt("PermissionID");
$arrPermissionID = RequestArray("PermissionID");

$EmailID = RequestInt("EmailID");
$arrEmailID = RequestArray("EmailID");
$EmailFieldID = RequestInt("EmailFieldID");
$arrEmailFieldID = RequestArray("EmailFieldID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Master Settings", "MasterSettings.php", "Master Settings");
$cQuickNav->AddNav("Add Permission", "MasterSettings.php?PageFunction=Add+Permission", "IsTom");
$cQuickNav->AddNav("Permission List", "MasterSettings.php?PageFunction=Permission+List", "IsTom");
$cQuickNav->AddNav("Add Email", "MasterSettings.php?PageFunction=Add+Email", "IsTom");
$cQuickNav->AddNav("Email List", "MasterSettings.php?PageFunction=Email+List", "IsTom");



// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {


	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	MasterSettings
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Master Settings") {
		$cMasterSettings = new cMasterSettings(1);
		$cMasterSettings->LoadFieldsFromForm();
		if ($cMasterSettings->ModifyRecord()) {
			AddIncomingMessage("Master Settings modified");
		}
		RedirectPage("MasterSettings.php");
	}

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Permission
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Permission" || $PageFunction == "Modify Permission") {
		$cPermissions = new cPermissions();
		$cPermissions->LoadFieldsFromForm();
		if ($PermissionID > 0) {
			$cPermissions->SetTableID($PermissionID);
			if ($cPermissions->ModifyRecord()) {
				AddIncomingMessage("Permission modified");
			}
		} else {
			$PermissionID = $cPermissions->InsertRecord();
			if ($PermissionID) {
				AddIncomingMessage("Permission created");
			}
		}
		if ($AddAnother) {
			RedirectPage("MasterSettings.php?PageFunction=Add+Permission");
		} else {
			RedirectPage("MasterSettings.php?PageFunction=Modify+Permission&PermissionID=$PermissionID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Permission
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Permission") {
		$cPermissions = new cPermissions();
		$cPermissions->DeleteRecord($arrPermissionID);
		RedirectPage("MasterSettings.php?PageFunction=Permission+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Email
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Email" || $PageFunction == "Modify Email") {
		$cEmails = new cEmails();
		$cEmails->LoadFieldsFromForm();
		if ($EmailID > 0) {
			$cEmails->SetTableID($EmailID);
			if ($cEmails->ModifyRecord()) {
				AddIncomingMessage("Email modified");
			}
		} else {
			$EmailID = $cEmails->InsertRecord();
			if ($EmailID) {
				AddIncomingMessage("Email created");
			}
		}
		if ($AddAnother) {
			RedirectPage("MasterSettings.php?PageFunction=Add+Email");
		} else {
			RedirectPage("MasterSettings.php?PageFunction=Modify+Email&EmailID=$EmailID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Email
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Email") {
		$cEmails = new cEmails();
		$cEmails->DeleteRecord($arrEmailID);
		RedirectPage("MasterSettings.php?PageFunction=Email+List");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Add/Modify Field
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Add Field" || $PageFunction == "Modify Field") {
		$cEmailFields = new cEmailFields();
		$cEmailFields->LoadFieldsFromForm();
		if ($EmailFieldID > 0) {
			$cEmailFields->SetTableID($EmailFieldID);
			if ($cEmailFields->ModifyRecord()) {
				AddIncomingMessage("Field modified");
			}
		} else {
			$EmailFieldID = $cEmailFields->InsertRecord();
			if ($EmailFieldID) {
				AddIncomingMessage("Field created");
			}
		}
		if ($AddAnother) {
			RedirectPage("MasterSettings.php?PageFunction=Add+Field&EmailID=$EmailID");
		} else {
			RedirectPage("MasterSettings.php?PageFunction=Modify+Field&EmailID=$EmailID&EmailFieldID=$EmailFieldID");
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Delete Field
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($PageFunction == "Delete Field") {
		$cEmailFields = new cEmailFields();
		$EmailID = $cEmailFields->DeleteRecord($arrEmailFieldID);
		RedirectPage("MasterSettings.php?PageFunction=Fieldl+List&EmailID=$EmailID");
	}
		
}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
	case "Master Settings History":
		$OnPageLoad = "PrepSlides();";
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
		
		
		switch ($PageFunction) {
			case "Master Settings":
				$cTabTable->AddTab("Master Settings", "Master Settings");
				$cTabTable->AddTab("History", "Master Settings History");
				$cTabTable->DisplayTabs("DisplayMasterSettingsForm");
				break;
				
			case "Master Settings History":
				$cTabTable->AddTab("Master Settings", "Master Settings");
				$cTabTable->AddTab("History", "Master Settings History");
				$cTabTable->DisplayTabs("DisplayHistory");
				break;
			
			case "Add Permission":
				$cTabTable->AddTab("Permission", "Add Permission");
				$cTabTable->DisplayTabs("DisplayPermissionForm");
				break;
			
			case "Modify Permission":
				$cTabTable->AddTab("Permission", "Modify Permission", "PermissionID=$PermissionID");
				$cTabTable->DisplayTabs("DisplayPermissionForm");
				break;
				
			case "Permission List":
				DisplayPermissionList();
				break;
				
			case "Add Email":
				$cTabTable->AddTab("Email", "Add Email");
				$cTabTable->DisplayTabs("DisplayEmailForm");
				break;
			
			case "Modify Email":
				$cTabTable->AddTab("Email", "Modify Email", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Add Field", "Add Field", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Field List", "Field List", "EmailID=$EmailID", "IsTom");
				$cTabTable->DisplayTabs("DisplayEmailForm");
				break;
				
			case "Add Field":
				$cTabTable->AddTab("Email", "Modify Email", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Add Field", "Add Field", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Field List", "Field List", "EmailID=$EmailID", "IsTom");
				$cTabTable->DisplayTabs("DisplayFieldForm");
				break;
				
			case "Modify Field":
				if (!$EmailID > 0) {
					$cEmailFields = new cEmailFields($EmailFieldID);
					$EmailID = $cEmailFields->EmailID;
				}
				$cTabTable->AddTab("Email", "Modify Email", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Add Field", "Add Field", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Modify Field", "Modify Field", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Field List", "Field List", "EmailID=$EmailID", "IsTom");
				$cTabTable->DisplayTabs("DisplayFieldForm");
				break;
				
			case "Email List":
				DisplayEmailList();
				break;
				
			case "Field List":
				$cTabTable->AddTab("Email", "Modify Email", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Add Field", "Add Field", "EmailID=$EmailID", "IsTom");
				$cTabTable->AddTab("Field List", "Field List", "EmailID=$EmailID", "IsTom");
				$cTabTable->DisplayTabs("DisplayFieldList");
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
//	DisplayMasterSettingsForm
// ---------------------------------------------------------------------------------
function DisplayMasterSettingsForm() {
	$cMasterSettings = new cMasterSettings(1);
	$AuthDisplay = "none";
	if ($cMasterSettings->EmailAuth) {
		$AuthDisplay = "block";
	}
	?>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MasterSettings.php">
			<input type="hidden" name="FormComplete" value="1">
			
			<div class="FormTitleWide">Administrator</div>
			<div class="FormField"><?php DisplayAdministratorSelect($cMasterSettings->AdministratorID)?></div>
			<div class="clear"></div>
			
			<div class="FormTitleWide">Email Server</div>
			<div class="FormField"><input type="text" style="width: 250px;" name="EmailServer" value="<?php print $cMasterSettings->EmailServer ?>" /></div>
			<div class="clear"></div>
			
			<div class="FormTitleWide">SMTP Authentication</div>
			<div class="FormField"><input type="checkbox" onClick="DisplayAuthentication(this.checked);" value="1" name="EmailAuth" <?php if ($cMasterSettings->EmailAuth) { print " checked";}?></div>
			<div class="clear"></div>
			
			<div id="SMTPAuthVars" style="display: <?php print $AuthDisplay?>;">

				<div class="FormTitleWide">Email Username</div>
				<div class="FormField"><input type="text" style="width: 250px;" name="EmailUser" value="<?php print $cMasterSettings->EmailUser ?>" /></div>
				<div class="clear"></div>
				
				<div class="FormTitleWide">Email Password</div>
				<div class="FormField"><input type="text" style="width: 250px;" name="EmailPass" value="<?php print $cMasterSettings->EmailPass ?>" /></div>
				<div class="clear"></div>
			
			</div>
			
			<div class="FormTitleWide">Modified Date</div>
			<div class="FormField"><?php print FormatDate($cMasterSettings->ModifiedDate)?></div>
			<div class="clear"></div>
			
			<div class="FormTitleWide">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<div class="clear"></div>	
			
		</form>
	</div>
	
	<script type="text/javascript">
		var AuthDiv = $("SMTPAuthVars");
		var AuthSlide = new Fx.Slide(AuthDiv);
		<?php if (!$cMasterSettings->EmailAuth) { ?>
			AuthSlide.hide();
			AuthDiv.setStyle("display","block");
		<?php }?>
		function DisplayAuthentication(inChecked) {
			if(inChecked) {
				AuthSlide.slideIn();
			} else {
				AuthSlide.slideOut();
			}
		}
	</script>
	
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayAdministratorSelect
// ---------------------------------------------------------------------------------
function DisplayAdministratorSelect($AdministratorID) {
	$sqlSelect = "select * from cms_users";
	$tbl = ExecuteQuery($sqlSelect);
	print("<select name=\"AdministratorID\" style=\"width: 250px;\">\n");
	while ($row = mysql_fetch_object($tbl)) {
		$UserID = $row->UserID;
		$UserName = $row->UserName;
		$FirstName = $row->FirstName;
		$LastName = $row->LastName;
		print("\t<option value=\"$UserID\"");
		if ($UserID == $AdministratorID) {
			print(" selected");
		}
		print(">$FirstName $LastName [$UserName]</option>\n");
	}
	print("</select>\n");
}

// ---------------------------------------------------------------------------------
//	DisplayPermissionForm
// ---------------------------------------------------------------------------------
function DisplayPermissionForm() {
	global $PermissionID, $PageFunction;
	if ($PermissionID > 0) {
		$cPermissions = new cPermissions($PermissionID);
	} else {
		$cPermissions = new cPermissions();
	}
	?>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MasterSettings.php">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="PermissionID" value="<?php print $PermissionID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			
			<div class="FormTitle">Permission</div>
			<div class="FormField"><input type="text" style="width: 250px;" name="Permission" value="<?php print $cPermissions->Permission ?>" /></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField">
				<textarea name="Description" style="width: 250px;" rows="5"><?php print $cPermissions->Description ?></textarea>
			</div>
			<div class="clear"></div>
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($PermissionID > 0) { ?>
				<div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
			<?php } else { ?>
				<div class="FormField"><input type="button" onClick="SubmitAdd();return false;" class="button" value="Save & Add" title="Save and Add Another" /></div>
			<?php } ?>
			<div class="clear"></div>			
		</form>
		
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			frm.Permission.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Permission?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Permission";
					frm.submit();
				}
			}
			
			function SubmitAdd() {
				frm.AddAnother.value = "1";
				frm.submit();
			}
		</script>
	</div>
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayPermissionList
// ---------------------------------------------------------------------------------
function DisplayPermissionList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select * from cms_permissions";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Permission");
	$cDataGrid->SetSelectFunction("Delete Permission");
	$cDataGrid->SetModifyFunction("Modify Permission");
	$cDataGrid->SetPrimaryKey("PermissionID");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Permissions? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Permission? This action cannot be undone.");
	

	$cDataGrid->AddColumn("PermissionID", "PermissionID");
	$cDataGrid->AddColumn("Permission", "Permission");
	$cDataGrid->AddColumn("Description", "Description", array("trunc", 70));

	$cDataGrid->DisplayGrid();
}

// ---------------------------------------------------------------------------------
//	DisplayHistory
// ---------------------------------------------------------------------------------
function DisplayHistory() {
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetTabKey("MasterSettingID");
	$cHistoryDisplay->SetTabValue(1);
	
	$cHistoryDisplay->SetTableName("MasterSettings");
	$cHistoryDisplay->SetTableID(1);
	$cHistoryDisplay->DisplayHistory();
}



// ---------------------------------------------------------------------------------
//	DisplayEmailForm
// ---------------------------------------------------------------------------------
function DisplayEmailForm() {
	global $EmailID, $PageFunction;
	if ($EmailID > 0) {
		$cEmails = new cEmails($EmailID);
	} else {
		$cEmails = new cEmails();
	}
	?>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MasterSettings.php">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="EmailID" value="<?php print $EmailID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			
			<div class="FormTitle">Email Name</div>
			<div class="FormField"><input type="text" style="width: 250px;" name="EmailName" value="<?php print $cEmails->EmailName ?>" /></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Description</div>
			<div class="FormField"><input type="text" style="width: 250px;" Name="EmailDescription" value="<?php print $cEmails->EmailDescription ?>" /></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Category</div>
			<div class="FormField"><input type="text" style="width: 250px;" Name="EmailCategory" value="<?php print $cEmails->EmailCategory ?>" /></div>
			<div class="clear"></div>
			
			<?php if ($EmailID > 0) { ?>
				<div class="FormTitle">Modified Date</div>
				<div class="FormField"><?php print FormatDate($cEmails->ModifiedDate)?></div>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($EmailID > 0) { ?>
				<div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
			<?php } else { ?>
				<div class="FormField"><input type="button" onClick="SubmitAdd();return false;" class="button" value="Save & Add" title="Save and Add Another" /></div>
			<?php } ?>
			<div class="clear"></div>			
		</form>
		
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			frm.EmailName.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Email?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Email";
					frm.submit();
				}
			}
			
			function SubmitAdd() {
				frm.AddAnother.value = "1";
				frm.submit();
			}
		</script>
	</div>
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayFieldForm
// ---------------------------------------------------------------------------------
function DisplayFieldForm() {
	global $EmailFieldID, $EmailID, $PageFunction;
	$cEmails = new cEmails($EmailID);
	if ($EmailFieldID > 0) {
		$cEmailFields = new cEmailFields($EmailFieldID);
	} else {
		$cEmailFields = new cEmailFields();
	}
	?>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="MasterSettings.php">
			<input type="hidden" name="FormComplete" value="1">
			<input type="hidden" name="EmailID" value="<?php print $EmailID?>">
			<input type="hidden" name="EmailFieldID" value="<?php print $EmailFieldID?>">
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
			<input type="hidden" name="AddAnother" value="0">

			
			<div class="FormTitle">Email Name</div>
			<div class="FormField"><?php print $cEmails->EmailName ?></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Field Name</div>
			<div class="FormField"><input type="text" style="width: 250px;" Name="FieldName" value="<?php print $cEmailFields->FieldName ?>" /></div>
			<div class="clear"></div>
			
			<div class="FormTitle">Replace Value</div>
			<div class="FormField"><input type="text" style="width: 250px;" Name="ReplaceValue" value="<?php print $cEmailFields->ReplaceValue ?>" /></div>
			<div class="clear"></div>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<?php if ($EmailFieldID > 0) { ?>
				<div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
			<?php } else { ?>
				<div class="FormField"><input type="button" onClick="SubmitAdd();return false;" class="button" value="Save & Add" title="Save and Add Another" /></div>
			<?php } ?>
			<div class="clear"></div>			
		</form>
		
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			frm.FieldName.focus();
			
			function ConfirmDelete() {
				DeleteRecord = confirm('Are you certain you wish to delete this Email?\nThis action can not be undone.');
				if (DeleteRecord) {
					frm.PageFunction.value = "Delete Email";
					frm.submit();
				}
			}
			
			function SubmitAdd() {
				frm.AddAnother.value = "1";
				frm.submit();
			}
		</script>
	</div>
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayEmailList
// ---------------------------------------------------------------------------------
function DisplayEmailList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select * from cms_emails";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Email");
	$cDataGrid->SetSelectFunction("Delete Email");
	$cDataGrid->SetModifyFunction("Modify Email");
	$cDataGrid->SetPrimaryKey("EmailID");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Emails? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Email? This action cannot be undone.");
	$cDataGrid->SetSortBy("EmailCategory");
	$cDataGrid->SetSortDirection("desc");
	

	$cDataGrid->AddColumn("EmailID", "EmailID");
	$cDataGrid->AddColumn("EmailName", "EmailName");
	$cDataGrid->AddColumn("EmailCategory", "EmailCategory");
	$cDataGrid->AddColumn("EmailDescription", "EmailDescription", array("trunc", 70));

	$cDataGrid->DisplayGrid();
}

// ---------------------------------------------------------------------------------
//	DisplayFieldList
// ---------------------------------------------------------------------------------
function DisplayFieldList() {
	global $EmailID;
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select * from cms_emailfields where EmailID = $EmailID";
	$cDataGrid->SetQuery($sqlSelect);
	$cDataGrid->SetDeleteFunction("Delete Field");
	$cDataGrid->SetSelectFunction("Delete Field");
	$cDataGrid->SetModifyFunction("Modify Field");
	$cDataGrid->SetPrimaryKey("EmailFieldID");
	$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Email Fields? This action cannot be undone.");
	$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Email Field? This action cannot be undone.");
	

	$cDataGrid->AddColumn("EmailFieldID", "EmailFieldID");
	$cDataGrid->AddColumn("FieldName", "FieldName");
	$cDataGrid->AddColumn("ReplaceValue", "ReplaceValue");

	$cDataGrid->DisplayGrid();
}



?>