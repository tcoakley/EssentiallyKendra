<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	Tab Table
//		version: 1.0
//		Last Update: 6/30/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cTabTable {

	private $arrTabNames = array();
	private $arrTabFunctions = array();
	private $arrTabExtra = array();
	private $TabWidth;

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inTabWidth = 120) {
		$this->SetTabWidth($inTabWidth);
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddTab
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddTab($TabName, $TabFunction, $TabExtra = "", $Permission = "") {
		if ($Permission == "" || $_SESSION["arrPermissions"][$Permission]) {
			array_push($this->arrTabNames, $TabName);
			array_push($this->arrTabFunctions, $TabFunction);
			array_push($this->arrTabExtra, $TabExtra);
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTabWidth
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTabWidth($inTabWidth) {
		$this->TabWidth = $inTabWidth;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayTabs
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayTabs($inFunction) {
		global $PageFunction;
		$CurrentPage = $_SERVER['PHP_SELF'];
		print("<!-- Tab Table -->");
		print("<div id=\"TabTable\">\n<ul>\n");
		for($looper = 0; $looper < count($this->arrTabNames); $looper++) {
			$TabName = $this->arrTabNames[$looper];
			$TabFunction = $this->arrTabFunctions[$looper];
			$TabExtra = $this->arrTabExtra[$looper];
			if ($TabFunction == $PageFunction) {
				$css = "class=\"selected\"";
			} else {
				$css = "";
			}
			print "<li $css><a href=\"$CurrentPage?PageFunction=" . urlencode($TabFunction);
			if ($TabExtra != "") {
				print "&" . $TabExtra;
			}
			print("\" style=\"width: " .$this->TabWidth . "px\">$TabName</a></li>\n");
				
		}
		//print("<li class=\"PageFunction\">$PageFunction</li>\n");
		print("</ul>\n</div>\n");
		print ("<div id=\"TabContent\">\n");
		$inFunction();
		print("</div>\n");
		print("<!-- /Tab Table -->");
	}

}
?>