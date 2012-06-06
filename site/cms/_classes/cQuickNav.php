<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	QuickNav
//		version: 1.0
//		Last Update: 7/3/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cQuickNav {

	private $arrQuickNav = array();

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct() {
		
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddNav
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddNav($NavName, $NavAddress, $Permission = "") {
		if ($Permission == "" || CheckPermission($Permission)) {
			$arrNavItem = array($NavName, $NavAddress);
			array_push($this->arrQuickNav, $arrNavItem);
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayNav
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayNav() {
		print("<!-- QuickNav -->\n<div id=\"QuickNav\">\n\t<ul>\n");
		$arrQuickNav = array_reverse($this->arrQuickNav);
		foreach($arrQuickNav as $arrNavItem) {
			$NavName = $arrNavItem[0];
			$NavAddress = $arrNavItem[1];
			print("\t\t<li><a href=\"$NavAddress\">$NavName</a></li>\n");
		}
		print ("\t</ul>\n</div>\n<!-- /QuickNav -->\n");
	}
	
	

}
?>