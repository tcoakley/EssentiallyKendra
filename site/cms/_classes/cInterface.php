<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Interface class Template
//		version: 1.01
//		Last Update: 6/17/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cInterface extends cTableAssistant {


	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inID = null) {
		$this->SetTableName("");
		$this->SetPrimaryKey("");
		
		$this->AddField("CreatedDate", "date");
		$this->AddField("ModifiedDate", "date");
		
		
		if (!is_null($inID)) {
			if ($inID > 0) {
				$this->SetTableID($inID);
				$this->LoadData();
			}
		}
		
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	ModifyRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function ModifyRecord() {
		$this->AddFieldValue("ModifiedDate", GetTime());
		$rv = $this->DoModify();
		$this->SaveFile();
		return $rv;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	InsertRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function InsertRecord() {
		$this->AddFieldValue("ModifiedDate", GetTime());
		$this->AddFieldValue("CreatedDate", GetTime());
		$rv = $this->DoInsert();
		$this->SaveFile();
		return $rv;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DeleteRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DeleteRecord($arrMediaID) {
		foreach($arrMediaID as $MediaID) {

			$this->SetTableID($MediaID);
			$this->LoadFieldsFromDB();
			
			if($this->DoDelete()) {
				AddIncomingMessage("Media item " . $this->enTitle . " was deleted.");
			} else {
				AddErrorMessage("Error attempting to delete MediaID: $this->MediaID.");
			}
		}
		$this->SaveFile();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	LoadData
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function LoadData() {
		$this->LoadFieldsFromDB();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SaveFile
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SaveFile() {
		//$SaveFile = GetWebRoot() . "_uploads/";
	}

}
?>
