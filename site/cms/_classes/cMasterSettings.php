<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	MasterSettings
//		version: 1.0
//		Last Update: 6/23/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cMasterSettings extends cTableAssistant {


	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inMasterSettingID = null) {
		$this->SetTableName("cms_mastersettings");
		$this->SetPrimaryKey("MasterSettingID");
		
		$this->AddField("AdministratorID", "integer");
		$this->AddField("EmailServer", "string");
		$this->AddField("EmailAuth", "bool");
		$this->AddField("EmailUser", "string");
		$this->AddField("EmailPass", "string");
		$this->AddField("ModifiedDate", "date");
		
		if (!is_null($inMasterSettingID)) {
			if ($inMasterSettingID > 0) {
				$this->SetTableID($inMasterSettingID);
				$this->LoadData();
			}
		}
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ModifyRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function ModifyRecord() {
		$this->AddFieldValue("ModifiedDate", GetTime());
		return $this->DoModify();
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	LoadData
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function LoadData() {
		$this->LoadFieldsFromDB();
	}

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	function
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function LoadCredentials(&$FirstName, &$LastName, &$Email) {
		$sqlSelect = "select FirstName, LastName, Email from cms_users" .
			" where UserID = " . $this->AdministratorID;
		$tbl = ExecuteQuery($sqlSelect);
		if ($tbl) {
			$row = mysql_fetch_object($tbl);
			$FirstName = $row->FirstName;
			$LastName = $row->LastName;
			$Email = $row->Email;
		} else {
			$FirstName = "Tom";
			$LastName = "Coakley";
			$Email = "tom@wa007.com";
		}
	}
	


}
?>