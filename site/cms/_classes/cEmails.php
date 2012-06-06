<?php
if(strpos($_SERVER['SCRIPT_NAME'], "cms/") > 0) {
	include_once('_phpMailer/class.phpmailer.php');
} else {
	include_once('cms/_phpMailer/class.phpmailer.php');
}

// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Emails
//		version: 1.1
//		Last Update: 6/27/09
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cEmails extends cTableAssistant {


	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inEmailID = null) {
		$this->SetTableName("cms_emails");
		$this->SetPrimaryKey("EmailID");
		
		$this->AddField("EmailName", "string");
		$this->AddField("EmailDescription", "string");
		$this->AddField("EmailSubject", "string");
		$this->AddField("EmailBodyText", "string");
		$this->AddField("EmailBodyHtml", "string");
		$this->AddField("EmailCategory", "string");
		$this->AddField("ModifiedDate", "date");
		
		if (!is_null($inEmailID)) {
			if ($inEmailID > 0) {
				$this->SetTableID($inEmailID);
				$this->LoadFieldsFromDB();
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
		$this->AddFieldValue("ModifiedDate", GetTime());
		return $this->DoInsert();
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DeleteRecord
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DeleteRecord($arrEmailID) {
		foreach($arrEmailID as $EmailID) {
			$this->SetTableID($EmailID);
			$this->LoadFieldsFromDB();
			if($this->DoDelete()) {
				AddIncomingMessage("$this->EmailName deleted.");
				$sqlSelect = "delete from cms_emailfields where EmailID = '" . $this->EmailID . "'";
				ExecuteQuery($sqlSelect);
			} else {
				AddErrorMessage("Error attempting to delete EmailID: $this->EmailID.");
			}
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddReplacementField
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddReplacementField($FieldName, $FieldValue) {
		$this->$FieldName = $FieldValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SendEmail
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SendEmail($Email, $ToEmail, $ToName) {
		// Get Admin Credentials
		$cMasterSettings = new cMasterSettings(1);
		$cMasterSettings->LoadCredentials($AdminFirstName, $AdminLastName, $AdminEmail);
		// Get Email Fields
		$sqlSelect = "select * from " . $this->GetTableName() . " where EmailName = '" . mysql_escape_string($Email) . "'";
		$tbl = ExecuteQuery($sqlSelect);
		$row = mysql_fetch_object($tbl);
		$EmailID = $row->EmailID;
		$EmailSubject = $row->EmailSubject;
		$EmailBodyText = $row->EmailBodyText;
		$EmailBodyHtml	= $row->EmailBodyHtml;
		
		// Get Replacement Fields
		$sqlSelect = "select * from cms_emailfields where EmailID = $EmailID";
		$tbl = ExecuteQuery($sqlSelect);
		while ($row = mysql_fetch_object($tbl)) {
			$FieldName = $row->FieldName;
			$ReplaceValue = $row->ReplaceValue;
			$EmailBodyText = str_replace($ReplaceValue, $this->$FieldName, $EmailBodyText);
			$EmailBodyHtml = str_replace($ReplaceValue, $this->$FieldName, $EmailBodyHtml);
			$EmailSubject = str_replace($ReplaceValue, $this->$FieldName, $EmailSubject);
		}
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->Host = $cMasterSettings->EmailServer;
		if ($cMasterSettings->EmailAuth) {
			$mail->SMTPAuth = true;
			$mail->Username = $cMasterSettings->EmailUser;
			$mail->Password = $cMasterSettings->EmailPass;
		}
		$mail->From = $AdminEmail;
		$mail->FromName = $AdminFirstName . " " . $AdminLastName;
		$mail->Subject = $EmailSubject;
		$mail->AltBody = $EmailBodyText;
		$mail->MsgHTML($EmailBodyHtml);
		$mail->AddAddress($ToEmail, $ToName);
		if(!$mail->Send()) {
		  debug("MailError",$mail->ErrorInfo,true);
		}

	}

}
?>