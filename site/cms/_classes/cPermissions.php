<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Permissions
//		version: 1.0
//		Last Update: 6/23/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cPermissions extends cTableAssistant {

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inPermissionID = null) {
		$this->SetTableName("cms_permissions");
		$this->SetPrimaryKey("PermissionID");
		
		$this->AddField("Permission", "string");
		$this->AddField("Description", "string");
		
		if (!is_null($inPermissionID)) {
			if ($inPermissionID > 0) {
				$this->SetTableID($inPermissionID);
				$this->LoadFieldsFromDB();
			}
		}
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ModifyRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function ModifyRecord() {
		return $this->DoModify();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	InsertRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function InsertRecord() {
		return $this->DoInsert();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DeleteRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DeleteRecord($arrPermissionID) {
		foreach($arrPermissionID as $PermissionID) {
			$this->SetTableID($PermissionID);
			$this->LoadFieldsFromDB();
			if($this->DoDelete()) {
				AddIncomingMessage("$this->Permission deleted.");
				$sqlSelect = "delete from cms_userpermissions where Permission = '" . mysql_escape_string($this->Permission) . "'";
				ExecuteQuery($sqlSelect);
				$sqlSelect = "delete from cms_grouppermissions where Permission = '" . mysql_escape_string($this->Permission) . "'";
				ExecuteQuery($sqlSelect);
			} else {
				AddErrorMessage("Error attempting to delete PermissionID: $PermissionID.");
			}
		}
	}

}
?>