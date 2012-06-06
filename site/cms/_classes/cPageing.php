<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	cPageing
//		version: 1.0
//		Last Update: 7/7/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cPageing {

	public $PageNumber;
	public $RecordsPerPage;
	
	public $TableWidth = 750;
	public $PrimaryKey;
	public $CountVar;
	
	private $Filtering = false;
	public $FiltersActive = false;

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct() {
		$this->PageNumber = RequestInt("PageNumber");
		$this->RecordsPerPage = RequestInt("RecordsPerPage");
		
		DefaultNumber($this->PageNumber, 1);
		DefaultNumber($this->RecordsPerPage, 30);

	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	BuildPageDisplay
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function BuildPageDisplay(&$sqlSelect, $PagesToDisplay = 7) {
		$PageNumber = $this->PageNumber;
		$RecordsPerPage = $this->RecordsPerPage;
		$PrimaryKey = $this->GetPrimaryKey();
		$CountVar = $this->GetCountVar();
		DefaultString($CountVar, $PrimaryKey);
		$PageDisplay = "";
		$TableWidth = 

		// Get NumRecords
		$sqlCount = "select count($CountVar) as NumRecords";
		$pos = strpos(strtolower($sqlSelect)," from", 1);
		$sqlCount .= substr($sqlSelect, $pos - strlen($sqlSelect));
		$tbl = ExecuteQuery($sqlCount);
		$row = mysql_fetch_object($tbl);
		$NumRecords = $row->NumRecords;


		if ($NumRecords > $RecordsPerPage || RequestBool("FA")) {
			$MaxPages = ceil($NumRecords/$RecordsPerPage);
			if ($PageNumber > $MaxPages) {
				$PageNumber = $MaxPages;
			}
			
			$StartRecord = (($PageNumber - 1) * $RecordsPerPage);
			$EndRecord = $StartRecord - 1 + $RecordsPerPage + 1;

			if ($StartRecord < 0 ) {
				$StartRecord = 0;
			}
			if ($EndRecord > $NumRecords) {
				$EndRecord = $NumRecords;
			}
			
			$sqlSelect .= " limit $StartRecord, $RecordsPerPage";
			
			if ($PageNumber > 1) {
				$PreviousPage = $PageNumber - 1;
			} else {
				$PreviousPage = 1;
			}
			if ($PageNumber < $MaxPages) {
				$NextPage = $PageNumber + 1;
			} else {
				$NextPage = $MaxPages;
			}

			$PageDisplay = "<div id=\"PageDisplay\" style=\"width:" . $this->TableWidth . "px;\">\n" .
				"\t<ul>\n" .
				"\t\t<li>Viewing " . 
				number_format(($StartRecord + 1),0) . " - " .
				number_format($EndRecord,0) . " of " . 
				number_format($NumRecords,0) . " records</li>\n" .
				"\t\t<li>&nbsp;</li>\n" .
				"\t\t<li><a href=\"javascript:;\" onClick=\"ChangePage('1');return false;\"><<</a></li>\n" .			
				"\t\t<li><a href=\"javascript:;\" onClick=\"ChangePage('" . $PreviousPage . "');return false;\"><</a></li>\n";
			
			if ($MaxPages > 1 || RequestBool("FA")) {
				If ($MaxPages <= $PagesToDisplay) {
					$LowEnd = 1;
					$HighEnd = $MaxPages;
				} else {
					if ($PageNumber < ceil($PagesToDisplay/2)) {
						$LowEnd = 1;
						$HighEnd = $PagesToDisplay;
					} else {
						if ( $PageNumber > ($MaxPages - (floor($PagesToDisplay/2))) ) {
							$LowEnd = $MaxPages - ($PagesToDisplay - 1);
							$HighEnd = $MaxPages;
						} else {
							$LowEnd = $PageNumber - floor($PagesToDisplay/2);
							$HighEnd = $PageNumber + floor($PagesToDisplay/2);
						}
					}

				}

				for ($looper = $LowEnd; $looper <= $HighEnd; $looper++) {
					if ($PageNumber == $looper) {
						$PageDisplay .= "\t\t<li class=\"active\">page $looper</li>\n";
					} else {
						$PageDisplay .= "\t\t<li><a href=javascript: onClick=\"ChangePage('$looper" .
							"');return false;\">$looper</a></li>\n";
					}
				}
			}
			

			
			$PageDisplay .= "\t\t<li><a href=\"javascript:;\" onClick=\"ChangePage('" . $NextPage . "');return false;\">></a></li>\n" .			
				"\t\t<li><a href=\"javascript:;\" onClick=\"ChangePage('" . $MaxPages . "');return false;\">>></a></li>\n";
			if (($MaxPages > 1 && $this->Filtering) || RequestBool("FA")) {
				$this->FiltersActive = true;
				$PageDisplay .= "\t\t<li class=\"Filters\" id=\"FilterButton\">+ Filters</li>\n";
			}
			$PageDisplay .= "\t</ul>\n</div>\n";

		}
		
		return $PageDisplay;
	}
	
	

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetPrimaryKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetPrimaryKey($inValue) {
		$this->PrimaryKey = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetPrimaryKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetPrimaryKey() {
		return $this->PrimaryKey;
	}
	

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetCountVar
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetCountVar($inValue) {
		$this->CountVar = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetCountVar
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetCountVar() {
		return $this->CountVar;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTableWidth
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTableWidth($inValue) {
		$this->TableWidth = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetFiltering
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetFiltering($inValue) {
		$this->Filtering = $inValue;
	}

}
?>