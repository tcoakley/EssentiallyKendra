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
ValidateLogin("Users");


// #################################################################################
//  Initialization
// #################################################################################
$PageTitle = "Users";

$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "User List");
$AddAnother = RequestBool("AddAnother");

$UserID = RequestInt("UserID");
$arrUserID = RequestArray("UserID");
$arrGroupID = RequestArray("GroupID");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Add User", "Users.php?PageFunction=Add+User", "Users");
$cQuickNav->AddNav("User List", "Users.php?PageFunction=User+List", "Users");
$cQuickNav->AddNav("Add Group", "Groups.php?PageFunction=Add+Group", "Groups");
$cQuickNav->AddNav("Group List", "Groups.php?PageFunction=Group+List", "Groups");



// #################################################################################
//  MainProcessing
// #################################################################################
if ($FormComplete) {

    

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Add/Modify User
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "Add User" || $PageFunction == "Modify User") {
        $cUsers = new cUsers();
        $cUsers->LoadFieldsFromForm();
        if ($UserID > 0) {
            $cUsers->SetTableID($UserID);
            if ($cUsers->ModifyRecord()) {
                AddIncomingMessage("User modified");
            }
        } else {
            $UserID = $cUsers->InsertRecord();
            if ($UserID) {
                AddIncomingMessage("User created");
            }
        }
        if ($AddAnother) {
            RedirectPage("Users.php?PageFunction=Add+User");
        } else {
            RedirectPage("Users.php?PageFunction=Modify+User&UserID=$UserID");
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Delete User
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "Delete User") {
        $cUsers = new cUsers();
        $cUsers->DeleteRecord($arrUserID);
        RedirectPage("Users.php?PageFunction=User+List");
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Send Info
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "Send Info") {
        $cUsers = new cUsers($UserID);
        $cEmails = new cEmails();
        $cEmails->AddReplacementField("UserName", $cUsers->UserName);
        $cEmails->AddReplacementField("FirstName", $cUsers->FirstName);
        $cEmails->AddReplacementField("LastName", $cUsers->LastName);
        $cEmails->AddReplacementField("Password", $cUsers->Password);
        $cEmails->SendEmail("Forgot Password", $Email, $cUsers->FirstName . " " . $cUsers->LastName);
        AddIncomingMessage("Login Information sent");
        RedirectPage("Users.php?PageFunction=Modify+User&UserID=$UserID");
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  User Groups
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "User Groups") {
        $cUsers = new cUsers($UserID);
        $cUsers->AddAssociation("cms_usergroups", "GroupID", $arrGroupID);
        if ($cUsers->ModifyRecord()) {
            AddIncomingMessage("User groups modified");
        }
        RedirectPage("Users.php?PageFunction=User+Groups&UserID=$UserID");
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  User Permissions
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "User Permissions") {
        $cUsers = new cUsers($UserID);
        $cUsers->AddAssociation("cms_userpermissions", "Permission", RequestArray("Permission"));
        if ($cUsers->ModifyRecord()) {
            AddIncomingMessage("User permissions modified");
        }
        RedirectPage("Users.php?PageFunction=User+Permissions&UserID=$UserID");
    }
        
}


// #################################################################################
//  Display
// #################################################################################
$StyleSheets = "milkbox/milkbox.css";  // Add any additional style sheets you want require_onced here
$JavaLibraries = "milkbox.js"; // Add any java libraries you want  here 
switch ($PageFunction) {
    case "User History":
    case "User Activity":
        $OnPageLoad = "PrepSlides();";
        break;
        
    case "Add User":
    case "Modify User":
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
        if ($PageFunction == "Add User") {
            $cTabTable->AddTab("User", "Add User");
        } else {
            $cTabTable->AddTab("User", "Modify User", "UserID=$UserID");
            $cTabTable->AddTab("Groups", "User Groups", "UserID=$UserID");  
            $cTabTable->AddTab("Permissions", "User Permissions", "UserID=$UserID");
            $cTabTable->AddTab("History", "User History", "UserID=$UserID");
            $cTabTable->AddTab("Activity", "User Activity", "UserID=$UserID");
        }
        
        
        switch ($PageFunction) {
            case "Add User":
                $cTabTable->DisplayTabs("DisplayUserForm");
                break;
            
            case "Modify User":
                $cTabTable->DisplayTabs("DisplayUserForm");
                break;
                
            case "User List":
                DisplayUserList();
                break;
                
            case "User Permissions":
                $cTabTable->DisplayTabs("DisplayPermissionsForm");
                break;
                
            case "User Groups":
                $cTabTable->DisplayTabs("DisplayGroupForm");
                break;
                
            case "User History":
                $cTabTable->DisplayTabs("DisplayHistory");
                break;
                
            case "User Activity":
                $cTabTable->DisplayTabs("DisplayActivity");
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
//  DisplayUserForm
// ---------------------------------------------------------------------------------
function DisplayUserForm() {
    global $UserID, $PageFunction;
    if ($UserID > 0) {
        $cUsers = new cUsers($UserID);
    } else {
        $cUsers = new cUsers();
    }
    ?>
    <div id="FormCanisterMaster">
        <form name="frmMain" method="post" action="Users.php" enctype="multipart/form-data" class="MooValidator">
            <input type="hidden" name="FormComplete" value="1">
            <input type="hidden" name="UserID" value="<?php print $UserID?>">
            <input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
            <input type="hidden" name="AddAnother" value="0">

            <?php if ($UserID > 0 ) { ?>
                <div class="FormTitle">ID</div>
                <div class="FormField"><?php print $UserID ?></div>
                <div class="clear"></div>
            <?php } ?>
            
            <div class="FormTitle">Username</div>
            <?php if ($PageFunction == "Add User") { ?>
                <div class="FormField"><input type="text" class="req" alt="ml-3,an-0,uq-_ajax/CheckUserName.php" style="width: 250px;" name="UserName" value="<?php print $cUsers->UserName ?>"></div>
            <?php } else { ?>   
                <div class="FormField"><?php print $cUsers->UserName ?></div>
            <?php } ?>
            <div class="clear"></div>
            
            <div class="FormTitle">Password</div>
            <div class="FormField"><input type="text" class="req" alt="ml-3" style="width: 250px;" name="Password" value="<?php print $cUsers->Password ?>"></div>
            <div class="clear"></div>
            
            <div class="FormTitle">First Name</div>
            <div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="FirstName" value="<?php print $cUsers->FirstName ?>"></div>
            <div class="clear"></div>
            
            <div class="FormTitle">Last Name</div>
            <div class="FormField"><input type="text" class="req" alt="ml-1" style="width: 250px;" name="LastName" value="<?php print $cUsers->LastName ?>"></div>
            <div class="clear"></div>
            
            <div class="FormTitle">Email</div>
            <div class="FormField"><input type="text" class="req" alt="em-0" style="width: 250px;" name="Email" value="<?php print $cUsers->Email ?>"></div>
            <div class="clear"></div>
            
            <div class="FormTitle">Watermark Image</div>
            <div class="FormField"><input type="file" style="width: 250px;" name="OverlayImage"></div>
            <div class="clear"></div>
            
            <?php if (strlen($cUsers->OverlayImage) > 4) { ?>
                <div class="FormTitle">Current Thumb</div>
                <div class="FormField"><?php $cUsers->DisplayImageLink("OverlayImage", "PageFunction=Modify+User&UserID=$UserID");?></div>
                <div class="clear"></div>
            <?php } ?>
            
            <div class="FormTitle">Is a Manager</div>
            <div class="FormField"><input type="checkbox" name="IsManager" value="1" <?php if($cUsers->IsManager) { print "checked"; }?>></div>
            <div class="clear"></div>
            
            <div class="FormTitle">Manager</div>
            <div class="FormField"><?php DisplayManagerSelect($cUsers->Manager); ?></div>
            <div class="clear"></div>

            
            <?php if ($UserID > 0 ) { ?>
                <div class="FormTitle">Created Date</div>
                <div class="FormField"><?php print FormatDate($cUsers->CreatedDate)?></div>
                <div class="clear"></div>
                
                <div class="FormTitle">Modified Date</div>
                <div class="FormField"><?php print FormatDate($cUsers->ModifiedDate)?></div>
                <div class="clear"></div>
                
                <div class="FormTitle">Last Login</div>
                <div class="FormField"><?php print FormatDate($cUsers->LoginDate)?></div>
                <div class="clear"></div>
            <?php } ?>
            
            
            <div class="FormTitle">&nbsp;</div>
            <div class="FormField"><input type="submit" class="button" value="Save" /></div>
            <?php if ($UserID > 0) { ?>
                <div class="FormField"><input type="button" class="button" value="Delete" onClick="ConfirmDelete();" /></div>
                <div class="FormField"><input type="button" class="button" value="Send Info" title="Send Login Information" onClick="SendInfo();" /></div>
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
            if (frm.UserName) {
                frm.UserName.focus();
            } else {
                frm.Password.focus();
            }

            function ConfirmDelete() {
                DeleteRecord = confirm('Are you certain you wish to delete this User?\nThis action can not be undone.');
                if (DeleteRecord) {
                    frm.PageFunction.value = "Delete User";
                    frm.submit();
                }
            }
            
            function SendInfo() {
                frm.PageFunction.value = "Send Info";
                frm.submit();
            }
            
            function SubmitAdd() {
                frm.AddAnother.value = "1";
            }
        </script>
    </div>
    <?php
}

// ---------------------------------------------------------------------------------
//  DisplayManagerSelect
// ---------------------------------------------------------------------------------
function DisplayManagerSelect($Manager) {
    $sqlSelect = "select UserID, FirstName, LastName from cms_users where IsManager = 1";
    $tbl = ExecuteQuery($sqlSelect);
    print "<select name=\"Manager\">\n" .
        "   <option value=\"\" style=\"width: 250px;\">Select manager</option>\n";
    while ($row = mysql_fetch_object($tbl)) {
        $UserID = $row->UserID;
        $FirstName = $row->FirstName;
        $LastName = $row->LastName;
        print " <option value=\"$UserID\"";
        if (!is_null($Manager) && $UserID == $Manager) {
            print " selected";
        }
        print ">$LastName, $FirstName</option>\n";
    }
    print "</select>\n";
}

// ---------------------------------------------------------------------------------
//  DisplayUserList
// ---------------------------------------------------------------------------------
function DisplayUserList() {
    $cDataGrid = new cDataGrid();
    $sqlSelect = "select UserID, FirstName, LastName, UserName, UNIX_TIMESTAMP(LoginDate) as LoginDate, UNIX_TIMESTAMP(CreatedDate) as CreatedDate from cms_users";
    $cDataGrid->SetQuery($sqlSelect);
    $cDataGrid->SetDeleteFunction("Delete User");
    $cDataGrid->SetSelectFunction("Delete User");
    $cDataGrid->SetModifyFunction("Modify User");
    $cDataGrid->SetPrimaryKey("UserID");
    $cDataGrid->SetSortBy("UserName");
    $cDataGrid->SetSortDirection("asc");
    $cDataGrid->SetSelectConfirmation("Are you certain you wish to delete the selected Users? This action cannot be undone.");
    $cDataGrid->SetDeleteConfirmation("Are you certain you wish to delete this User? This action cannot be undone.");
    $cDataGrid->SetFiltering(true);
    

    $cDataGrid->AddColumn("UserID", "UserID", null, "int");
    $cDataGrid->AddColumn("User Name", "UserName");
    $cDataGrid->AddColumn("First Name", "FirstName");
    $cDataGrid->AddColumn("Last Name", "LastName");
    $cDataGrid->AddColumn("Last Login", "LoginDate", array("Date","%m/%d/%y %H:%M.%S"), "date");
    $cDataGrid->AddColumn("Created", "CreatedDate", array("Date","%m/%d/%y"), "date");

    $cDataGrid->DisplayGrid();
}



// ---------------------------------------------------------------------------------
//  DisplayGroupForm
// ---------------------------------------------------------------------------------
function DisplayGroupForm() {
    global $UserID, $PageFunction;
    $cUsers = new cUsers($UserID);
    $sqlSelect = "Select GroupID from cms_usergroups where UserID = $UserID";
    $tbl = ExecuteQuery($sqlSelect);
    $arrGroups = array();
    while ($row = mysql_fetch_object($tbl)) {
        $GroupID = $row->GroupID;
        array_push($arrGroups, $GroupID);
    }
    ?>
    <div id="FormCanisterMaster">
        <form name="frmMain" method="post" action="Users.php">
            <input type="hidden" name="FormComplete" value="1" />
            <input type="hidden" name="UserID" value="<?php print $UserID?>" />
            <input type="hidden" name="PageFunction" value="<?php print $PageFunction?>" />
            <?php
            $sqlSelect = "select * from cms_groups";
            $tbl = ExecuteQuery($sqlSelect);
            while ($row = mysql_fetch_object($tbl)) {
                $GroupID = $row->GroupID;
                $GroupName = $row->GroupName;
                $GroupDescription = $row->GroupDescription;
                print("<div class=\"FormTitle\">$GroupName</div>\n");
                if (in_array($GroupID, $arrGroups)) {
                    print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"GroupID[]\" value=\"$GroupID\" checked></div>\n");
                } else {
                    print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"GroupID[]\" value=\"$GroupID\"></div>\n");
                }
                print("<div class=\"FormField\">$GroupDescription</div>\n");
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
//  DisplayPermissionsForm
// ---------------------------------------------------------------------------------
function DisplayPermissionsForm() {
    global $UserID, $PageFunction;
    $cUsers = new cUsers($UserID);
    $sqlSelect = "select Permission from cms_userpermissions where UserID = $UserID";
    $tbl = ExecuteQuery($sqlSelect);
    $arrPermissions = array();
    while ($row = mysql_fetch_object($tbl)) {
        $Permission = $row->Permission;
        $arrPermissions[] = $Permission;
    }
    $sqlSelect = "select ugrp.GroupID, GroupName from cms_usergroups ugrp" .
        " left join cms_groups grp on ugrp.GroupID = grp.GroupID" .
        " where UserID = $UserID";
    $tbl = ExecuteQuery($sqlSelect);
    $arrGroups = array();
    $arrGroupPermissions = array();
    $arrGroupNames = array();
    while ($row = mysql_fetch_object($tbl)) {
        $GroupID = $row->GroupID;
        $GroupName = $row->GroupName;
        $arrGroupNames[$GroupID] = $GroupName;
        array_push($arrGroups, $GroupID);
        $sqlSelect = "select Permission from cms_grouppermissions where GroupID = $GroupID";
        $tbl2 = ExecuteQuery($sqlSelect);
        $GroupPermissions = array();
        while ($row = mysql_fetch_object($tbl2)) {
            $Permission = $row->Permission;
            array_push($GroupPermissions, $Permission);
        }
        $arrGroupPermissions[$GroupID] = $GroupPermissions;
    }
    ?>
    <div id="FormCanisterMaster">
        <form name="frmMain" method="post" action="Users.php">
            <input type="hidden" name="FormComplete" value="1">
            <input type="hidden" name="UserID" value="<?php print $UserID?>">
            <input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">
            <?php
            $sqlSelect = "select * from cms_permissions";
            $tbl = ExecuteQuery($sqlSelect);
            while ($row = mysql_fetch_object($tbl)) {
                $Permission = $row->Permission;
                $Description = $row->Description;
                print("<div class=\"FormTitle\">$Permission</div>\n");
                if (in_array($Permission, $arrPermissions)) {
                    print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"Permission[]\" value=\"$Permission\" checked></div>\n");
                } else {
                    print("<div class=\"FormField\"><input type=\"checkbox\" class=\"CheckBox\" name=\"Permission[]\" value=\"$Permission\"></div>\n");
                }
                $IconWritten = false;
                $DisplayGroups = "";
                print("<div class=\"FormField\">");
                foreach($arrGroups as $GroupID) {
                    if (in_array($Permission, $arrGroupPermissions[$GroupID])) {
                        $IconWritten = true;
                        if ($DisplayGroups != "") {
                            $DisplayGroups .= "<br>";
                        }
                        $DisplayGroups .= $arrGroupNames[$GroupID];
                    }
                }
                if ($IconWritten) {
                    print("<a href=\"Users.php?UserID=$UserID&PageFunction=User+Groups\" class=\"Tips\" title=\"From Groups\" rel=\"" .
                    $DisplayGroups . "\"><img src=\"_img/GroupPermission.jpg\" width=\"13\" height=\"13\" border=\"0\"></a>\n");
                } else {
                    print("&nbsp;");
                }
                print("</div>");
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

        </form>
        <div class="clear"></div>
    </div>
    <script type="text/javascript">
        //SetChecks
        var tip = new Tips('.Tips');
        function SetChecks(inValue) {
            $$("input.CheckBox").each(function(el) {
                el.setProperty("checked", inValue);
            });
        }
    </script>
    <?php
}

// ---------------------------------------------------------------------------------
//  DisplayHistory
// ---------------------------------------------------------------------------------
function DisplayHistory() {
    global $UserID;
    $cHistoryDisplay = new cHistoryDisplay();
    $cHistoryDisplay->SetTabKey("UserID");
    $cHistoryDisplay->SetTabValue($UserID);
    
    $cHistoryDisplay->SetTableName("cms_users");
    $cHistoryDisplay->SetTableID($UserID);
    $cHistoryDisplay->DisplayHistory();
}

// ---------------------------------------------------------------------------------
//  DisplayActivity
// ---------------------------------------------------------------------------------
function DisplayActivity() {
    global $UserID;
    $cHistoryDisplay = new cHistoryDisplay();
    $cHistoryDisplay->SetTabKey("UserID");
    $cHistoryDisplay->SetTabValue($UserID);
    
    //$cHistoryDisplay->SetTableName("cms_users");
    //$cHistoryDisplay->SetTableID($UserID);
    $cHistoryDisplay->SetUserID($UserID);
    $cHistoryDisplay->AddFormValue("UserID", $UserID);
    
    $cHistoryDisplay->DisplayHistory();
}
?>