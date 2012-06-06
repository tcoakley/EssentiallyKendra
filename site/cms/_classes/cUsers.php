<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//  Users
//      version: 1.0
//      Last Update: 6/23/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cUsers extends cTableAssistant {

    public $arrPermissions;
    public $TableID;

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Constructor
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function __construct($inUserID = null) {
        $this->SetTableName("cms_users");
        $this->SetPrimaryKey("UserID");
        
        $this->AddField("UserName", "string");
        $this->AddField("FirstName", "string");
        $this->AddField("LastName", "string");
        $this->AddField("Password", "string");
        $this->AddField("Email", "string");
        $this->AddField("Manager", "integer");
        $this->AddField("IsManager", "bool");
        $this->AddField("OverlayImage", "file", "_uploads/Overlay/");
        $this->AddField("CreatedDate", "date");
        $this->AddField("ModifiedDate", "date");
        $this->AddField("LoginDate", "date");
        $this->AddField("PasswordDate", "date");
        
        if (!is_null($inUserID)) {
            if ($inUserID > 0) {
                $this->SetTableID($inUserID);
                $this->LoadData();
            }
        }
    }
    
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  ModifyRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function ModifyRecord() {
        $this->AddFieldValue("ModifiedDate", GetTime());
        return $this->DoModify();
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  InsertRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function InsertRecord() {
        $this->AddFieldValue("ModifiedDate", GetTime());
        $this->AddFieldValue("CreatedDate", GetTime());
        $this->AddFieldValue("LoginDate", GetTime());
        $this->AddFieldValue("PasswordDate", GetTime());
        
        $UserID = $this->DoInsert();
        
        // Send Email
        $cEmails = new cEmails();
        $cEmails->AddReplacementField("UserName", $this->UserName);
        $cEmails->AddReplacementField("FirstName", $this->FirstName);
        $cEmails->AddReplacementField("LastName", $this->LastName);
        $cEmails->AddReplacementField("Email", $this->Email);
        $cEmails->AddReplacementField("Password", $this->Password);
        $cEmails->AddReplacementField("CreatedDate", FormatDate($this->CreatedDate, "%m/%d/%Y"));
        $cEmails->SendEmail("New Account", $this->Email, $this->FirstName . " " . $this->LastName);
        
        return $UserID;
        
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DeleteRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DeleteRecord($arrUserID) {
        foreach($arrUserID as $UserID) {
            $this->SetTableID($UserID);
            $this->LoadFieldsFromDB();
            if($this->DoDelete()) {
                AddIncomingMessage("$this->UserName deleted.");
                $sqlSelect = "delete from cms_usergroups where UserID = $UserID";
                ExecuteQuery($sqlSelect);
                $sqlSelect = "delete from cms_userpermissions where UserID = $UserID";
                ExecuteQuery($sqlSelect);
            } else {
                AddErrorMessage("Error attempting to delete UserID: $UserID.");
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  LoadData
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function LoadData() {
        $this->LoadFieldsFromDB();
        // Load Permissions
        $sqlSelect = "Select Permission from cms_userpermissions where UserID = " . $this->GetTableID();
        $tbl = ExecuteQuery($sqlSelect);
        $arrPermissions = array();
        while ($row = mysql_fetch_object($tbl)) {
            $arrTemp = array($row->Permission => true);
            $arrPermissions = array_merge($arrPermissions, $arrTemp);
        }
        $this->arrPermissions = $arrPermissions;
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Login
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function login($UserName, $Password) {
        $sqlSelect = "select UserID, UserName, Password, FirstName, LastName, Email, IsManager, " .
            " UNIX_TIMESTAMP(CreatedDate) as CreatedDate," .
            " UNIX_TIMESTAMP(ModifiedDate) as ModifiedDate," .
            " UNIX_TIMESTAMP(LoginDate) as LoginDate," .
            " UNIX_TIMESTAMP(PasswordDate) as PasswordDate" .
            " from " . $this->GetTableName() . 
            " where UserName = '" . mysql_escape_string($UserName) . "'";
        //debug("sqlSelect", $sqlSelect);
        $tbl = ExecuteQuery($sqlSelect);
        $row = mysql_fetch_object($tbl);
        if (!$row) {
            AddErrorMessage("Incorrect Username or Password.");
            return false;
        } else {
            if ($Password != $row->Password) {
                AddErrorMessage("Incorrect Password.");
                $UserID = $row->UserID;
                // Save failure in history
                $this->SetTableID($row->TableID);
                $this->SaveHistory("failed login", $UserID, $Password, $UserID);
                // Check for multiple failures and reset password
                $sqlSelect = "select count(HistoryID) as FailedAttempts" .
                    " from cms_history hst" .
                    " where Action = 'failed login'" .
                    " and TableID = $UserID" .
                    " and CreatedDate >  FROM_UNIXTIME(" . $row->LoginDate . ")";
                $tbl = ExecuteQuery($sqlSelect);
                $row = mysql_fetch_object($tbl);
                if ($row->FailedAttempts > 3) {
                    $NewPassword = RandomCode(5,8);
                    $sqlSelect = "update cms_users set Password = '$NewPassword' where UserID = $UserID";
                    ExecuteQuery($sqlSelect);
                    AddErrorMessage("More than three failed attempts to this account. Password has been reset. Use the 'Lost Password' link to retreive the new password");
                    return false;
                }
            } else {
                // Successful Login
                $_SESSION["UserID"] = $row->UserID;
                $_SESSION["User"] = $row;
                
                // Record the login
                $this->SetTableID($row->UserID);
                $this->SaveHistory("login");
                $this->AddFieldValue("LoginDate", GetTime());
                $this->ModifyRecord();
                
                // Load Permissions
                $sqlSelect = "Select Permission from cms_userpermissions where UserID = " . $row->UserID .
                    " union" .
                    " Select Permission from cms_grouppermissions gp" .
                    " left join cms_usergroups ug on gp.GroupID = ug.GroupID" .
                    " where UserID = " . $row->UserID;
                $tbl = ExecuteQuery($sqlSelect);
                $arrPermissions = array();
                while ($row = mysql_fetch_object($tbl)) {
                    $arrTemp = array($row->Permission => true);
                    $arrPermissions = array_merge($arrPermissions, $arrTemp);
                }
                $_SESSION["arrPermissions"] = $arrPermissions;
                return true;
            }
        }
    }

}
?>