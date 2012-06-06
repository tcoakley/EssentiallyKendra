<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Groups
//		version: 1.0
//		Last Update: 6/23/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cGroups extends cTableAssistant {

	public $arrPermissions;

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inGroupID = null) {
		$this->SetTableName("cms_groups");
		$this->SetPrimaryKey("GroupID");
		
		$this->AddField("GroupName", "string");
		$this->AddField("GroupDescription", "string");
		$this->AddField("CreatedDate", "date");
		$this->AddField("ModifiedDate", "date");
		
		if (!is_null($inGroupID)) {
			if ($inGroupID > 0) {
				$this->SetTableID($inGroupID);
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
	//	InsertRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function InsertRecord() {
		$this->AddFieldValue("CreatedDate", GetTime());
		$this->AddFieldValue("ModifiedDate", GetTime());
		return $this->DoInsert();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	LoadData
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function LoadData() {
		$this->LoadFieldsFromDB();
		$sqlSelect = "Select Permission from cms_grouppermissions where GroupID = " . $this->GetTableID();
		$tbl = ExecuteQuery($sqlSelect);
		$arrPermissions = array();
		while ($row = mysql_fetch_object($tbl)) {
			$Permission = $row->Permission;
			array_push($arrPermissions, $Permission);
		}
		$this->arrPermissions = $arrPermissions;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DeleteRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DeleteRecord($arrGroupID) {
		foreach($arrGroupID as $GroupID) {
			$this->SetTableID($GroupID);
			$this->LoadFieldsFromDB();
			if($this->DoDelete()) {
				AddIncomingMessage("$this->GroupName deleted.");
				$sqlSelect = "delete from cms_usergroups where GroupID = $GroupID";
				ExecuteQuery($sqlSelect);
				$sqlSelect = "delete from cms_grouppermissions where GroupID = $GroupID";
				ExecuteQuery($sqlSelect);
			} else {
				AddErrorMessage("Error attempting to delete GroupID: $GroupID.");
			}
		}
	}

}
?>