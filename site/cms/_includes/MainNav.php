<!-- Main Nav -->

<div id="MainMenu">
    <?php
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //  Configure and display the Main Nav
        //      AddNav(TabName, TabAddress, Permission, Parent);
        //          TabName is what displays in the nav 
        //              if TabName has submenu it must be a unique name
        //          TabAddress is the URL that is called on click
        //          Permission is the required Permission for it to display
        //          Parent is the unique TabName of the parent menu
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $cMainNav = new cMainNav();
        $cMainNav->AddNav("Home", "Home.php", "Welcome");
            $cMainNav->AddNav("My Account", "Home.php?PageFunction=My+Account", "Welcome", "Home");
        $cMainNav->AddNav("Users", "Users.php", "Users");
            $cMainNav->AddNav("Add User", "Users.php?PageFunction=Add+User", "Users", "Users");
            $cMainNav->AddNav("User List", "Users.php?PageFunction=User+List", "Users", "Users");
            $cMainNav->AddNav("Add Group", "Groups.php?PageFunction=Add+Group", "Groups", "Users");
            $cMainNav->AddNav("Group List", "Groups.php?PageFunction=Group+List", "Groups", "Users");
        $cMainNav->AddNav("Images", "Images.php?PageFunction=Image+List", "Images");
            $cMainNav->AddNav("Add Image", "Images.php?PageFunction=Add+Image", "Images", "Images");
            $cMainNav->AddNav("Image List", "Images.php?PageFunction=Image+List", "Images", "Images");
        $cMainNav->AddNav("Master Settings", "MasterSettings.php", "Master Settings");
            $cMainNav->AddNav("Add Permission", "MasterSettings.php?PageFunction=Add+Permission", "IsTom", "Master Settings");
            $cMainNav->AddNav("Permission List", "MasterSettings.php?PageFunction=Permission+List", "IsTom", "Master Settings");
            $cMainNav->AddNav("Add Email", "MasterSettings.php?PageFunction=Add+Email", "IsTom", "Master Settings");
            $cMainNav->AddNav("Email List", "MasterSettings.php?PageFunction=Email+List", "IsTom", "Master Settings");
            DisplayEmailNav($cMainNav);
        $cMainNav->AddNav("Logout", "Logout.php");
        
        $cMainNav->DisplayNav();
        
        
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //  DisplayEmailNav
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        function DisplayEmailNav(&$cMainNav) {
            $sqlSelect = "select * from cms_emails order by EmailCategory desc";
            $tbl = ExecuteQuery($sqlSelect);
            $OldCategory = "";
            $cMainNav->AddNav("Emails", "Emails.php", "Emails", "Master Settings");
            while ($row = mysql_fetch_object($tbl)) {
                $EmailID = $row->EmailID;
                $EmailCategory = $row->EmailCategory;
                $EmailName = $row->EmailName;
                if ($EmailCategory != "") {
                    if ($EmailCategory != $OldCategory) {
                        $OldCategory = $EmailCategory;
                        $cMainNav->AddNav($EmailCategory, "Emails.php?PageFunction=Modify+Email&EmailID=$EmailID", "Emails", "Emails");
                    }
                    $cMainNav->AddNav($EmailName, "Emails.php?PageFunction=Modify+Email&EmailID=$EmailID", "Emails", $EmailCategory);
                } else {
                    $cMainNav->AddNav($EmailName, "Emails.php?PageFunction=Modify+Email&EmailID=$EmailID", "Emails", "Emails");
                }               
            }
        }
        
        
    ?>
    <div class="clear"></div>
</div>

<!-- /Main Nav -->
