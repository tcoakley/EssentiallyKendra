<?php
// #################################################################################
//	require_onces
// #################################################################################
require_once("../_includes/constants.php");
require_once("../_includes/functions.php");
require_once("../_includes/functions_site.php");

// #################################################################################
//	Initialization
// #################################################################################
$UserName = RequestString("UserName");

// #################################################################################
//	Main Processing
// #################################################################################
$sqlSelect = "Select count(UserID) as TheCount from cms_users where UserName = '" . mysql_escape_string($UserName) . "'";
$tbl = ExecuteQuery($sqlSelect);
$row = mysql_fetch_object($tbl);

$TheCount = $row->TheCount;

if($TheCount > 0 ) {
	print("false");
} else {
	print("true");
}

?>