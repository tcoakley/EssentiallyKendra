<?php
// #################################################################################
//  Configure PHP to run the way I want
//      This should be the first include of every php page
// #################################################################################
session_start();
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(0);
date_default_timezone_set("America/New_York");
// Allow extra memory for image processing
ini_set("memory_limit","60M");


// #################################################################################
//  Database
// #################################################################################

//server
/*
define("DBAddress", "parkauto.db.4072670.hostedresource.com");
define("DBUser", "parkauto");
define("DBPassword", "Mpib1k1n1w@x");
define("DBName", "parkauto");
define("Platform", "windows"); // windows or unix for pathing purposes
*/

//local
define("DBAddress", "localhost");
define("DBUser", "parkauto");
define("DBPassword", "Mpib1k1n1w@x");
define("DBName", "parkauto");
define("Platform", "windows"); // windows or unix for pathing purposes

?>