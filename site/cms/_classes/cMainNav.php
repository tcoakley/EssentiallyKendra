<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	MainNav
//		version: 1.0
//		Last Update: 7/1/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cMainNav {

	private $arrMainNav = array();
	private $MainNavDisplay = "";
	private $NavInit = false;

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct() {
		
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddTab
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddNav($TabName, $TabAddress, $Permission = "", $Parent = "") {
		if (!isset($_SESSION["MainNavDisplay"])) {
			if ($Permission == "" || CheckPermission($Permission)) {

				$arrNavItem = array($TabName, $TabAddress);
				
				if ($Parent != "") {
					$Parent = str_replace(" ", "", $Parent);
					if (!isset($this->$Parent)) {
						$this->$Parent = array();
					}
					array_push($this->$Parent, $arrNavItem);
				} else {
					array_push($this->arrMainNav, $arrNavItem);
				}
			}
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayNav
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayNav() {
		if (!isset($_SESSION["MainNavDisplay"])) {
			$this->BuildNav($this->arrMainNav);
		}
		$this->MainNavDisplay = $_SESSION["MainNavDisplay"];
		print($this->MainNavDisplay);
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	BuildNav
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function BuildNav($inArray) {
		
		if(!$this->NavInit) {
			$this->NavInit = true;
			//$this->MainNavDisplay .= "<ul id=\"MainNavigation\" class=\"mainmenu\">\n";
			$this->MainNavDisplay .= "<ul id=\"dropdown-menu\" class=\"dropdown\">\n";
		} else {
			$this->MainNavDisplay .= "	<ul>\n";
		}
		foreach($inArray as $arrNavItem) {
			$TabName = $arrNavItem[0];
			$TabAddress = $arrNavItem[1];
			$this->MainNavDisplay .= "	<li>\n" .
				"		<a href=\"$TabAddress\">$TabName</a>\n";
			$Child = str_replace(" ", "", $TabName);
			if (isset($this->$Child)) {
				if (is_array($this->$Child)) {
					$this->BuildNav($this->$Child);
				}
			}
			$this->MainNavDisplay .= "	</li>\n";
		}
		$this->MainNavDisplay .= "	</ul>\n";

		$_SESSION["MainNavDisplay"] = $this->MainNavDisplay;
	

	}

}
?>