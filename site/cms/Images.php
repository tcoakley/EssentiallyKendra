<?php
// #################################################################################
//  require_onces
// #################################################################################
require_once("_includes/constants.php");
require_once("_includes/functions.php");
require_once("_includes/functions_site.php");

// #################################################################################
//  Validation
// #################################################################################
ValidateLogin("Images");


// #################################################################################
//  Initialization
// #################################################################################
$PageTitle = "Images";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Images List");
$AddAnother = RequestBool("AddAnother");
$AutoCrop = RequestBool("AutoCrop");

$ImageID = RequestInt("ImageID");
$arrImageID = RequestArray("ImageID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add Image", "Images.php?PageFunction=Add+Image", "Images");
$cQuickNav->AddNav("Image List", "Images.php?PageFunction=Image+List", "Images");


// #################################################################################
//  MainProcessing
// #################################################################################
if ($FormComplete) {

    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Add/Modify Image
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "Add Image" || $PageFunction == "Modify Image") {
        $cImages = new cImages();
        $cImages->LoadFieldsFromForm();
        $cImages->SetAutoCrop(true);
        if ($ImageID > 0) {
            $cImages->SetTableID($ImageID);
            if ($cImages->ModifyRecord()) {
                AddIncomingMessage("Image modified");
            }
        } else {
            $ImageID = $cImages->InsertRecord();
            if (is_numeric($ImageID)) {
                AddIncomingMessage("Image created");
            } else {
                AddErrorMessage($ImageID);
                $ImageId = 0;
            }
        }
        if ($AddAnother) {
            RedirectPage("Images.php?PageFunction=Add+Image");
        } else {
            RedirectPage("Images.php?PageFunction=Modify+Image&ImageID=$ImageID");
        }
    }
    
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Delete Images
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "Delete Images") {
        $cImages = new cImages();
        $cImages->DeleteRecord($arrImageID);
        RedirectPage("Images.php?PageFunction=Image+List");
    }
    
    
}


// #################################################################################
//  Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css,MooCalendar007/MooCalendar007.css";  // Add any additional style sheets you want require_onced here
$JavaLibraries = "milkbox.js,MooDate007.js,MooCalendar007.js"; // Add any java libraries you want  here  
switch ($PageFunction) {
    case "Images History":
        $OnPageLoad = "PrepSlides();";
        break;
        
    case "Add Image":
    case "Modify Image":
        $OnPageLoad = "";
        break;
           
    default:
        $OnPageLoad = ""; 
        break;
}
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
        if ($PageFunction == "Add Image") {
            $cTabTable->AddTab("Images", "Add Image");
        } else {
            $cTabTable->AddTab("Images", "Modify Image", "ImageID=$ImageID");
            $cTabTable->AddTab("History", "Images History", "ImageID=$ImageID");
        }
        
        
        switch ($PageFunction) {
            case "Add Image":
                $cTabTable->DisplayTabs("DisplayImageForm");
                break;
            
            case "Image List":
                $cTabTable->DisplayTabs("DisplayImagesList");
                break;
                
            case "Modify Image":
                $cTabTable->DisplayTabs("DisplayImageForm");
                break;
                               
            case "Images History":
                $cTabTable->DisplayTabs("DisplayImagesHistory");
                break;
        }
    ?>

</div>
<!-- /Content area -->


<?php require_once("_includes/ClosePage.php"); ?>

<?php
// #################################################################################
//  Functions
// #################################################################################


// ---------------------------------------------------------------------------------
//  DisplayImageForm
// ---------------------------------------------------------------------------------
function DisplayImageForm() {
    global $ImageID, $PageFunction;
    if ($ImageID > 0) {
        $cImages = new cImages($ImageID);
    } else {
        $cImages = new cImages();
        $cImages->Orientation = "horizontal";
    }
    include_once("fckeditor/fckeditor_php5.php");
    ?>

    <div id="FormCanisterMaster">
        <form name="frmMain" method="post" action="Images.php" enctype="multipart/form-data" class="MooValidator">
            <input type="hidden" name="FormComplete" value="1">
            <input type="hidden" name="ImageID" value="<?php print $ImageID?>">
            <input type="hidden" name="CategoryID" value="1">
            <input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
            <input type="hidden" name="AddAnother" value="0">

            <?php if ($ImageID > 0 ) { ?>
                <div class="FormTitle">ID</div>
                <div class="FormField"><?php print $ImageID ?></div>
                <?php if  ($_SESSION["AddingImages"]) { ?>
                    <div class="FloatLeft" style="margin: 0 0 0 50px;">
                        <input type="button" class="button" value="Continue" onClick="document.location.href='Images.php?PageFunction=Video&ImageID=<?php print $ImageID ?>'">
                    </div>
                <?php } ?>
                <div class="clear"></div>
            <?php } ?>
            
            <div class="FormTitle">Salesman</div>
            <div class="FormField"><?php DisplayOwnerSelect($cImages->Owner)?></div>
            <div class="clear"></div>            
            
            <div class="FormTitle">Customer</div>
            <div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="Customer" value="<?php print $cImages->Customer ?>"></div>
            <div class="clear"></div>
            
            <div class="FormTitle">Customer Email</div>
            <div class="FormField"><input type="text" class="req" alt="em-0" style="width: 250px;" name="CustomerEmail" value="<?php print $cImages->CustomerEmail ?>"></div>
            <div class="clear"></div>

            <div class="FormTitle">Image</div>
            <div class="FormField"><input type="file" style="width: 250px;" name="Image"></div>
            <div class="clear"></div>
            
            <?php if (strlen($cImages->Image) > 4) { ?>
                <div class="FormTitle">Current Image</div>
                <div class="FormField"><?php $cImages->DisplayImageLink("Image", "PageFunction=Modify+Image&ImageID=$ImageID");?></div>
                <div class="clear"></div>
                
                <div class="FormTitle">Customer Image</div>
                <div class="FormField"><?php $cImages->DisplayCustomerImage();?></div>
                <div class="clear"></div>
            <?php } ?>
            
            
            <?php if ($ImageID > 0 ) { ?>
                <div class="FormTitle">Created Date</div>
                <div class="FormField"><?php print FormatDate($cImages->CreatedDate)?></div>
                <div class="clear"></div>
                
                <div class="FormTitle">Modified Date</div>
                <div class="FormField"><?php print FormatDate($cImages->ModifiedDate)?></div>
                <div class="clear"></div>

            <?php } ?>
            
            
            <div class="FormTitle">&nbsp;</div>
            <div class="FormField"><input type="submit" class="button" value="Save" /></div>
            <?php if ($ImageID > 0) { ?>
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
            frm.ImageTitle.focus();
            
            function ConfirmDelete() {
                DeleteRecord = confirm('Are you certain you wish to delete this Image?\nThis action can not be undone.');
                if (DeleteRecord) {
                    frm.PageFunction.value = "Delete Images";
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
//  DisplayOwnerSelect
// ---------------------------------------------------------------------------------
function DisplayOwnerSelect($Owner) {
    if(is_null($Owner) || $Owner < 1) {
        $Owner = $_SESSION["UserID"];
    }
    $sqlSelect = "select FirstName, LastName, UserName, UserID from cms_users order by LastName, FirstName";
    $tbl = ExecuteQuery($sqlSelect);
    if (mysql_num_rows($tbl) < 1) {
        print "No users currently exist.";
    } else {
        if ($_SESSION["User"]->IsManager == 1) {
            print "<select name=\"Owner\">\n";
            while ($row = mysql_fetch_object($tbl)) {
                $UserID = $row->UserID;
                $FirstName = $row->FirstName;
                $LastName = $row->LastName;
                $UserName = $row->UserName;
                print " <option value=\"$UserID\"";
                if ($UserID == $Owner) {
                    print " selected";
                }
                print ">$LastName, $FirstName [$UserName]</option>\n";
            }
            print "</select>";
        } else {
            $sqlSelect = "select FirstName, LastName, UserName, UserID from cms_users where UserID = $Owner order by LastName, FirstName";
            $tbl = ExecuteQuery($sqlSelect);
            $row = mysql_fetch_object($tbl);
            $UserID = $row->UserID;
            $FirstName = $row->FirstName;
            $LastName = $row->LastName;
            $UserName = $row->UserName;
            print "<input type=\"hidden\" name=\"Owner\" value=\"" . $_SESSION["UserID"] . "\">\n" .
               "$LastName, $FirstName [$UserName]";
        }
    }
    
}


// ---------------------------------------------------------------------------------
//  DisplayImagesList
// ---------------------------------------------------------------------------------
function DisplayImagesList() {
    $cDataGrid = new cDataGrid();
    if ($_SESSION["User"]->IsManager) {
        $sqlSelect = "select ImageID, Image, UserName, UNIX_TIMESTAMP(Images.CreatedDate) as CreatedDate, UNIX_TIMESTAMP(Images.ModifiedDate) as ModifiedDate" .
            " from cms_images Images" .
            " left join cms_users Users on Images.Owner = Users.UserID";
    } else {
        $sqlSelect = "select ImageID, Image, UserName, UNIX_TIMESTAMP(Images.CreatedDate) as CreatedDate, UNIX_TIMESTAMP(Images.ModifiedDate) as ModifiedDate" .
            " from cms_images Images" .
            " left join cms_users Users on Images.Owner = Users.UserID" .
            " where Owner = " . $_SESSION["UserID"];
    }
        
    $cDataGrid->SetQuery($sqlSelect);
    $cDataGrid->SetDeleteFunction("Delete Images");
    $cDataGrid->SetSelectFunction("Delete Images");
    $cDataGrid->SetModifyFunction("Modify Image");
    $cDataGrid->SetPrimaryKey("ImageID");
    $cDataGrid->SetSortBy("Images.CreatedDate");
    $cDataGrid->SetSortDirection("desc");
    $cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Images? This action cannot be undone.");
    $cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this Images? This action cannot be undone.");
    $cDataGrid->SetFiltering(true); 

    $cDataGrid->AddColumn("ID", "ImageID", null, "int");
    $cDataGrid->AddColumn("Image", "Image", null, "string");
    $cDataGrid->AddColumn("Salesman", "UserName", null, "int");
    $cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%Y"), "date");
    $cDataGrid->AddColumn("Modified", "ModifiedDate", array("Date","%m/%d/%Y"), "date");

    $cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//  DisplayImagesHistory
// ---------------------------------------------------------------------------------
function DisplayImagesHistory() {
    global $ImageID;
    $cHistoryDisplay = new cHistoryDisplay();
    $cHistoryDisplay->SetTabKey("ImageID");
    $cHistoryDisplay->SetTabValue($ImageID);
    
    $cHistoryDisplay->SetTableName("cms_images");
    $cHistoryDisplay->SetTableID($ImageID);
    $cHistoryDisplay->DisplayHistory();
}
?>