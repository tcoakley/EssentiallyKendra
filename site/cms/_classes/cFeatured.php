<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Featured
//		version: 1.0
//		Last Update: 2/26/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cFeatured extends cTableAssistant {


	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inFeaturedID = null) {
		$this->SetTableName("cms_featured");
		$this->SetPrimaryKey("FeaturedID");
		
		$this->AddField("DisplayOrder", "integer");
		$this->AddField("Title", "string");
		$this->AddField("Image", "file", "_uploads/featured/", array(641,360));
		$this->AddField("Video", "string");
		$this->AddField("Link", "string");
		$this->AddField("AutoStart", "bool");
		$this->AddField("DelayTime", "integer");
		$this->AddField("Description", "string");
		$this->AddField("CreatedDate", "date");
		$this->AddField("ModifiedDate", "date");
		
		
		if (!is_null($inFeaturedID)) {
			if ($inFeaturedID > 0) {
				$this->SetTableID($inFeaturedID);
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
		$sqlSelect = "select DisplayOrder from cms_featured order by DisplayOrder desc limit 1";
		$tbl = ExecuteQuery($sqlSelect);
		if ($tbl) {
			if ($row = mysql_fetch_object($tbl)) {
				$DisplayOrder = $row->DisplayOrder + 10;
			}
		}
		DefaultNumber($DisplayOrder, 10);
		$this->AddFieldValue("DisplayOrder", $DisplayOrder);
		$this->AddFieldValue("ModifiedDate", GetTime());
		$this->AddFieldValue("CreatedDate", GetTime());
		$rv = $this->DoInsert();
		$this->SaveFile();
		return $rv;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DeleteRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DeleteRecord($arrFeaturedID) {
		foreach($arrFeaturedID as $FeaturedID) {

			$this->SetTableID($FeaturedID);
			$this->LoadFieldsFromDB();
			
			if($this->DoDelete()) {
				AddIncomingMessage("Featured item " . $this->Title . " was deleted.");
			} else {
				AddErrorMessage("Error attempting to delete FeaturedID: $this->FeaturedID.");
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
		$FeaturedSaveFile = GetWebRoot() . "_uploads/Featured.xml";

		$FBody = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
			"<featured>\n";
		$sqlSelect = "select FeaturedID, Title, Image, Video, Link from cms_featured order by DisplayOrder desc";
		$tbl = ExecuteQuery($sqlSelect);


		while ($row = mysql_fetch_object($tbl)) {
			$this->SetTableID($row->FeaturedID);
			$this->LoadData();
			$FBody .= "	<item Title=\"" . XMLPrepare($this->Title) . "\"" .
				" Image=\"" . $this->GetFileName("Image", false) . "\"" .
				" Video=\"_flv/" . $this->Video . "\"" .
				" Link=\"" . XMLPrepare($this->Link) . "\"" .
				" AutoStart=\"" . $this->AutoStart . "\"" .
				" DelayTime=\"" . ($this->DelayTime * 31) . "\">" .
				XMLPrepare($this->Description) .
				"</item>\n";
		}
		$FBody .= "</featured>\n";

		$FileHandle = fopen($FeaturedSaveFile, "w");
		fwrite($FileHandle, $FBody);
		fclose($FileHandle);
		

	}


}
?>
