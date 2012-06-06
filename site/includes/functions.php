<?php
/// =======================================================================================================
/// PHP Function Library
///     version 1.10
///     Last Updated: 6/16/2009
///
/// =======================================================================================================

// #################################################################################
//  Date Variables for File inclusion 
//  Will be replaced by cms system soon I hope
// #################################################################################
$currentDayFile = strtolower(formatDate(getTime(), '%A') . '.php');
$currentDateFile = formatDate(getTime(), '%m%d%Y') . '.php';

$twoWeeksArray = array();
for ($looper = 0; $looper < 14; $looper++) {
    $targetDate = DateAdd('d',$looper, getTime());
    $twoWeeksArray[] = array(
        'date' => $targetDate,
        'dayFile' => strtolower(formatDate($targetDate, '%A') . '.php'),
        'dateFile' => formatDate($targetDate, '%m%d%Y') . '.php'
    );
}

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
//  openDatabase
//-----------------------------------------------------------
function openDatabase() {
    $dbh = mysql_connect (DBAddress, DBUser, DBPassword) or die ('<br>I cannot connect to the database because: <br>' . mysql_error());
    
    if (!mysql_select_db (DBName)) {
        exit("<br>Unable to connect to " . DBName . "<br>" . mysql_error());
    }

}

// ---------------------------------------------------------------------------------
//  executeQuery
// ---------------------------------------------------------------------------------
function executeQuery($sqlSelect) {
    $objConn = openDatabase();
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
//  requestString
// ---------------------------------------------------------------------------------
function requestString($inString) {
    if (array_key_exists($inString, $_REQUEST)) {
        return $_REQUEST[$inString];
    } else {
        return null;
    }
}

// ---------------------------------------------------------------------------------
//  requestInt
// ---------------------------------------------------------------------------------
function requestInt($inString) {
    $rv = requestString($inString);
    if (is_numeric($rv)) {
        return intval($rv);
    } else {
        return null;
    }
}

// ---------------------------------------------------------------------------------
//  requestFloat
// ---------------------------------------------------------------------------------
function requestFloat($inString) {
    $rv = requestString($inString);
    if (is_numeric($rv)) {
        return floatval($rv);
    } else {
        return null;
    };
}

// ---------------------------------------------------------------------------------
//  requestBool
// ---------------------------------------------------------------------------------
function requestBool($inString) {
    $rv = strtolower(requestString($inString));
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
//  requestDate
// ---------------------------------------------------------------------------------
function requestDate($inString) {
    $rv = requestString($inString);
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
//  requestArray
// ---------------------------------------------------------------------------------
function requestArray($inString) {
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
//  getTime
//      Function so that time can be adjusted if needed.
// ---------------------------------------------------------------------------------
function getTime() {
    $rv = time();
    // Adjust for west coast server
    //$rv = dateAdd("h", 3, $rv);
    return $rv;
}

// ---------------------------------------------------------------------------------
//  formatDate
// ---------------------------------------------------------------------------------
function formatDate($inDate, $inFormat = "%m/%d/%y %H:%M.%S") {
    $rv = "";
    if (is_numeric($inDate)) {
        if ($inDate != 0) {
            $rv = strftime($inFormat,$inDate);          
        }
    }
    return $rv;
}

//----------------------------------------------------------------------------------
//  dateAdd
//----------------------------------------------------------------------------------
function dateAdd($interval, $number, $date) {

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
//= dateDiff
//----------------------------------------------------------------------------------
function dateDiff ($interval,$date1,$date2) {
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
//  addErrorMessage
// ---------------------------------------------------------------------------------
function addErrorMessage($inMessage) {
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
//  clearErrors
// ---------------------------------------------------------------------------------
function clearErrors() {
    $_SESSION["ErrorMessage"] = null;
}

// ---------------------------------------------------------------------------------
//  addIncomingMessage
// ---------------------------------------------------------------------------------
function addIncomingMessage($inMessage) {
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
//  clearIncomingMessages
// ---------------------------------------------------------------------------------
function clearIncomingMessages() {
    $_SESSION["IncomingMessage"] = null;
}

// ---------------------------------------------------------------------------------
//  addPendingMessage
// ---------------------------------------------------------------------------------
function addPendingMessage($inMessage) {
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
function clearPendingMessages() {
    $_SESSION["PendingMessage"] = null;
}

// #################################################################################
//  Image Functions
// #################################################################################
function cropImage($FileName,$SaveFile, $Top, $Left, $Width, $Height, $CropWidth, $CropHeight) {
    
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
function resizeImage($FileName,$SaveFile, $MaxWidth, $MaxHeight = null) {
    
    $extension = getFileExtension($FileName);
    
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
//  getRssFeed
// ---------------------------------------------------------------------------------
function getRssFeed() {
    $arrFeeds = null;
    try {
        $doc = new DOMDocument();
        $doc->load('http://doterrablog.com/feed');
        $arrFeeds = array();
        foreach ($doc->getElementsByTagName('item') as $node) {
            $itemRSS = array ( 
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
            );
            array_push($arrFeeds, $itemRSS);
        }
    } catch(Exception $e) {

    }

    return $arrFeeds;
}

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
//  randomCode
// ---------------------------------------------------------------------------------
function randomCode($inMin = 1, $inMax = 9) {
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
//  htmlPrepare
// ---------------------------------------------------------------------------------
function htmlPrepare($inString) {

    $find[] = 'â€œ'; // left side double smart quote
    $find[] = 'â€'; // right side double smart quote
    $find[] = 'â€˜'; // left side single smart quote
    $find[] = 'â€™'; // right side single smart quote
    $find[] = 'â€¦'; // elipsis
    $find[] = 'â€”'; // em dash
    $find[] = 'â€“'; // en dash

    $replace[] = '"';
    $replace[] = '"';
    $replace[] = "'";
    $replace[] = "'";
    $replace[] = "...";
    $replace[] = "-";
    $replace[] = "-";

    $rv = $inString;
    $rv = utf8_decode($rv);
    $rv = html_entity_decode($rv);
    $rv = htmlentities($rv);
    $rv = str_replace($find, $replace, $rv);

    

    return $rv;
}

// ---------------------------------------------------------------------------------
//  utf8ToEntities
// ---------------------------------------------------------------------------------
function utf8ToEntities ($string) {
    /* note: apply htmlspecialchars if desired /before/ applying this function
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
//
   
    // reject too-short sequences
    $string = preg_replace("/[\302-\375]([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\340-\375].([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\360-\375]..([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\370-\375]...([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\374-\375]....([\001-\177])/", "&#65533;\\1", $string);
   
    // reject illegal bytes & sequences
        // 2-byte characters in ASCII range
    $string = preg_replace("/[\300-\301]./", "&#65533;", $string);
        // 4-byte illegal codepoints (RFC 3629)
    $string = preg_replace("/\364[\220-\277]../", "&#65533;", $string);
        // 4-byte illegal codepoints (RFC 3629)
    $string = preg_replace("/[\365-\367].../", "&#65533;", $string);
        // 5-byte illegal codepoints (RFC 3629)
    $string = preg_replace("/[\370-\373]..../", "&#65533;", $string);
        // 6-byte illegal codepoints (RFC 3629)
    $string = preg_replace("/[\374-\375]...../", "&#65533;", $string);
        // undefined bytes
    $string = preg_replace("/[\376-\377]/", "&#65533;", $string);

    // reject consecutive start-bytes
    $string = preg_replace("/[\302-\364]{2,}/", "&#65533;", $string);
   
    // decode four byte unicode characters
    $string = preg_replace(
        "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e",
        "'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 |" .
        " (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'",
    $string);
   
    // decode three byte unicode characters
    $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
"'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'",
    $string);
   
    // decode two byte unicode characters
    $string = preg_replace("/([\300-\337])([\200-\277])/e",
    "'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'",
    $string);
   
    // reject leftover continuation bytes
    $string = preg_replace("/[\200-\277]/", "&#65533;", $string);
   
    return $string;
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
       

    }
    return $rv;
}

// ---------------------------------------------------------------------------------
//  defaultString
// ---------------------------------------------------------------------------------
function defaultString(&$inString, $defaultString = "") {
    if (empty($inString) || is_null($inString)) {
        $inString = $defaultString;
    }
}

// ---------------------------------------------------------------------------------
//  defaultNumber
// ---------------------------------------------------------------------------------
function defaultNumber(&$inNumber, $defaultNumber = 0) {
    if (is_null($inNumber)) {
        $inNumber = $defaultNumber;
    }
}

// ---------------------------------------------------------------------------------
//  defaultBoolean
// ---------------------------------------------------------------------------------
function defaultBoolean(&$inBool, $DefaultBool = false) {
    if (!is_bool($inBool)) {
        $inBool = $DefaultBool;
    }
}


// ---------------------------------------------------------------------------------
//  redirectPage
// ---------------------------------------------------------------------------------
function redirectPage($inPage) {
    session_write_close();
    header("Location: $inPage");
    die();
}

//--------------------------------------------------------------------------------------
//  displayContent
//--------------------------------------------------------------------------------------
function displayContent($inContent) {
    return str_replace("\n", "<br \\>\n", htmlPrepare($inContent));
}

// ---------------------------------------------------------------------------------
//  truncString
// ---------------------------------------------------------------------------------
function truncString($inString, $inLength) {
    if ( strlen($inString) > $inLength ) {
        return substr($inString, 0, ($inLength - 3)) . " ...";
    } else {
        return $inString;
    }
}

// ---------------------------------------------------------------------------------
//  getFileExtension
// ---------------------------------------------------------------------------------
function getFileExtension($inFileName) {
    return substr($inFileName, strrpos($inFileName, '.') + 1);
}


// ---------------------------------------------------------------------------------
//  getWebRoot
// ---------------------------------------------------------------------------------
function getWebRoot() {
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
//  cacheBuster
// ---------------------------------------------------------------------------------
function cacheBuster() {
    return formatDate(getTime(), "%H%M%S");
}

?>