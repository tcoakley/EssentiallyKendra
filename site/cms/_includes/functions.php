<?php
/// =======================================================================================================
/// PHP Function Library
///     version 1.10
///     Last Updated: 6/16/2009
///
/// =======================================================================================================


// #################################################################################
//  CLASS AUTOLOADER
//      Will automatically load in classes in the _classes folder
//      so that they do not need manually included.
// #################################################################################
function __autoload($inClassName) {
    if(strpos($_SERVER['SCRIPT_NAME'], "cms/") > 0) {
            require_once "_classes/$inClassName.php";
        } else {
        require_once "cms/_classes/$inClassName.php";
        }
}

// #################################################################################
//  Database Functions
// #################################################################################
//-----------------------------------------------------------
//  OpenDatabase
//-----------------------------------------------------------
function OpenDatabase() {
    $dbh = mysql_connect (DBAddress, DBUser, DBPassword) or die ('<br>I cannot connect to the database because: <br>' . mysql_error());
    
    if (!mysql_select_db (DBName)) {
        exit("<br>Unable to connect to " . DBName . "<br>" . mysql_error());
    }

}

// ---------------------------------------------------------------------------------
//  ExecuteQuery
// ---------------------------------------------------------------------------------
function ExecuteQuery($sqlSelect) {
    $objConn = OpenDatabase();
    $results = mysql_query($sqlSelect);
    if (!$results) {
        exit("error: [$sqlSelect]<br>" . mysql_error());
    }
    return $results;
}


// #################################################################################
//  Request Functions
// #################################################################################
// ---------------------------------------------------------------------------------
//  RequestString
// ---------------------------------------------------------------------------------
function RequestString($inString) {
    if (array_key_exists($inString, $_REQUEST)) {
        return $_REQUEST[$inString];
    } else {
        return null;
    }
}

// ---------------------------------------------------------------------------------
//  RequestInt
// ---------------------------------------------------------------------------------
function RequestInt($inString) {
    $rv = RequestString($inString);
    if (is_numeric($rv)) {
        return intval($rv);
    } else {
        return null;
    }
}

// ---------------------------------------------------------------------------------
//  RequestFloat
// ---------------------------------------------------------------------------------
function    RequestFloat($inString) {
    $rv = RequestString($inString);
    if (is_numeric($rv)) {
        return floatval($rv);
    } else {
        return null;
    };
}

// ---------------------------------------------------------------------------------
//  RequestBool
// ---------------------------------------------------------------------------------
function RequestBool($inString) {
    $rv = strtolower(RequestString($inString));
    switch ($rv) {
        case "1":
            return 1;
            break;
        case "true":
            return 1;
            break;
        default:
            return 0;
    }
}

// ---------------------------------------------------------------------------------
//  RequestDate
// ---------------------------------------------------------------------------------
function RequestDate($inString) {
    $rv = RequestString($inString);
    if (strlen($rv) > 0) {
        $rv = strtotime($rv);
        if (!$rv) {
            $rv = null;
        }
    } else {
        $rv = null;
    }
    return $rv;
}

// ---------------------------------------------------------------------------------
//  RequestArray
// ---------------------------------------------------------------------------------
function RequestArray($inString) {
    if (array_key_exists($inString, $_REQUEST)) {
        $rv =  $_REQUEST[$inString];
        switch (gettype($rv)) {
            case "array";
                break;
            case "string":
                $rv = array($rv);
                break;
            default:
                null;
                break;
        }
        if (!is_array($rv)) {
            $rv = null;
        }
        return $rv;
    } else {
        return null;
    }

}


// #################################################################################
//  Date Functions
// #################################################################################

// ---------------------------------------------------------------------------------
//  GetTime
//      Function so that time can be adjusted if needed.
// ---------------------------------------------------------------------------------
function GetTime() {
    $rv = time();
    // Adjust for west coast server
    $rv = DateAdd("h", 3, $rv);
    return $rv;
}

// ---------------------------------------------------------------------------------
//  FormatDate
// ---------------------------------------------------------------------------------
function FormatDate($inDate, $inFormat = "%m/%d/%y %H:%M.%S") {
    $rv = "";
    if (is_numeric($inDate)) {
        if ($inDate != 0) {
            $rv = strftime($inFormat,$inDate);          
        }
    }
    return $rv;
}

//----------------------------------------------------------------------------------
//  DateAdd
//----------------------------------------------------------------------------------
function DateAdd($interval, $number, $date) {

    $arrDateTime = getdate($date);
    $hours = $arrDateTime['hours'];
    $minutes = $arrDateTime['minutes'];
    $seconds = $arrDateTime['seconds'];
    $month = $arrDateTime['mon'];
    $day = $arrDateTime['mday'];
    $year = $arrDateTime['year'];

    switch ($interval) {
    
        case 'yyyy':
        case 'y':
            $year+=$number;
            break;
        case 'm':
            $month+=$number;
            break;
        
        case 'd':
            $day+=$number;
            break;
        case 'ww':
        case 'w':
            $day+=($number*7);
            break;
        case 'h':
            $hours+=$number;
            break;
        case 'n':
            $minutes+=$number;
            break;
        case 's':
            $seconds+=$number; 
            break;            
    }
       $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
    return $timestamp;
}



//----------------------------------------------------------------------------------
//= DateDiff
//----------------------------------------------------------------------------------
function DateDiff ($interval,$date1,$date2) {
    // get the number of seconds between the two dates 
$timedifference = $date2 - $date1;

    switch ($interval) {
        case 'w':
            $retval = bcdiv($timedifference,604800);
            break;
        case 'd':
            $retval = bcdiv($timedifference,86400);
            break;
        case 'h':
            $retval =bcdiv($timedifference,3600);
            break;
        case 'n':
            $retval = bcdiv($timedifference,60);
            break;
        case 's':
            $retval = $timedifference;
            break;
            
    }
    return $retval;

}

// #################################################################################
//  Messaging Functions 
// #################################################################################
// ---------------------------------------------------------------------------------
//  AddErrorMessage
// ---------------------------------------------------------------------------------
function AddErrorMessage($inMessage) {
    if (array_key_exists("ErrorMessage", $_SESSION)) {
        if (is_array($_SESSION["ErrorMessage"])) {
            array_push($_SESSION["ErrorMessage"], $inMessage);
        } else {
            $_SESSION["ErrorMessage"] = array($inMessage);
        }
    } else {
        $_SESSION["ErrorMessage"] = array($inMessage);
    }
}
// ---------------------------------------------------------------------------------
//  ClearErrors
// ---------------------------------------------------------------------------------
function ClearErrors() {
    $_SESSION["ErrorMessage"] = null;
}

// ---------------------------------------------------------------------------------
//  AddIncomingMessage
// ---------------------------------------------------------------------------------
function AddIncomingMessage($inMessage) {
    if (array_key_exists("IncomingMessage", $_SESSION)) {
        if (is_array($_SESSION["IncomingMessage"])) {
            array_push($_SESSION["IncomingMessage"], $inMessage);
        } else {
            $_SESSION["IncomingMessage"] = array($inMessage);
        }
    } else {
        $_SESSION["IncomingMessage"] = array($inMessage);
    }
}
// ---------------------------------------------------------------------------------
//  ClearIncomingMessages
// ---------------------------------------------------------------------------------
function ClearIncomingMessages() {
    $_SESSION["IncomingMessage"] = null;
}

// ---------------------------------------------------------------------------------
//  AddPendingMessage
// ---------------------------------------------------------------------------------
function AddPendingMessage($inMessage) {
    if (array_key_exists("PendingMessage", $_SESSION)) {
        if (is_array($_SESSION["PendingMessage"])) {
            array_push($_SESSION["PendingMessage"], $inMessage);
        } else {
            $_SESSION["PendingMessage"] = array($inMessage);
        }
    } else {
        $_SESSION["PendingMessage"] = array($inMessage);
    }
}
// ---------------------------------------------------------------------------------
//  ClearPendings
// ---------------------------------------------------------------------------------
function ClearPendingMessages() {
    $_SESSION["PendingMessage"] = null;
}

// #################################################################################
//  Image Functions
// #################################################################################
function CropImage($FileName,$SaveFile, $Top, $Left, $Width, $Height, $CropWidth, $CropHeight) {
    
    $arrFileName = explode('.', $FileName);
    $extension = array_pop($arrFileName);
    
    //$FileName = ".." . $FileName;
    switch(strtolower($extension)) {
        case "gif":
            $objImage = imagecreatefromgif($FileName);
            break;
        case "png":
            $objImage = imagecreatefrompng($FileName);
            break;
        default:
            $objImage = imagecreatefromjpeg($FileName);
            break;
    }
    
    if($Width > $Height) {
        $DestWidth = $CropWidth;
        $DestHeight = round($CropHeight * $Height / $Width);
    } else {
        $DestWidth = round($CropWidth * $Width / $Height);
        $DestHeight = $CropHeight;
    }

    
    $DestImage = imagecreatetruecolor($CropWidth, $CropHeight);
    
    // handle transparancy    
    if ( ($type == IMAGETYPE_GIF) || ($type == IMAGETYPE_PNG) ) {
        $trnprt_indx = imagecolortransparent($objImage);
        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {
            // Get the original image's transparent color's RGB values
            $trnprt_color  = imagecolorsforindex($objImage, $trnprt_indx);
            // Allocate the same color in the new image resource
            $trnprt_indx    = imagecolorallocate($DestImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

            // Completely fill the background of the new image with allocated color.
            imagefill($DestImage, 0, 0, $trnprt_indx);

            // Set the background color for new image to transparent
            imagecolortransparent($DestImage, $trnprt_indx);
        } elseif ($type == IMAGETYPE_PNG) {

            // Turn off transparency blending (temporarily)
            imagealphablending($DestImage, false);

            // Create a new transparent color for image
            $color = imagecolorallocatealpha($DestImage, 0, 0, 0, 127);

            // Completely fill the background of the new image with allocated color.
            imagefill($DestImage, 0, 0, $color);

            // Restore transparency blending
            imagesavealpha($DestImage, true);
        }
    }
    
    imagecopyresampled($DestImage, $objImage, 0, 0, $Left, $Top, $CropWidth, $CropHeight, $Width, $Height); 
    switch(strtolower($extension)) {
        case "gif":
            imagegif($DestImage, $SaveFile);
            break;
        case "png":
            imagepng($DestImage, $SaveFile,100);
            break;
        default:
            imagejpeg($DestImage,$SaveFile,100);
            break;
    }
    /*
    debug("FileName", $FileName);
    debug("DestImage", $DestImage);
    debug("extension", $extension);
    debug("Left", $Left);
    debug("Top", $Top);
    debug("Width", $Width);
    debug("Height", $Height);
    debug("CropWidth", $CropWidth);
    debug("CropHeight", $CropHeight);
    debug("DestImage", $DestImage, true);
    */
    
}

// ---------------------------------------------------------------------------------
//  Resize Image
// ---------------------------------------------------------------------------------
function ResizeImage($FileName,$SaveFile, $MaxWidth, $MaxHeight = null) {
    
    $extension = GetFileExtension($FileName);
    
    switch(strtolower($extension)) {
        case "gif":
            $objImage = imagecreatefromgif($FileName);
            break;
        case "png":
            $objImage = imagecreatefrompng($FileName);
            break;
        default:
            $objImage = imagecreatefromjpeg($FileName);
            break;
    }
    
    list($width, $height, $type, $attr) = getimagesize($FileName);
    $TargetWidth = $width;
    $TargetHeight = $height;
    if (!is_null($MaxWidth)) {
        if ($MaxWidth < $TargetWidth) {
            $TargetWidth = $MaxWidth;
            $TargetHeight = round($TargetHeight * $TargetWidth / $width);
        }
    }
    if (!is_null($MaxHeight)) {
        if ($MaxHeight < $TargetHeight) {
            $TargetHeight = $MaxHeight;
            $TargetWidth = round($TargetWidth * $TargetHeight / $height);
        }
    }
    

    $DestImage = imagecreatetruecolor($TargetWidth, $TargetHeight);
    
    // handle transparancy    
    if ( ($type == IMAGETYPE_GIF) || ($type == IMAGETYPE_PNG) ) {
        $trnprt_indx = imagecolortransparent($objImage);
        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {
            // Get the original image's transparent color's RGB values
            $trnprt_color  = imagecolorsforindex($objImage, $trnprt_indx);
            // Allocate the same color in the new image resource
            $trnprt_indx    = imagecolorallocate($DestImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

            // Completely fill the background of the new image with allocated color.
            imagefill($DestImage, 0, 0, $trnprt_indx);

            // Set the background color for new image to transparent
            imagecolortransparent($DestImage, $trnprt_indx);
        } elseif ($type == IMAGETYPE_PNG) {

            // Turn off transparency blending (temporarily)
            imagealphablending($DestImage, false);

            // Create a new transparent color for image
            $color = imagecolorallocatealpha($DestImage, 0, 0, 0, 127);

            // Completely fill the background of the new image with allocated color.
            imagefill($DestImage, 0, 0, $color);

            // Restore transparency blending
            imagesavealpha($DestImage, true);
        }
    }

    
    
    imagecopyresampled($DestImage, $objImage, 0, 0, 0, 0, $TargetWidth, $TargetHeight, $width, $height); 
    switch(strtolower($extension)) {
        case "gif":
            imagegif($DestImage, $SaveFile);
            break;
        case "png":
            imagepng($DestImage, $SaveFile,0);
            break;
        default:
            imagejpeg($DestImage,$SaveFile,100);
            break;
    }
    
}


// #################################################################################
//  Other Functions
// #################################################################################

// ---------------------------------------------------------------------------------
//  Debug
// ---------------------------------------------------------------------------------
function debug ($inDisplay, $inVar = "varempty", $inStop = false) {
    print "<div style=\"line-height: 20px;\">Debug: ";
    if ($inVar === "varempty") {
        print "[$inDisplay]";
    } else {
        if (is_array($inVar)) {
            print "<b>$inDisplay:</b> "; 
            var_dump($inVar);
        } else {
            print "<b>$inDisplay:</b> "; 
            var_dump($inVar);
        }
    }
    if ($inStop) {
        exit("<br>Stopped in Debug</div>");
    }
    print("</div>\n");
}

// ---------------------------------------------------------------------------------
//  RandomCode
// ---------------------------------------------------------------------------------
function RandomCode($inMin = 1, $inMax = 9) {
    $arrSeed = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9");
    srand((float) microtime() * 10000000);
    $CodeLength = rand($inMin, $inMax);
    $arrKeys = array_rand($arrSeed,$CodeLength);
    $rv = "";
    foreach ($arrKeys as $key) {
        $rv .= $arrSeed[$key];
    }
    return $rv;
}

// ---------------------------------------------------------------------------------
//  HtmlPrepare
// ---------------------------------------------------------------------------------
function HtmlPrepare($inString) {
    return htmlentities($inString);
}

// ---------------------------------------------------------------------------------
//  XMLPrepare
// ---------------------------------------------------------------------------------
function XMLPrepare($inString) {
    $rv = "";
    if ($inString != null) {
        $rv = str_replace("&", "&amp;", $inString);
        $rv = str_replace("\r\n", "&#xD;", $rv);
        $rv = str_replace("\"", "&quot;", $rv);

        // Spanish characters       
        $rv = str_replace("á", "&#225;", $rv);
        $rv = str_replace("Á", "&#193;", $rv);
        $rv = str_replace("é", "&#233;", $rv);
        $rv = str_replace("É", "&#201;", $rv);
        $rv = str_replace("í", "&#237;", $rv);
        $rv = str_replace("Í", "&#205;", $rv);
        $rv = str_replace("ñ", "&#241;", $rv);
        $rv = str_replace("Ñ", "&#209;", $rv);
        $rv = str_replace("ó", "&#243;", $rv);
        $rv = str_replace("Ó", "&#211;", $rv);
        $rv = str_replace("Ü", "&#220;", $rv);
        $rv = str_replace("ü", "&#252;", $rv);
        $rv = str_replace("Ú", "&#218;", $rv);
        $rv = str_replace("ú", "&#250;", $rv);
        

    }
    return $rv;
}

// ---------------------------------------------------------------------------------
//  DefaultString
// ---------------------------------------------------------------------------------
function DefaultString(&$inString, $DefaultString = "") {
    if (empty($inString) || is_null($inString)) {
        $inString = $DefaultString;
    }
}

// ---------------------------------------------------------------------------------
//  DefaultNumber
// ---------------------------------------------------------------------------------
function DefaultNumber(&$inNumber, $DefaultNumber = 0) {
    if (is_null($inNumber)) {
        $inNumber = $DefaultNumber;
    }
}

// ---------------------------------------------------------------------------------
//  DefaultBoolean
// ---------------------------------------------------------------------------------
function DefaultBoolean(&$inBool, $DefaultBool = false) {
    if (!is_bool($inBool)) {
        $inBool = $DefaultBool;
    }
}


// ---------------------------------------------------------------------------------
//  RedirectPage
// ---------------------------------------------------------------------------------
function RedirectPage($inPage) {
    session_write_close();
    header("Location: $inPage");
}

//--------------------------------------------------------------------------------------
//  DisplayContent
//--------------------------------------------------------------------------------------
function DisplayContent($inContent) {
    return str_replace("\n", "<br \\>\n", HtmlPrepare($inContent));
}

// ---------------------------------------------------------------------------------
//  TruncString
// ---------------------------------------------------------------------------------
function TruncString($inString, $inLength) {
    if ( strlen($inString) > $inLength ) {
        return substr($inString, 0, ($inLength - 3)) . " ...";
    } else {
        return $inString;
    }
}

// ---------------------------------------------------------------------------------
//  GetFileExtension
// ---------------------------------------------------------------------------------
function GetFileExtension($inFileName) {
    return substr($inFileName, strrpos($inFileName, '.') + 1);
}


// ---------------------------------------------------------------------------------
//  GetWebRoot
// ---------------------------------------------------------------------------------
function GetWebRoot() {
    // in windows 
    if (Platform == "windows") {
        $WebRoot = realpath("./");
        $ScriptName = $_SERVER["SCRIPT_NAME"];
        $ScriptName = substr($ScriptName,0,strrpos($ScriptName,"/"));
        $ScriptName = str_replace("/", "\\", $ScriptName);
        return str_replace($ScriptName,"\\",$WebRoot);
    } else {
        return $_SERVER['DOCUMENT_ROOT'] . "/";
    }
}

// ---------------------------------------------------------------------------------
//  CacheBuster
// ---------------------------------------------------------------------------------
function CacheBuster() {
    return FormatDate(GetTime(), "%H%M%S");
}

?>