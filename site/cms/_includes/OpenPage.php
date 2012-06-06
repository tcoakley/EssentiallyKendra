<?php
    DefaultString($PageName, "ParkAuto Administration");
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/shtml; charset=iso-8859-1">
    <meta http-equiv="Content-Language" content="en-us">
    <meta http-equiv="Pragma" content="no-cache">
    <title><?php print $PageName ?> </title>
    
    <!-- Stylesheet Includes -->
    <link rel="stylesheet" href="_css/Global.css" type="text/css">
    <link rel="stylesheet" href="_css/Forms.css" type="text/css">
    <link rel="stylesheet" href="_css/Header.css" type="text/css">
    <link rel="stylesheet" href="_css/dropdown/uvumi-dropdown.css" type="text/css">
    <link rel="stylesheet" href="_css/Messages.css" type="text/css">
    <link rel="stylesheet" href="_css/TabTables.css" type="text/css">
    <link rel="stylesheet" href="_css/DataGrid.css" type="text/css">
    <link rel="stylesheet" href="_css/QuickNav.css" type="text/css">
    <link rel="stylesheet" href="_css/PageDisplay.css" type="text/css">
    <link rel="stylesheet" href="_css/ToolTips.css" type="text/css">
    <link rel="stylesheet" href="_css/HistoryDisplay.css" type="text/css">
    <?php 
        
        //Check for extra style sheets
        if ($StyleSheets != "") {
            $arrStyleSheets = explode(",", $StyleSheets);
            foreach($arrStyleSheets as $StyleSheet) {
                print("<link rel=\"stylesheet\" href=\"_css/" . trim($StyleSheet) . "\" type=\"text/css\">\n");
            }
        }
    ?>
    <!-- /Stylesheet Includes -->
    
    
    <!-- MooTools Scripts -->
    <script type="text/javascript" src="_js/mootools.js"></script>
    <script type="text/javascript" src="_js/mootools_more.js"></script>
    <script type="text/javascript" src="_js/DropMenu2.js"></script>
    <script type="text/javascript" src="_js/UvumiDropdown-compressed.js"></script>
    <!-- /MooTools Scripts -->
    
    <!-- Other Scripts -->
    <?php 
        //Check for extra Java Libraries
        if ($JavaLibraries != "") {
            $arrJavaLibraries = explode(",", $JavaLibraries);
            foreach($arrJavaLibraries as $JavaLibrary) {
                print("<script type=\"text/javascript\" src=\"_js/" . trim($JavaLibrary) . "\"></script>\n");
            }
        }
    ?>
    <!-- /Other Scripts -->

    <!-- On Dom Ready -->
    <script type="text/javascript">
        // DomReady
        window.addEvent('domready', 
            function() {
                <?php 
                if (isset($_SESSION["User"])) {
                    if ($_SESSION["User"]->UserID > 0) {
                        //print("menu = new DropMenu('MainNavigation');\n");
                        ?>
                        var myMenu = new UvumiDropdown("dropdown-menu"); 
                        <?php
                    }
                } 
                if (isset($OnPageLoad)) {
                    print($OnPageLoad);
                }
                ?>
                
            }
        );
    </script>
    <!-- /On Dom Ready -->
    
    

</head>
<body>
<div id="OuterContainer">