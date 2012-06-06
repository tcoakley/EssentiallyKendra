<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	EmailFields
//		version: 1.0
//		Last Update: 7/18/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cEmailFields extends cTableAssistant {

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inEmailFieldID = null) {
		$this->SetTableName("cms_emailfields");
		$this->SetPrimaryKey("EmailFieldID");
		
		$this->AddField("EmailID", "integer");
		$this->AddField("FieldName", "string");
		$this->AddField("ReplaceValue", "string");
		
		if (!is_null($inEmailFieldID)) {
			if ($inEmailFieldID > 0) {
				$this->SetTableID($inEmailFieldID);
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
	function DeleteRecord($arrEmailFieldID) {
		foreach($arrEmailFieldID as $EmailFieldID) {
			$this->SetTableID($EmailFieldID);
			$this->LoadFieldsFromDB();
			$EmailID = $this->EmailID;
			if($this->DoDelete()) {
				AddIncomingMessage("$this->FieldName deleted.");
			} else {
				AddErrorMessage("Error attempting to delete EmailFieldID: $this->EmailFieldID.");
			}
		}
		return $EmailID;
		
	}

}
?>