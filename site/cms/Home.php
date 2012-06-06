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
ValidateLogin("Welcome");


// #################################################################################
//  Initialization
// #################################################################################
$PageTitle = "Home";
$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
DefaultString($PageFunction, "Welcome");

$cQuickNav = new cQuickNav();
$cQuickNav->AddNav("Home", "Home.php");
$cQuickNav->AddNav("My Account", "Home.php?PageFunction=My+Account");

// #################################################################################
//  MainProcessing
// #################################################################################
if ($FormComplete) {
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Modify My Account
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($PageFunction == "My Account") {
        $cUsers = new cUsers($_SESSION["UserID"]);
        $cUsers->LoadFieldsFromForm();
        $cUsers->SetTableID($_SESSION["UserID"]);
        if ($cUsers->ModifyRecord()) {
            AddIncomingMessage("Account Modified");
        }
        RedirectPage("Home.php?PageFunction=My+Account");
    }
}

// #################################################################################
//  Display
// #################################################################################
$StyleSheets = "";  // Add any additional style sheets you want require_onced here
switch ($PageFunction) {
    case "My Account":
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
<style>
.SummaryBox {
    border: 1px solid #262626;
    padding: 0;
    width: 250px;
    margin:0
}
.SummaryBox .Title {
    background: #161616;
    padding: 3px 5px 3px 5px;
    color:#29b900;
    font-weight: bold;
}
.SummaryBox .Canister {
    padding: 5px;
}
.SummaryBox .Canister .Indent {
    margin-left: 15px;
    text-decoration: italic;
}
</style>
<div id="ContentDiv">
    <?php
    $CurrentUser = $_SESSION["User"];
    $UserID = $CurrentUser->UserID;

    ?>
    <style>
        #textContent {
            width: 450px;
            background: #fff;
            padding: 20px;
        }

    </style>
        <?php
        switch ($PageFunction) {
            case "My Account":
                $cTabTable = new cTabTable(120);
                $cTabTable->AddTab("My Account", "My Account");
                $cTabTable->DisplayTabs("DisplayUserForm");
                break;
                
            default:
                DisplayWelcome();
                break;
        }
        ?>

    



</div>
<!-- /Content area -->


<?php require_once("_includes/ClosePage.php"); ?>

<?php
// ---------------------------------------------------------------------------------
//  DisplayWelcome
// ---------------------------------------------------------------------------------
function DisplayWelcome() {
    global $CurrentUser;
    ?>
    <div style="background-color: #fff;padding:30px;">
        <p><b>Welcome <?php print $CurrentUser->FirstName?></b></p>
        <div class="FloatLeft" style="margin: 0 30px 0 0;"> 
            <p><?php DisplayPersonalStatistics($CurrentUser); ?></p>
            <p><?php DisplayUserStatistics(); ?></p>
            
            <div class="clear"></div>
        </div>
        <div class="FloatLeft">
             <p><?php DisplayImageStatistics(); ?></p>
        </div>
    
        <div class="clear"></div>
    </div>
    <?php
}


// ---------------------------------------------------------------------------------
//  DisplayPersonalStatistics
// ---------------------------------------------------------------------------------
function DisplayPersonalStatistics($CurrentUser) {
    $sqlSelect = "select" .
        " (select count(HistoryID) " .
            " from cms_history hst" .
            " where Action = 'failed login'" .
            " and TableID = " . $CurrentUser->UserID .
            " and CreatedDate > FROM_UNIXTIME($CurrentUser->LoginDate)" .
        ") as FailedAttempts" .
        ", (select count(HistoryID) " .
            " from cms_history hst" .
            " where Action = 'failed login'" .
            " and TableID = " . $CurrentUser->UserID .
        ") as TotalFailed" .
        ",(select count(HistoryID)" .
            " from cms_history hst" .
            " where Action = 'login'" .
            " and UserID = " . $CurrentUser->UserID .
        ") as TotalLogins";
    $tbl = ExecuteQuery($sqlSelect);
    $row = mysql_fetch_object($tbl);
    $TotalLogins = $row->TotalLogins;
    $FailedAttempts = $row->FailedAttempts;
    $TotalFailed = $row->TotalFailed;
    $Attempts = "";
    if ($FailedAttempts > 0 ) {
        $sqlSelect = "select Note,IPAddress from cms_history hst" .
            " where Action = 'failed login'" .
            " and TableID = " . $CurrentUser->UserID .
            " and CreatedDate > FROM_UNIXTIME($CurrentUser->LoginDate)";
        $tbl = ExecuteQuery($sqlSelect);
        while ($row = mysql_fetch_object($tbl)) {
            $Note = $row->Note;
            $IPAddress = $row->IPAddress;
            $Attempts .= "<div class=\"Indent\">$Note [ $IPAddress ]</div>\n";  
        }
    }
    
    print "<div class=\"SummaryBox\">\n" .
            "<div class=\"Title\">Personal Summary</div>\n" .
            "<div class=\"Canister\">\n" .
                "<div>Last Visit: " . FormatDate($CurrentUser->LoginDate) . "</div>\n" .
                "<div>Total Visits: " . number_format($TotalLogins) . "</div>\n" .
                "<div>Failed Logins: " . number_format($TotalFailed) . "</div>\n" .
                "<div>Recent Failed: " . number_format($FailedAttempts) . "</div>\n" .
                $Attempts .
            "</div>\n" .
        "</div>";
}

// ---------------------------------------------------------------------------------
//  DisplayUserStatistics
// ---------------------------------------------------------------------------------
function DisplayUserStatistics() {
    $sqlSelect = "select" .
        " (select count(UserID) from cms_users) as TotalUsers," .
        " (select count(UserID) from cms_users where UNIX_TIMESTAMP(LoginDate) > " . DateAdd("d", -7, GetTime()) . ") as UsersThisWeek";
    $tbl = ExecuteQuery($sqlSelect);
    $row = mysql_fetch_object($tbl);
    $TotalUsers = $row->TotalUsers;
    $UsersThisWeek = $row->UsersThisWeek;
    print "<div class=\"SummaryBox\">\n" .
            "<div class=\"Title\">User Summary</div>\n" .
            "<div class=\"Canister\">\n" .
                "<div>Total Users: " . number_format($TotalUsers) . "</div>\n" .
                "<div>Active Users: " . number_format($UsersThisWeek) . "</div>\n" .
            "</div>\n" .
        "</div>";
}

// ---------------------------------------------------------------------------------
//  DisplayImageStatistics
// ---------------------------------------------------------------------------------
function DisplayImageStatistics() {
    $sqlSelect = "select" .
        " (select count(ImageID) from cms_images) as TotalImages," .
        " (select count(ImageID) from cms_images where Owner = " . $_SESSION["UserID"] . ") as MyImages";
    $tbl = ExecuteQuery($sqlSelect);
    $row = mysql_fetch_object($tbl);
    $TotalImages = $row->TotalImages;
    $MyImages = $row->MyImages;
    print "<div class=\"SummaryBox\">\n" .
            "<div class=\"Title\">Image Summary</div>\n" .
            "<div class=\"Canister\">\n" .
                "<div>Total Images: " . number_format($TotalImages) . "</div>\n" .
                "<div>My Images: " . number_format($MyImages) . "</div>\n" .
            "</div>\n" .
        "</div>";
}



// ---------------------------------------------------------------------------------
//  DisplayMediaStatistics
// ---------------------------------------------------------------------------------
function DisplayMediaStatistics($CurrentUser) {
    $sqlSelect = "select" .
        " (select count(MediaID) " .
            " from cms_media m" .
        ") as NumMedia" .
        ", (select count(MediaID) " .
            " from cms_media m2" .
            " where CreatedDate > '" . FormatDate(GetTime(), "%Y-%m-%d") . "'" .
        ") as NumRecent" .
        ",(select count(MediaImageID)" .
            " from cms_history hst" .
            " where Action = 'login'" .
            " and UserID = " . $CurrentUser->UserID .
        ") as TotalLogins";
    $tbl = ExecuteQuery($sqlSelect);
    $row = mysql_fetch_object($tbl);
    $TotalLogins = $row->TotalLogins;
    $FailedAttempts = $row->FailedAttempts;
    $TotalFailed = $row->TotalFailed;
    $Attempts = "";
    if ($FailedAttempts > 0 ) {
        $sqlSelect = "select Note,IPAddress from cms_history hst" .
            " where Action = 'failed login'" .
            " and TableID = " . $CurrentUser->UserID .
            " and CreatedDate > FROM_UNIXTIME($CurrentUser->LoginDate)";
        $tbl = ExecuteQuery($sqlSelect);
        while ($row = mysql_fetch_object($tbl)) {
            $Note = $row->Note;
            $IPAddress = $row->IPAddress;
            $Attempts .= "<div class=\"Indent\">$Note [ $IPAddress ]</div>\n";  
        }
    }
    
    print "<div class=\"SummaryBox\">\n" .
            "<div class=\"Title\">Personal Summary</div>\n" .
            "<div class=\"Canister\">\n" .
                "<div>Last Visit: " . FormatDate($CurrentUser->LoginDate) . "</div>\n" .
                "<div>Total Visits: " . number_format($TotalLogins) . "</div>\n" .
                "<div>Failed Logins: " . number_format($TotalFailed) . "</div>\n" .
                "<div>Recent Failed: " . number_format($FailedAttempts) . "</div>\n" .
                $Attempts .
            "</div>\n" .
        "</div>";
}

// ---------------------------------------------------------------------------------
//  DisplayUserForm
// ---------------------------------------------------------------------------------
function DisplayUserForm() {
    global $PageFunction;
    $cUsers = new cUsers($_SESSION["UserID"]);
    ?>
    <div id="FormCanisterMaster">
        <form name="frmMain" method="post" action="Home.php" enctype="multipart/form-data" class="MooValidator">
            <input type="hidden" name="FormComplete" value="1">
            <input type="hidden" name="PageFunction" value="<?php print $PageFunction?>">

            <?php if ($UserID > 0 ) { ?>
                <div class="FormTitle">ID</div>
                <div class="FormField"><?php print $UserID ?></div>
                <div class="clear"></div>
            <?php } ?>
            
            <div class="FormTitle">Username</div>
            <div class="FormField"><?php print $cUsers->UserName ?></div>
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

        </script>
    </div>
    <?php
}




?>