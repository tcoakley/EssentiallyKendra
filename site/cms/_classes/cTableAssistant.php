<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//  TableAssistant
//      Version 1.4.1
//      Last Update: 6/16/2009
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cTableAssistant {

    private $TableName = null;
    private $PrimaryKey = null;
    private $TableID = null;
    private $arrFields = array();
    private $arrFieldTypes = array();
    private $arrFieldsSet = array();
    private $arrFileSavePath = array();
    public $arrImageDimensions = array();
    private $arrTempPaths = array();
    private $arrDBFieldTypes = array();
    private $arrFieldLengths = array();
    private $arrCurrentValues = array();
    private $arrDetail = array();
    private $arrAssociationTables = array();
    private $arrAssociationValues = array();
    private $arrAssociationFields = array();
    private $arrCopyFile = array();
    private $tbl = null;
    private $HasFiles = false;
    private $AllowNulls = false;
    private $AutoCrop = false;
    private $MasterImage = array();
    private $MasterImageName = null; // Default Image to use if other images are not passed
    private $MasterImagePath = null; // Default Image to use if other images are not passed
    
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Constructor
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function __construct() {
        
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  AddField
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddField($FieldName, $FieldType = "string", $FileSavePath = "", $ImageDimensions = null, $MasterImage = false) {
        if (!in_array($FieldName, $this->arrFields)) {
            array_push($this->arrFields, $FieldName);
            array_push($this->arrFieldTypes, $FieldType);
            array_push($this->arrFileSavePath, $FileSavePath);
            array_push($this->arrImageDimensions, $ImageDimensions);
            if ($MasterImage) {
                $this->MasterImage = $FieldName;
                array_push($this->arrCopyFile, false);
            } else {
                array_push($this->arrCopyFile, true);
            }
            if ($FieldType == "file") {
                $this->HasFiles = true;
            }
            $this->$FieldName = null;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  AddFieldValue
    //      Adds a field that will be inserted/modified
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddFieldValue($FieldName, $FieldValue, $TempPath = null) {
        if (!is_null($FieldValue) || $this->AllowNulls) {
            if (!in_array($FieldName, $this->arrFields)) {
                exit("\n<br>Attempt to add value for $FieldName, but $FieldName is not set.\n");
            }
            $this->$FieldName = $FieldValue;
            if(!is_null($TempPath)) {
                $this->arrTempPaths[$FieldName] = $TempPath;
            }
            if (!in_array($FieldName, $this->arrFieldsSet)) {
                array_push($this->arrFieldsSet, $FieldName);
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Add Association
    //      Allows for an association table
    //      Accepts the table name and an array of values
    //      Assumes Primary Key is used for association
    //      This is for a single value assocation
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddAssociation($TableName, $FieldName, $arrValues) {
        array_push($this->arrAssociationTables, $TableName);
        array_push($this->arrAssociationFields, $FieldName);
        array_push($this->arrAssociationValues, $arrValues);
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  LoadFieldsFromDB
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function LoadFieldsFromDB() {
        if (is_null($this->TableID)) {
            debug("Died in LoadFields from db because TableID is not set.",$this->TableName, 1);
        }
        $sqlSelect = "select ";
        for ($looper = 0; $looper < count($this->arrFields); $looper++) {
            $FieldName = $this->arrFields[$looper];
            $FieldType = $this->arrFieldTypes[$looper];
            if ($looper > 0) {
                $sqlSelect .= ", ";
            }
            switch ($FieldType) {
                case "date":
                    $sqlSelect .= "UNIX_TIMESTAMP($FieldName) as $FieldName";
                    break;
                    
                default:
                    $sqlSelect .= $FieldName;
                    break;
            }
        }
        $sqlSelect .= " from $this->TableName" .
            " where $this->PrimaryKey = $this->TableID";
        $tbl = ExecuteQuery($sqlSelect);
        $row = mysql_fetch_object($tbl);
        if ($tbl) {
            for ($looper = 0; $looper < mysql_num_fields($tbl); $looper++) {
                $FieldName = mysql_field_name($tbl, $looper);
                $this->$FieldName = stripslashes($row->$FieldName);
            }
        }
        mysql_free_result($tbl);
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  LoadFieldsFromForm
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function LoadFieldsFromForm() {
        for ($looper = 0; $looper < count($this->arrFields); $looper++) {
            $FieldName = $this->arrFields[$looper];
            $FieldType = $this->arrFieldTypes[$looper];
            switch ($FieldType) {
                case "file":
                    if (array_key_exists($FieldName, $_FILES)) {
                        $this->AddFieldValue($FieldName,$_FILES[$FieldName]['name'],$_FILES[$FieldName]['tmp_name']);
                    } else {
                        $this->AddFieldValue($FieldName,"","");
                    }
                    break;
                    
                case "string":
                    $this->AddFieldValue($FieldName,RequestString($FieldName));
                    break;
                    
                case "integer":
                    $this->AddFieldValue($FieldName, RequestInt($FieldName));
                    break;
                    
                case "float":
                    $this->AddFieldValue($FieldName, RequestFloat($FieldName));
                    break;
                    
                case "bool":
                    $this->AddFieldValue($FieldName, RequestBool($FieldName));
                    break;
            
                case "date":
                    $this->AddFieldValue($FieldName, RequestDate($FieldName));
                    break;
                    

            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DumpFields
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DumpFields() {
        print ("<br>\n<br>\n<b> == [ Dump of Fields ] ==</b>");
        for ($looper = 0; $looper < count($this->arrFields); $looper++) {
            $FieldName = $this->arrFields[$looper];
            $FieldType = $this->arrFieldTypes[$looper];
            switch ($FieldType) {
                case "date":
                    debug($FieldName, FormatDate($this->$FieldName));
                    break;
                    
                default:
                    debug($FieldName, $this->$FieldName);
                    break;
            }
            
        }
        print ("<br>\n<b> == [ /Dump of Fields ] ==</b><br>\n<br>");
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DoInsert
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DoInsert() {
        // Verify that we are ready for insert
        if (is_null($this->TableName)) {
            debug("The TableName has not been set yet.","DoInsert",true);
        }
        if (is_null($this->PrimaryKey)) {
            debug("The PrimaryKey has not been set yet.","DoInsert",true);
        }
        // First Load information about the fields
        $this->LoadFieldInformation();
        
        //Prepare Insert
        $sqlSelect = "insert into $this->TableName (" .
            implode(", ", $this->arrFieldsSet) . ")" .
            " values (";
        $sqlSelect = str_replace($this->PrimaryKey . ",", "", $sqlSelect);

        
        // Begin Values Loop
        for ($looper = 0; $looper < count($this->arrFieldsSet); $looper ++) {
            $FieldName = $this->arrFieldsSet[$looper];
            $FieldValue = $this->$FieldName;
            $key = array_search($FieldName, $this->arrFields);
            $FieldType = $this->arrDBFieldTypes[$looper];
            $FieldLength = $this->arrFieldLengths[$looper];
            $FileSavePath = $this->arrFileSavePath[$looper];
            $ImageDimensions = $this->arrImageDimensions[$key];
            if ($this->MasterImage == $FieldName) {
                $this->MasterImageName = $FieldValue;
            }
            if (array_key_exists($FieldName, $this->arrTempPaths)) {
                $TempPath = $this->arrTempPaths[$FieldName];
            } else {
                $TempPath = null;
            }
            if ($FieldName != $this->PrimaryKey) {
                if ($looper > 0) {
                    $sqlSelect .= ", ";
                }
                
            
                switch ($FieldType) {
                    case "string":
                        if (strLen($FieldValue) > $FieldLength) {
                            AddErrorMessage("$FieldName was " . strlen($FieldValue) .
                                " characters in length and the database could only accept $FieldLength." .
                                " Your entry was shortened to the maximum length allowed.");
                            $FieldValue = substr($FieldValue, 0, $FieldLength);
                        }
                        $sqlSelect .= "'" . mysql_escape_string($FieldValue) . "'";
                        $this->AddDetail($FieldName, $FieldValue);
                        break;
                    
                    case "file":
                        if(strlen($TempPath) > 0 || !is_null($this->MasterImageName)) {
                            if (strlen($TempPath) > 0 ) {

                            } elseif (!is_null($ImageDimensions)) {
                                $FieldValue = $this->MasterImageName;
                            } else {
                                $FieldValue = "";
                            }                       
                            if (strLen($FieldValue) > $FieldLength) {
                                AddErrorMessage("$FieldName was " . strlen($FieldValue) .
                                    " characters in length and the database could only accept $FieldLength." .
                                    " Your entry was shortened to the maximum length allowed.");
                                $FieldValue = substr($FieldValue, 0, $FieldLength);
                            }
                            $sqlSelect .= "'" . mysql_escape_string($FieldValue) . "'";
                            $this->AddDetail($FieldName, $FieldValue);
                        } else {
                            $sqlSelect .= "''";
                        }
                        break;

                    case "blob":
                        $sqlSelect .= "'" . mysql_escape_string($FieldValue) . "'";
                        $this->AddDetail($FieldName, $FieldValue);
                        break;

                    case "datetime":
                        $sqlSelect .= "FROM_UNIXTIME($FieldValue)";
                        $this->AddDetail($FieldName, FormatDate($FieldValue, "%Y-%m-%d %H:%M:%S"));
                        break;

                    default:
                        $sqlSelect .= "'$FieldValue'";
                        $this->AddDetail($FieldName, $FieldValue);
                        break;                  
                }
            }
        }
        // End values loop
        $sqlSelect .= ")";
        //debug("sqlSelect", $sqlSelect,true);
        $result = ExecuteQuery($sqlSelect);
        
        if ($result) {
            $this->TableID = mysql_insert_id();
            //Handle AssociationTables
            $this->SaveAssociations();
            //Save the history
            $this->SaveHistory("insert");
            // Save any Files
            $this->SaveFiles();
            return $this->TableID;
        } else {
            return false;
        }
    }
    

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DoModify
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DoModify() {
        $OneWritten = false;
        // Verify that we are ready for insert
        if (is_null($this->TableName)) {
            debug("The TableName has not been set yet.","DoModify",true);
        }
        if (is_null($this->PrimaryKey)) {
            debug("The PrimaryKey has not been set yet.","DoModify",true);
        }
        if (is_null($this->TableID)) {
            debug("The TableID has not been set yet.","DoModify",true);
        }
        // First Load information about the fields
        $this->LoadFieldInformation();
        
        //Prepare Query
        $sqlSelect = "update $this->TableName set ";
        // Begin query build loop
        for ($looper = 0; $looper < count($this->arrFieldsSet); $looper ++) {
            $FieldName = $this->arrFieldsSet[$looper];
            $key = array_search($FieldName, $this->arrFields);
            $FieldValue = $this->$FieldName;
            $FieldType = $this->arrDBFieldTypes[$looper];
            $DeclaredFieldType = $this->arrFieldTypes[$key];
            $FieldLength = $this->arrFieldLengths[$looper];
            $CurrentValue = $this->arrCurrentValues[$looper];
            $ImageDimensions = $this->arrImageDimensions[$looper];
            
            
            if (strlen($CurrentValue) > 0) {
                $this->arrCopyFile[$looper] = false;
            }
            if ($this->MasterImage == $FieldName) {
                $this->MasterImageName = $FieldValue;
            }

            if ($FieldName != $this->PrimaryKey) {
                switch ($FieldType) {
                    case "string":
                        if ($FieldValue != $CurrentValue) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            if (strLen($FieldValue) > $FieldLength) {
                                AddErrorMessage("$FieldName was " . strlen($FieldValue) .
                                    " characters in length and the database could only accept $FieldLength." .
                                    " Your entry was shortened to the maximum length allowed.");
                                $FieldValue = substr($FieldValue, 0, $FieldLength);
                            }
                            $this->AddDetail($FieldName, $FieldValue, $CurrentValue);
                            $sqlSelect .= "$FieldName = '" . mysql_escape_string($FieldValue) . "'";
                            $OneWritten = true;
                        }
                        break;
                        
                    case "file":
                        if ($FieldValue != $CurrentValue && strlen($FieldValue) > 0) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            if (strLen($FieldValue) > $FieldLength) {
                                AddErrorMessage("$FieldName was " . strlen($FieldValue) .
                                    " characters in length and the database could only accept $FieldLength." .
                                    " Your entry was shortened to the maximum length allowed.");
                                $FieldValue = substr($FieldValue, 0, $FieldLength);
                            }
                            $this->AddDetail($FieldName, $FieldValue, $CurrentValue);
                            $sqlSelect .= "$FieldName = '" . mysql_escape_string($FieldValue) . "'";
                            $OneWritten = true;
                        }
                        break;

                    case "blob":
                        if ($FieldValue != $CurrentValue) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            $this->AddDetail($FieldName, $FieldValue, $CurrentValue);
                            $sqlSelect .= "$FieldName = '" . mysql_escape_string($FieldValue) . "'";
                            $OneWritten = true;
                        }
                        break;

                    case "datetime":
                        if($FieldValue != strtotime($CurrentValue)) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            $this->AddDetail($FieldName, FormatDate($FieldValue, "%Y-%m-%d %H:%M:%S"), $CurrentValue);
                            if($FieldValue == null) {
                                $sqlSelect .= "$FieldName = null";
                            } else {
                                $sqlSelect .= "$FieldName = FROM_UNIXTIME($FieldValue)";
                            }
                            $OneWritten = true;
                        }
                        break;

                    case "int":  
                        if ($DeclaredFieldType == "bool") {
                            if($FieldValue != 1) {
                                $FieldValue = 0;
                            }
                        }
                        if($FieldValue != $CurrentValue || is_null($CurrentValue)) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            $this->AddDetail($FieldName, $FieldValue, $CurrentValue);
                            $sqlSelect .= "$FieldName = $FieldValue";
                            $OneWritten = true;
                        }
                        break;  
                        
                    default:
                        if($FieldValue != $CurrentValue) {
                            if ($OneWritten) {
                                $sqlSelect .= ", ";
                                $OneWritten = false;
                            }
                            $this->AddDetail($FieldName, $FieldValue, $CurrentValue);
                            $sqlSelect .= "$FieldName = '$FieldValue'";
                            $OneWritten = true;
                        }
                        break;                  
                }
            }
        }
        // End Query Build loop
        $sqlSelect .= " where $this->PrimaryKey = $this->TableID";
        //debug("sqlSelect", $sqlSelect,true);
        if ($OneWritten) {
            if (ExecuteQuery($sqlSelect)) {
                // Clear values for another modify
                $this->arrFieldsSet = array();
                //Handle AssociationTables
                $this->SaveAssociations();
                //Save the history
                $this->SaveHistory("modify");
                // files
                $this->SaveFiles();
                return true;
            } else {
                return false;
            }
        } else {
            // No changes made to any data
            return true;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DoDelete
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DoDelete() {
        if (is_null($this->TableName)) {
            debug("The TableName has not been set yet.","DoDelete",true);
        }
        if (is_null($this->PrimaryKey)) {
            debug("The PrimaryKey has not been set yet.","DoDelete",true);
        }
        if (is_null($this->TableID)) {
            debug("The TableID has not been set yet.","DoDelete",true);
        }
        $this->DeleteFiles();
        $sqlSelect = "Delete from $this->TableName where $this->PrimaryKey = $this->TableID";
        if (ExecuteQuery($sqlSelect)) {
            //Save the history
            $this->SaveHistory("delete");
            return true;
        } else {
            return false;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Save Files
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SaveFiles() {
        if ($this->HasFiles) {
            for ($looper = 0; $looper < count($this->arrFields); $looper ++) {
                $FieldName = $this->arrFields[$looper];
                $FieldValue = $this->$FieldName;
                $FieldType = $this->arrFieldTypes[$looper];
                $FileSavePath = $this->arrFileSavePath[$looper];
                $ImageDimensions = $this->arrImageDimensions[$looper];
                if (array_key_exists($FieldName, $this->arrTempPaths)) {
                    $TempPath = $this->arrTempPaths[$FieldName];
                } else {
                    $TempPath = null;
                }
                $CopyFile = $this->arrCopyFile[$looper];
                
                
                if($FieldType == "file") {
                    if((!is_null($TempPath) && strlen($TempPath) > 3) || (!is_null($this->MasterImageName) && !is_null($ImageDimensions)) ) {
                        if (!is_null($TempPath) && strlen($TempPath) > 3) {
                            if(strpos($_SERVER['SCRIPT_NAME'], "cms/") > 0 || $_SERVER["SERVER_NAME"] == "www.daddyyankee.com") {
                                $DestFile = GetWebRoot() . $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                            } else {
                                $DestFile = $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                            }
                            move_uploaded_file($TempPath, $DestFile);
                            if ($FieldName == $this->MasterImage) {
                                $this->MasterImagePath = $DestFile;
                                $this->MasterImageName = $FieldValue;
                            }
                        } else {
                            if ($CopyFile && !is_null($ImageDimensions) && !is_null($this->MasterImagePath)) {
                                if(strpos($_SERVER['SCRIPT_NAME'], "cms/") > 0 || $_SERVER["SERVER_NAME"] == "www.daddyyankee.com") {
                                    $DestFile = GetWebRoot() . $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($this->MasterImageName);
                                } else {
                                    $DestFile = $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($this->MasterImageName);
                                }
                                copy($this->MasterImagePath, $DestFile);
                                $FieldValue = $this->MasterImageName;
                                $this->$FieldName = $FieldValue;
                            }
                        }
                        if ($ImageDimensions != null) {
                            $cWidth = $ImageDimensions[0];
                            $cHeight = $ImageDimensions[1];
                            $this->GetImageSize($FieldName, $width, $height);
                            if ($cWidth < $width || $cHeight < $height) {
                                if ($this->AutoCrop) {
                                    $CutWidth = $width;
                                    $CutHeight = $height;
                                    $Ratio = round($cWidth/$cHeight);
                                    if (($width - $cWidth) > ($height - $cHeight)) {
                                        $CutWidth = round($height * ($cWidth/$cHeight));
                                        if ($CutWidth > $width) {
                                            $CutWidth = $width;
                                            $CutHeight = round($width * ($cHeight/$cWidth));
                                        }   
                                    } else {
                                        $CutHeight = round($width * ($cHeight/$cWidth));
                                        if ($CutHeight > $height) {
                                            $CutHeight = $height;
                                            $CutWidth = round($height * ($cWidth/$cHeight));
                                        }   
                                    }
                                    CropImage($DestFile,$DestFile, 0, 0, $CutWidth, $CutHeight, $cWidth, $cHeight);
                                } else {
                                    AddErrorMessage($FieldValue . " has wrong dimensions. [ $width x $height ] Correct dimensions are [ $cWidth x $cHeight ]");
                                }
                            }                           
                        }

                    }
                }
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Delete Files
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DeleteFiles() {
        if ($this->HasFiles) {
            for ($looper = 0; $looper < count($this->arrFields); $looper ++) {
                $FieldName = $this->arrFields[$looper];
                $FieldValue = $this->$FieldName;
                $FieldType = $this->arrFieldTypes[$looper];
                $FileSavePath = $this->arrFileSavePath[$looper];
                if($FieldType == "file") {
                    if(strlen($FieldValue) > 4) {
                        $DestFile = "../" . str_replace("\\","/",$FileSavePath) . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                        unlink($DestFile);
        
                    }
                }
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  Get File Name
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetFileName($FieldName, $AsFilePath = true) {
        $loc = array_search($FieldName, $this->arrFields);
        if($loc) {
            $FieldValue = $this->$FieldName;
            if (strlen($FieldValue) > 3) {
                $FileSavePath = $this->arrFileSavePath[$loc];
                if ($AsFilePath) {
                    if(strpos($_SERVER['SCRIPT_NAME'], "cms/") > 0) {               
                        return GetWebRoot() .  $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                    } else {
                        return $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                    }
                    //return "/" . $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                } else {
                    return  "/" . $FileSavePath . $FieldName . $this->TableID . "." . GetFileExtension($FieldValue);
                }
            }
        } else {
            return false;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetImageSize
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetImageSize($FieldName, &$width, &$height, &$type = null, &$attr = null) {
        if (strlen($this->GetFileName($FieldName, true)) > 3) {
            list($width, $height, $type, $attr) = getimagesize($this->GetFileName($FieldName, true));   
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  DisplayImageLink
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function DisplayImageLink($FieldName,$QueryString) {
        $Display = "";
        $Dimensions = "";
        $loc = array_search($FieldName, $this->arrFields);
        if($loc) {                      
            $FieldValue = $this->$FieldName;
            if (strlen($FieldValue) > 3) {
                $FileName = $this->GetFileName($FieldName,false);
                $Display = "<a href=\"$FileName?" . CacheBuster() . "\" rel=\"milkbox[cms1]\" title=\"$FieldName\">$FieldValue</a>";
                $this->GetImageSize($FieldName, $width, $height);
                $Dimensions = "&nbsp; [ $width x $height ]";
                $ImageDimensions = $this->arrImageDimensions[$loc];
                if ($ImageDimensions != null) {
                    $_SESSION["ReturnPage"] = $_SERVER["PHP_SELF"] . "?$QueryString";
                    $cWidth = $ImageDimensions[0];
                    $cHeight = $ImageDimensions[1];
                    if ($width > $cWidth || $height > $cHeight) {
                        $Dimensions = "<span class=\"Warning\">$Dimensions</span> &nbsp; " .
                            "<a href=\"CropTool.php?FileName=.." . urlencode("$FileName") .
                            "&CropWidth=$cWidth&CropHeight=$cHeight\" class=\"Warning\">CROP</a>";
                    }
                }
            }
        }
        print $Display . $Dimensions;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  LoadFieldInformation
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function LoadFieldInformation() {
        if (!is_null($this->TableID)) {
            $sqlSelect = "select " . implode(", ", $this->arrFieldsSet) . 
                " from $this->TableName" .
                " where $this->PrimaryKey = $this->TableID";
        } else {
            $sqlSelect = "select " . implode(", ", $this->arrFieldsSet) . 
                " from $this->TableName limit 1";
        }
        $tbl = ExecuteQuery($sqlSelect);
        $row = mysql_fetch_object($tbl);
        if ($tbl) {
            for ($looper = 0; $looper < mysql_num_fields($tbl); $looper++) {
                $FieldName = mysql_field_name($tbl, $looper);
                $Loc = array_search($FieldName, $this->arrFields);
                if ($this->arrFieldTypes[$Loc] == "file") {
                    array_push($this->arrDBFieldTypes, "file");
                } else {
                    array_push($this->arrDBFieldTypes, mysql_field_type($tbl, $looper));
                }               
                array_push($this->arrFieldLengths, mysql_field_len($tbl, $looper));
                if (!is_null($this->TableID)) {
                    $FieldName = mysql_field_name($tbl, $looper);
                    array_push($this->arrCurrentValues, $row->$FieldName);
                }
            }
            
        }
        mysql_free_result($tbl);
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SaveAssociations
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function SaveAssociations() {
        $CurrentValue = "";
        $FieldValue = "";
        for ($looper = 0; $looper < count($this->arrAssociationTables); $looper++) {
            $TableName = $this->arrAssociationTables[$looper];
            $FieldName = $this->arrAssociationFields[$looper];          
            $arrValues = $this->arrAssociationValues[$looper];
            // Get current Values
            $sqlSelect = "select $FieldName from $TableName" .
                " where $this->PrimaryKey = $this->TableID";
            $tbl = ExecuteQuery($sqlSelect);
            while ($row = mysql_fetch_object($tbl)) {
                if ($CurrentValue != "") {
                    $CurrentValue .= ", ";
                }
                $CurrentValue .= $row->$FieldName;
            }           
            mysql_free_result($tbl);
            $FieldValue = implode(", ", $arrValues);
            // Save if values are different
            if ($CurrentValue != $FieldValue) {
                // Clear field
                $sqlSelect = "delete from $TableName where $this->PrimaryKey = $this->TableID";
                ExecuteQuery($sqlSelect);
                // Save new values
                foreach ($arrValues as $Value) {
                    if (gettype($Value) == "string") {
                        $Value = mysql_escape_string($Value);
                        $sqlSelect = "insert into $TableName ($this->PrimaryKey, $FieldName)" .
                            " values ($this->TableID, '$Value')";
                    } else {
                        $sqlSelect = "insert into $TableName ($this->PrimaryKey, $FieldName)" .
                            " values ($this->TableID, $Value)";
                    }
                    ExecuteQuery($sqlSelect);
                }
                // Add to history detail
                $this->AddDetail($TableName, $FieldValue, $CurrentValue);
            }
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SaveHistory
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function SaveHistory($inAction, $inTableID = -1, $inNote = "", $inUserID = -1 ) {
        if ($inUserID == -1) {
            if ( !array_key_exists("User", $_SESSION) || is_null($_SESSION["User"]->UserID ) ) {
                $inUserID = 0;
            } else {
                $inUserID = $_SESSION["User"]->UserID;
            }
        }
        if ($inTableID == -1) {
            if (is_null($this->TableID)) {
                $inTableID = 0;
            } else {
                $inTableID = $this->TableID;
            }
        }
        $sqlSelect = "insert into cms_history (UserID, TableName, TableKey, TableID, Action, CreatedDate, Note, IPAddress)" .
            " values (" .
                $inUserID . ", " .
                "'$this->TableName', " .
                "'$this->PrimaryKey', " .
                $inTableID . ", " .
                "'$inAction', " .
                "FROM_UNIXTIME(" . GetTime() . "), " .
                "'" . mysql_escape_string($inNote) . "'," .
                "'" . $_SERVER["REMOTE_ADDR"] . "'" .
            ")";
        if (ExecuteQuery($sqlSelect)) {
            $HistoryID = mysql_insert_id();
            $this->SaveDetail($HistoryID);
        } else {
            return false;
        }
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  AddDetail
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function AddDetail($FieldName, $FieldValue, $CurrentValue = "") {
        $arrDetailValues = array($FieldName, $FieldValue, $CurrentValue);
        array_push($this->arrDetail, $arrDetailValues);
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SaveDetail
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function SaveDetail($HistoryID) {
        
        for ($looper = 0; $looper < count($this->arrDetail); $looper++) {
            $arrDetailValues = $this->arrDetail[$looper];
            $FieldName = $arrDetailValues[0];
            $FieldValue = $arrDetailValues[1];
            $CurrentValue = $arrDetailValues[2];
            $CurrentValue = mysql_escape_string(strval($CurrentValue));
            $FieldValue = mysql_escape_string(strval($FieldValue));
            if (strlen($CurrentValue) > 255) {
                $CurrentValue = mysql_escape_string(substr($CurrentValue, 0, 254));
            }
            if (strlen($FieldValue) > 255) {
                $FieldValue = mysql_escape_string(substr($FieldValue, 0, 254));
            }
            $sqlSelect = "insert into cms_historydetail (HistoryID, FieldName, OldValue, NewValue)" .
                "values (" .
                    $HistoryID . ", " .
                    "'" . mysql_escape_string($FieldName) . "', " .
                    "'$CurrentValue', " .
                    "'$FieldValue'" .
                ")";
            ExecuteQuery($sqlSelect);
        }
        $this->arrDetail = array();
                
    }
    
    
    // ---------------------------------------------------------------------------------
    //  Get/Set functions
    // ---------------------------------------------------------------------------------
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SetTableName
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SetTableName($inTableName) {
        $this->TableName = $inTableName;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetTableName
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetTableName() {
        return $this->TableName;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SetPrimaryKey
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SetPrimaryKey($inPrimaryKey) {
        $this->PrimaryKey = $inPrimaryKey;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetPrimaryKey
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetPrimaryKey() {
        return $this->PrimaryKey;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SetTableID
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SetTableID($inTableID) {
        $this->TableID = $inTableID;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetTableID
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetTableID() {
        return $this->TableID;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SetAllowNulls
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SetAllowNulls($inAllowNulls) {
        $this->AllowNulls = $inAllowNulls;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetAllowNulls
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetAllowNulls() {
        return $this->AllowNulls;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  SetAutoCrop
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SetAutoCrop($inAutoCrop) {
        $this->AutoCrop = $inAutoCrop;
    }
    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //  GetAutoCrop
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function GetAutoCrop() {
        return $this->AutoCrop;
    }
    
}
?>