<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//  Images
//      version: 1.0
//      Last Update: 10/25/2009
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cImages extends cTableAssistant {

    public $arrPermissions;
    public $TableID;

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Constructor
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function __construct($inImageID = null) {
        $this->SetTableName("cms_images");
        $this->SetPrimaryKey("ImageID");
        
        $this->AddField("Owner", "integer");
        $this->AddField("Customer", "string");
        $this->AddField("CustomerEmail", "string");
        $this->AddField("Image", "file", "_uploads/Images/", array(1280,960));
        $this->AddField("CreatedDate", "date");
        $this->AddField("ModifiedDate", "date");
        
        
        if (!is_null($inImageID)) {
            if ($inImageID > 0) {
                $this->SetTableID($inImageID);
                $this->LoadData();
            }
        }
    }
    
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  ModifyRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function ModifyRecord() {
        if (strtolower(GetFileExtension($this->Image)) != "jpg") {
            return "The file you have uploaded is not a JPG image file. Please check your file and try again.";
        } else {
            $this->AddFieldValue("ModifiedDate", GetTime());
            $result =  $this->DoModify();
            if (!is_null($this->Owner) && $this->Owner > 0) {
                $this->SaveCustomerImage();
            }
            return $result;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  InsertRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function InsertRecord() {
    
        if (strtolower(GetFileExtension($this->Image)) != "jpg") {
            return "The file you have uploaded is not a JPG image file. Please check your file and try again.";
        } else {
    
            $this->AddFieldValue("ModifiedDate", GetTime());
            $this->AddFieldValue("CreatedDate", GetTime());


            $ImageID = $this->DoInsert();

            // Create Customer Image
            if (!is_null($this->Owner) && $this->Owner > 0) {
                $this->SaveCustomerImage();
            }


            // Send Email
            $cUsers = new cUsers($this->Owner);

            $server = "http://" . $_SERVER['SERVER_NAME'];
            $FileName = $server . "/_uploads/CustomerImage/" . str_replace(" ", "", $this->Customer) . $this->GetTableID() . ".jpg";
            $cEmails = new cEmails();
            $cEmails->AddReplacementField("Salesman", $cUsers->FirstName . " " . $cUsers->LastName);
            $cEmails->AddReplacementField("Customer", $this->Customer);
            $cEmails->AddReplacementField("Image", $FileName);
            $cEmails->AddReplacementField("Date", FormatDate(GetTime(), "%m/%d/%Y"));
            $cEmails->SendEmail("Customer Email", $this->CustomerEmail, $this->Customer);

            if (!is_null($cUsers->Manager) && $cUsers->Manager > 0) {
                $cUsers2 = new cUsers($cUsers->Manager);
                $cEmails = new cEmails();
                $cEmails->AddReplacementField("Salesman", $cUsers->FirstName . " " . $cUsers->LastName);
                $cEmails->AddReplacementField("Customer", $this->Customer);
                $cEmails->AddReplacementField("Image", $FileName);
                $cEmails->AddReplacementField("Date", FormatDate(GetTime(), "%m/%d/%Y"));
                $cEmails->SendEmail("Customer Email", $cUsers2->Email, $cUsers2->FirstName . " " . $cUsers2->LastName);
            }
            return $ImageID;
        }
        
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SaveCustomerImage
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SaveCustomerImage() {
        $sqlSelect = "select OverlayImage from cms_users where UserID = $this->Owner";
        $tbl = ExecuteQuery($sqlSelect);
        $row = mysql_fetch_object($tbl);
        
        $OverlayImageTarget = GetWebRoot() . "_uploads/Overlay/OverlayImage$this->Owner." . GetFileExtension($row->OverlayImage);
        $CustomerImageTarget = GetWebRoot() . "_uploads/Images/Image" . $this->GetTableID() .  ".jpg";
        $DestImageTarget = GetWebRoot() . "_uploads/CustomerImage/" . str_replace(" ", "", $this->Customer) . $this->GetTableID() . ".jpg";
        
        $OverlayImage = imagecreatefrompng($OverlayImageTarget);
        $CustomerImage = imagecreatefromjpeg($CustomerImageTarget);
        
        imagecopy($CustomerImage,
                  $OverlayImage,
                  0,
                  0,
                  0,
                  0,
                  1280,
                  960
        );
        
        imagejpeg($CustomerImage,$DestImageTarget,100);
        
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DisplayCustomerImage
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function DisplayCustomerImage() {
        $FileName = "/_uploads/CustomerImage/" . str_replace(" ", "", $this->Customer) . $this->GetTableID() . ".jpg";
        $Display = "<a href=\"$FileName?" . CacheBuster() . "\" rel=\"milkbox[cms1]\" title=\"" . $this->Customer . "\">" . str_replace(" ", "", $this->Customer) . $this->GetTableID() . ".jpg</a>";
        print $Display;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DeleteRecord
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DeleteRecord($arrImageID) {
        foreach($arrImageID as $ImageID) {
            $this->SetTableID($ImageID);
            $this->LoadFieldsFromDB();
            if($this->DoDelete()) {
                AddIncomingMessage("$this->Image deleted.");
            } else {
                AddErrorMessage("Error attempting to delete ImageID: $ImageID.");
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  LoadData
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function LoadData() {
        $this->LoadFieldsFromDB();

    }



}
?>