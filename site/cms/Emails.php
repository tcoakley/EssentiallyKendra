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
ValidateLogin("Emails");


// #################################################################################
//	Initialization
// #################################################################################
$PageTitle = "Emails";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Email List");
$AddAnother = RequestBool("AddAnother");


$EmailID = RequestInt("EmailID");

$cQuickNav = new cQuickNav();


if(is_null($EmailID) || $EmailID < 1) {
	$sqlSelect = "Select EmailID from cms_emails limit 1";
	$tbl = ExecuteQuery($sqlSelect);
	$row = mysql_fetch_object($tbl);
	if ($row) {
		$EmailID = $row->EmailID;
	}
}




// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

	

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Modify Email
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$cEmails = new cEmails();
	$cEmails->LoadFieldsFromForm();
	$cEmails->SetTableID($EmailID);
	if ($cEmails->ModifyRecord()) {
		AddIncomingMessage("Email modified");
	}
	RedirectPage("Emails.php?EmailID=$EmailID");

}


// #################################################################################
//	Display
// #################################################################################
$StyleSheets = "";  // Add any additional style sheets you want require_onced here

switch ($PageFunction) {
	case "Email History":
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

<?php require_once("_includes/Messages.php"); ?>

<!-- Content area -->
<div id="ContentDiv">

	<?php
		$cTabTable = new cTabTable(120);
		$cTabTable->AddTab("Email", "Modify Email", "EmailID=$EmailID");
		$cTabTable->AddTab("History", "Email History", "EmailID=$EmailID");
		
		switch ($PageFunction) {
			case "Modify Email":
				$cTabTable->DisplayTabs("DisplayEmailForm");
				break;
				
			case "Email List":
				DisplayEmailList();
				break;
			
			case "Email History":
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
//	DisplayEmailForm
// ---------------------------------------------------------------------------------
function DisplayEmailForm() {
	global $EmailID, $PageFunction;
	$cEmails = new cEmails($EmailID);
	include_once("fckeditor/fckeditor_php5.php");
	?>
	<div id="FormDirections">
		<strong>
			The values below including the curly braces {} can be pasted into the body
			fields and will be replaced with the appropriate values when sent.
		</strong>
		<br><hr>
		<?php
			$sqlSelect = "select * from cms_emailfields where EmailID = $EmailID";
			$tbl = ExecuteQuery($sqlSelect);
			while ($row = mysql_fetch_object($tbl)) {
				$ReplaceValue = $row->ReplaceValue;
				print("<div>{$ReplaceValue}</div>\n");
			}
		?>
	</div>
	<div id="FormCanisterMaster">
		<form name="frmMain" method="post" action="Emails.php">
			<input type="hidden" name="FormComplete" value="1" />
			<input type="hidden" name="EmailID" value="<?php print $EmailID?>" />
			<input type="hidden" name="PageFunction" value="<?php print $PageFunction?>" />
			
			<div class="FormTitle">Name</div>
			<div class="FormField"><b><?php print $cEmails->EmailName?></b></div>
			<div class="clear"></div>

			<div class="FormTitle">Description</div>
			<div class="FormField"><?php print $cEmails->EmailDescription?></div>
			<div class="clear"></div>

			
			<div class="FormTitle">Subject</div>
			<div class="FormField"><input type="text" name="EmailSubject" style="width: 350px;" value="<?php print $cEmails->EmailSubject ?>"></div>
			<div class="clear"></div>
			
			<div class="FormTitle">HTML Body</div>
			<div class="FormField" style="width: 550px;">
				<?php
				$oFCKeditor = new FCKeditor("EmailBodyHtml");
				$oFCKeditor->BasePath = "fckeditor/";
				$oFCKeditor->Value = $cEmails->EmailBodyHtml;
				$oFCKeditor->Height = 300;
				$oFCKeditor->Create();
				?>
			</div>
			<div class="clear"></div>
			
			<div class="FormTitle">Text Body</div>
			<div class="FormField"><textarea style="width: 350px; height:200px;" name="EmailBodyText"><?php print $cEmails->EmailBodyText ?></textarea></div>
			<div class="clear"></div>
						
			<div class="FormTitle">Modified Date</div>
			<div class="FormField"><?php print FormatDate($cEmails->ModifiedDate)?></div>
			<div class="clear"></div>
			
			
			<div class="FormTitle">&nbsp;</div>
			<div class="FormField"><input type="submit" class="button" value="Save" /></div>
			<div class="clear"></div>			
		</form>
		
		<script type="text/javascript">		
			var frm = document.forms["frmMain"];
			frm.EmailSubject.focus();
		</script>
	</div>
	<?php
}

// ---------------------------------------------------------------------------------
//	DisplayEmailList
// ---------------------------------------------------------------------------------
function DisplayEmailList() {
	$cDataGrid = new cDataGrid();
	$sqlSelect = "select EmailID, EmailName, EmailDescription, UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate from cms_emails";
	$cDataGrid->SetQuery($sqlSelect);
	//$cDataGrid->SetDeleteFunction("Delete User");
	//$cDataGrid->SetSelectFunction("Delete User");
	$cDataGrid->SetModifyFunction("Modify Email");
	$cDataGrid->SetPrimaryKey("EmailID");
	$cDataGrid->SetSortBy("EmailName");
	$cDataGrid->SetSortDirection("asc");
	$cDataGrid->SetCanSelect(false);
	$cDataGrid->SetCanDelete(false);
	
	//$cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Users? This action cannot be undone.");
	//$cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this User? This action cannot be undone.");
	$cDataGrid->SetFiltering(false);
	

	$cDataGrid->AddColumn("EmailID", "EmailID", null, "int");
	$cDataGrid->AddColumn("Email Name", "EmailName");
	$cDataGrid->AddColumn("Description", "EmailDescription");

	$cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%y"), "date");

	$cDataGrid->DisplayGrid();
}

// ---------------------------------------------------------------------------------
//	DisplayHistory
// ---------------------------------------------------------------------------------
function DisplayHistory() {
	global $EmailID;
	$cHistoryDisplay = new cHistoryDisplay();
	$cHistoryDisplay->SetPrimaryKey("HistoryID");
	$cHistoryDisplay->SetTabKey("EmailID");
	$cHistoryDisplay->SetTabValue($EmailID);
	
	$cHistoryDisplay->SetTableName("Emails");
	$cHistoryDisplay->SetTableID($EmailID);
	$cHistoryDisplay->DisplayHistory();
}
?>