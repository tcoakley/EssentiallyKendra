<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	DataGrid
//		version: 1.0
//		Last Update: 7/2/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cDataGrid extends cPageing {

	private $sqlSelect;
	private $CanDelete = true;
	private $DeleteFunction;
	private $DeleteConfirmation = "Are you certain you wish to delete this record. This action cannot be undone.";
	private $CanModify = true;
	private $ModifyFunction;
	private $CanSelect = true;
	private $SelectFunction;
	private $SelectDisplay = "<img src=\"_img/b_drop.png\" width=\"16px\" height=\"16px\" style=\"border: 0;margin: 3px 0 0 0;\" title=\"delete\" />";
	private $SelectConfirmation = "";
	private $CurrentPage;
	private $arrColumns = array();
	private $arrFilters = array();
	private $SortDirection;
	private $SortBy;
	

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct($inQuery = "") {
		$this->CurrentPage = $_SERVER['PHP_SELF'];
		$this->arrFilters  = RequestArray("Filters");
		$this->SortBy = RequestString("SortBy");
		$this->SortDirection = RequestString("SortDirection");
		DefaultString($this->SortDirection, "desc");
		parent::__construct();
		if($inQuery != "") {
			$this->SetQuery($inQuery);
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddColumn
	//		Valid Formats
	//		Date, FormatString
	//		Number, Decimal Places
	//		Currency, Decimal Places
	//		Percent, Decimal Places
	//		Trunc, Max Characters
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddColumn($DisplayName, $FieldName, $arrFormat = null, $FilterType = "string") {
		if (!is_array($arrFormat) && !is_null($arrFormat) && strlen($arrFormat) > 0) {
			$arrFormat = array($arrFormat);
		}
		//debug("arrFormat", $arrFormat);
		$arrColumn = array($DisplayName, $FieldName, $arrFormat, $FilterType);
		array_push($this->arrColumns, $arrColumn);
	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayGrid
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayGrid() {
		global $PageFunction;
		$FilterString = "";
		// Validate that enough information is set to display grid.
		if ($this->CanDelete && strlen($this->DeleteFunction) < 1) {
			debug("Delete Function has not been set", "", 1);
		}
		if ($this->CanModify && strlen($this->PrimaryKey) < 1) {
			debug("Primary Key has not been set", "", 1);
		}
		if ($this->CanModify && strlen($this->ModifyFunction) < 1) {
			debug("Modify Function has not been set", "", 1);
		}
		if ($this->CanDelete && strlen($this->DeleteFunction) < 1) {
			debug("Delete Function has not been set", "", 1);
		}
		if ($this->CanSelect && strlen($this->SelectFunction) < 1) {
			debug("Select Function has not been set", "", 1);
		}
		if (strlen($this->sqlSelect) < 2) {
			debug("The query has not been set.", "", 1);
		}
		
		
		// Calculate number of tool columns
		$ToolColumns = 0;
		if ($this->CanSelect) {
			$ToolColumns++;
		}
		if ($this->CanModify) {
			$ToolColumns++;
		}
		if ($this->CanDelete) {
			$ToolColumns++;
		}
		
		$SelectDisplay = $this->SelectDisplay;
		$RecordsPerPage = $this->RecordsPerPage;
		$CurrentPage = $this->CurrentPage;
		$PrimaryKey = $this->PrimaryKey;
		$arrColumns = $this->arrColumns;
		$TableWidth = $this->TableWidth;
		$sqlSelect = $this->sqlSelect;
		$SortBy =  $this->SortBy;
		$SortDirection = $this->SortDirection;
		
		
		// Filter Application
		$FA = RequestBool("FA");
		if (RequestBool("FA")) {
			$FLoc = 0;
			$FilterString = "";
			foreach($this->arrColumns as $arrColumn) {
				$FLoc++;
				$DisplayName = $arrColumn[0];
				$FieldName = $arrColumn[1];
				$FilterType = $arrColumn[3];
				switch (strtolower($FilterType)) {
					case "string":
						$FV = RequestString("f" . $FLoc);
						if (strlen($FV) > 0) {
							$Addition = $FieldName . " like '%" . mysql_escape_string($FV) . "%'";
							$this->AddFilterString($FilterString, $Addition);							
						}
						break;
					case "int":
						$FV = RequestInt("f" . $FLoc);
						if ($FV != null) {
							$Addition = $FieldName . " > '" . $FV . "'";
							$this->AddFilterString($FilterString, $Addition);	
						}
						$FLoc++;
						$FV = RequestInt("f" . $FLoc);
						if ($FV != null) {
							$Addition = $FieldName . " < '" . $FV . "'";
							$this->AddFilterString($FilterString, $Addition);	
						}
						break;
					case "date":
						$FV = RequestDate("f" . $FLoc);
						if ($FV != null) {
							$Addition = $FieldName . " > FROM_UNIXTIME(" . $FV . ")";
							$this->AddFilterString($FilterString, $Addition);	
						}
						$FLoc++;
						$FV = RequestDate("f" . $FLoc);
						$FV = RequestDate("f" . $FLoc);
						if ($FV != null) {
							$Addition = "UNIX_TIMESTAMP(" . $FieldName . ") < '" . $FV . "'";
							$this->AddFilterString($FilterString, $Addition);	
						}
						break;
					case "bool":
						$FV = RequestInt("f" . $FLoc);
						if ($FV != null) {
							$Addition = $FieldName . " = '" . $FV . "'";
							$this->AddFilterString($FilterString, $Addition);	
						}
						break;					
				}
			}
			if ($_SESSION["FilterString"] != $FilterString) {
				$this->PageNumber = 1;
				$_SESSION["FilterString"] = $FilterString;
			}
			if ($FilterString != "") {
				if (strpos($sqlSelect, "where") > 0) {
					$sqlSelect .= " and " . $FilterString;
				} else {
					$sqlSelect .= " where " . $FilterString;
				}
			}
		}
		
		
		// Sort By
		if (strlen($SortBy) > 0) {
			$sqlSelect .= " order by $SortBy $SortDirection";
		}
		
		//Get RecordCount
		$PageDisplay = $this->BuildPageDisplay($sqlSelect);
		//debug("sqlSelect", $sqlSelect);
		$tbl = ExecuteQuery($sqlSelect);
		
		//OpenContainer
		print("<div id=\"DataGridContainer\">\n");
		

		// Pageing
		print($PageDisplay);
		
		// Open Form
		print("<form name=\"frmMain\" method=\"get\" action=\"$CurrentPage\">\n" .
			"<input type=\"hidden\" name=\"$PrimaryKey\" value=\"\">\n");
		
		// Filters
		if ($this->FiltersActive) {
			$this->DisplayFiltersForm();
		}
		
		// Print Headers
		print("<table id=\"DataTable\" style=\"width: " .$TableWidth . "px;\">\n" .
			"\t<thead>\n" .
			"\t\t<tr>\n");
		if($ToolColumns > 0) {
			print("<th colspan=\"$ToolColumns\">&nbsp;</th>\n");
		}
		foreach($arrColumns as $arrColumn) {
			$DisplayName = $arrColumn[0];
			$FieldName = $arrColumn[1];
			if ($FieldName == $SortBy) {
				print("<th>" .
					"<a href=\"javascript:;\" onClick=\"ChangeSortDirection();return false;\" title=\"Sort\">$DisplayName</a>" .
					"<a href=\"javascript:;\" onClick=\"ChangeSortDirection();return false;\" title=\"Sort\"><img src=\"_img/s_$SortDirection.png\" class=\"pngFix\" style=\"margin: 0 0 0 4px;\"></a>" .						
				"</th>\n");
			} else {
				print("<th><a href=\"javascript:;\" onClick=\"ChangeSortBy('$FieldName');return false;\" title=\"Sort\">$DisplayName</a></th>\n");
			}
		}
		print("\t\t</tr>\n" .
			"\t</thead>\n");
		// End Print Headers
		
		// Print Data
		print("\t<tbody>\n");
		$RowLoc = 0;
		$css = "";
		while ($row = mysql_fetch_object($tbl)) {
			if ($css == "odd") {
				$css = "even";
			} else {
				$css = "odd";
			}
			$RowLoc++;
			$TableID = $row->$PrimaryKey;
			print("\t\t<tr class=\"$css\">\n");
			if ($this->CanSelect) {
				print("<td class=\"tool\"><input type=\"checkbox\" class=\"CheckBox\" name=\"" . $PrimaryKey . "[]\" value=\"$TableID\" /></td>\n");
			}
			if ($this->CanModify) {
				print("<td class=\"tool\"><a href=\"$CurrentPage?PageFunction=" . urlencode($this->ModifyFunction) . 
					"&" . $PrimaryKey . 
					"=$TableID\"><img src=\"_img/b_edit.png\" width=\"16px\" height=\"16px\" title=\"edit\" /></a></td>\n");
			}
			if ($this->CanDelete) {
				print("<td class=\"tool\"><a href=\"javascript:;\" onClick=\"ConfirmDelete($TableID);return false;\"><img src=\"_img/b_drop.png\" width=\"16px\" height=\"16px\" title=\"delete\" /></a></td>\n");
			}
			foreach($arrColumns as $arrColumn) {
				$FieldName = $arrColumn[1];
				$arrFormat = $arrColumn[2];
				$FieldValue = $row->$FieldName;
				
				if(is_array($arrFormat)) {
					$Format = strtolower($arrFormat[0]);
					switch ($Format) {
						case "date":
							$FieldValue = FormatDate($FieldValue, $arrFormat[1]);
							break;

						case "number":
							$FieldValue = number_format($FieldValue, $arrFormat[1]);
							break;
							
						case "currency":
							$FieldValue = "$" . number_format($FieldValue, $arrFormat[1]);
							break;
							
						case "percent":
							$FieldValue = number_format($FieldValue, $arrFormat[1]) . "%";
							break;
							
						case "trunc":
							$FieldValue = TruncString($FieldValue, $arrFormat[1]);
							break;
							
						case "bool":
							if ($FieldValue > 0) {
								$FieldValue = "true";
							} else {
								$FieldValue = "false";
							}
							break;
					}
				}
				print("\t\t\t<td>$FieldValue</td>\n");
			}
			print("\t\t</tr>\n");
		}
		print("\t</tbody>\n");
		// end Print Data
		print("</table>\n");
		if ($this->CanSelect) {
			print("<table><tr>\n" .
				"\t<td><img src=\"_img/arrow_ltr.png\" width=\"38\" height=\"22\" style=\"padding: 0 10px 0 10px;\" /></td>\n" .
				"\t<td><a href=\"javascript:;\" onClick=\"SetChecks(true);\">Check All</a>\n /</td>" .
				"\t<td><a href=\"javascript:;\" onClick=\"SetChecks(false);\">Uncheck All</a></td>\n" .
				"\t<td><i>With selected:</i></td>\n" .
				"\t<td><a href=\"javascript:;\" onClick=\"SelectFunction();return false;\">$SelectDisplay</a></td>\n" .
				"</tr></table>\n");
		}
		//Form and javascript
		?>
		
			<input type="hidden" name="PageNumber" value="<?php print($CurrentPage)?>">
			<input type="hidden" name="RecordsPerPage" value="<?php print($RecordsPerPage)?>">
			<input type="hidden" name="SortBy" value="<?php print($SortBy)?>">
			<input type="hidden" name="SortDirection" value="<?php print($SortDirection)?>">
			<input type="hidden" name="PageFunction" value="<?php print($PageFunction)?>">
			<input type="hidden" name="FormComplete" value="0">
		</form>
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
			// SelectFunction
			function SelectFunction() {
				<?php if ($this->SelectConfirmation != "") { ?>
					IsConfirmed = confirm('<?php print($this->SelectConfirmation)?>');
					if (IsConfirmed) {
						frm.PageFunction.value = "<?php print($this->SelectFunction)?>";
						frm.FormComplete.value = 1;
						frm.submit();
					}
				<?php } else { ?>
					frm.PageFunction.value = "<?php print($this->SelectFunction); ?>";
					frm.FormComplete.value = 1;
					frm.submit();
				<?php } ?>
			}
			// ChangeSortDirection
			function ChangeSortDirection() {
				<?php if ($SortDirection == "asc") { ?>
					frm.SortDirection.value = "desc";
				<?php } else { ?>	
					frm.SortDirection.value = "asc";
				<?php } ?>
				frm.PageNumber.value = 1;
				frm.FormComplete.value = 0;
				frm.submit();
			}
			//ChangeSortBy
			function ChangeSortBy(inSortBy) {
				frm.SortBy.value = inSortBy;
				frm.PageNumber.value = 1;
				frm.FormComplete.value = 0;
				frm.submit();
			}
			//ConfirmDelete
			function ConfirmDelete(inID) {
				IsConfirmed = confirm('<?php print($this->DeleteConfirmation)?>');
				if (IsConfirmed) {
					SetChecks(false);
					frm.PageFunction.value = "<?php print($this->DeleteFunction); ?>";
					frm.<?php print("$PrimaryKey");?>.value = inID;
					frm.FormComplete.value = 1;
					frm.submit();
				}
			}
			//SetChecks
			function SetChecks(inValue) {
				$$("input.CheckBox").each(function(el) {
					el.setProperty("checked", inValue);
				});
			}
			//ChangePage
			function ChangePage(inPage) {
				frm.PageNumber.value = inPage;
				frm.FormComplete.value = 0;
				frm.submit();
			}

		
		</script>
		<?php
		print "</div>"; // Closing DataGridContainer
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddFilterString
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddFilterString(&$FilterString, $Addition) {
		if (strlen($FilterString) > 0) {
			$FilterString .= " and ";
		}
		$FilterString .= $Addition;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayFiltersForm
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayFiltersForm() {
		?>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				var FilterButton = $("FilterButton");
				if (FilterButton) {
					var FilterBox = $("FilterBox");
					var mySlide = new Fx.Slide(FilterBox).hide();
					FilterBox.setStyle("display", "block");
					FilterButton.addEvent("click", function() {
						mySlide.toggle();
					});
				}
			});
			function ClearFilters() {
				$$('input.FilterInput').each(function(a){
					a.value = "";
				});
				$$('select.FilterInput').each(function(a){
					a.value = "";
				});
				frm.FA.value = 0;
			}
			function ApplyFilters() {
				var frm = document.forms["frmMain"];
				frm.FA.value = 1;
				frm.submit();
			}
		</script>
		<!-- Begin Filters -->
		<div id="FilterBox" style="width: <?php print $this->TableWidth ?>px; display: none;">
			<div id="FiltersContainer">
				<input type="hidden" name="FA" value="<?php print RequestInt("FA")?>">
				<div class="FilterTitle">Filters:</div>
				<?php
				$FLoc = 0;
				foreach($this->arrColumns as $arrColumn) {
					$FLoc++;
					$DisplayName = $arrColumn[0];
					$FieldName = $arrColumn[1];
					$FilterType = $arrColumn[3];
					print "<div class=\"FieldName\">" . $DisplayName . "</div>\n" . 
						"<div class=\"FieldInputs\">\n";
					switch (strtolower($FilterType)) {
						case "string":
							$FV = RequestString("f" . $FLoc);
							print "<div class=\"FloatLeft\"><input type=\"text\" class=\"FilterInput\" name=\"f" . $FLoc . "\" value=\"" . HtmlPrepare($FV) . "\"></div>\n";
							break;
						case "int":
							$FV = RequestInt("f" . $FLoc);
							print "<div class=\"FloatLeft\">\n";
							print "<input type=\"text\" class=\"FilterInput\" name=\"f" . $FLoc . "\" value=\"" . HtmlPrepare($FV) . "\">";
							print "</div>";
							print "<div class=\"FloatLeft\">\n";
							print "&nbsp;>&nbsp;&nbsp;<&nbsp;";
							print "</div>";
							$FLoc++;
							$FV = RequestInt("f" . $FLoc);
							print "<div class=\"FloatLeft\">\n";
							print "<input type=\"text\" class=\"FilterInput\" name=\"f" . $FLoc . "\" value=\"" . HtmlPrepare($FV) . "\">\n";
							print "</div>\n";
							break;
						case "date":
							$FV = RequestDate("f" . $FLoc);
							print "<div class=\"FloatLeft\">\n";
							print "<input type=\"text\" class=\"FilterInput\" name=\"f" . $FLoc . "\" value=\"" . FormatDate($FV,"%m/%d/%y") . "\">\n";
							print "</div>\n";
							print "<div class=\"FloatLeft\">\n";
							print "&nbsp;>&nbsp;&nbsp;<&nbsp;";
							print "</div>\n";
							$FLoc++;
							$FV = RequestDate("f" . $FLoc);
							print "<div class=\"FloatLeft\">\n";
							print "<input type=\"text\" class=\"FilterInput\" name=\"f" . $FLoc . "\" value=\"" . FormatDate($FV,"%m/%d/%y") . "\">\n";
							print "</div>\n";
							break;
						case "bool":
							$FV = RequestInt("f" . $FLoc);
							print "<select name=\"f" . $FLoc . "\" class=\"FilterInput\">\n" .
								"<option value=\"\"></option>\n" .
								"<option value=\"0\"";
							if ($FV == 0) {
								print " selected";
							}
							print ">False</option>\n" .
								"<option value=\"1\"";
							if ($FV == 1) {
								print " selected";
							}
							print ">True</option\n";
							print "</select>";

							break;

					}
					print "</div>\n<div class=\"clear\"></div>\n";
				}
				?>
				<br>
				<div class="FloatLeft" style="margin-right: 20px;"><input type="button" class="button" onClick="ApplyFilters();return false;" value="Apply Filters"></div>
				<div class="FloatLeft"><input type="button" class="button" onClick="ClearFilters();return false;" value="Clear Filters"></div>
				<div class="clear"></div>
			</div>
		</div>
		
		<!-- End Filters -->
		<?php

	}
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetQuery
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetQuery($inQuery) {
		$this->sqlSelect = $inQuery;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetCanSelect
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetCanSelect($inBool) {
		$this->CanSelect = $inBool;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetSelectDisplay
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetSelectDisplay($inValue) {
		$this->SelectDisplay = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetSelectConfirmation
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetSelectConfirmation($inValue) {
		$this->SelectConfirmation = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetSelectFunction
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetSelectFunction($inValue) {
		$this->SelectFunction = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetCanModify
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetCanModify($inBool) {
		$this->CanModify = $inBool;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetModifyFunction
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetModifyFunction($inValue) {
		$this->ModifyFunction = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetCanDelete
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetCanDelete($inBool) {
		$this->CanDelete = $inBool;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetDeleteFunction
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetDeleteFunction($inValue) {
		$this->DeleteFunction = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetDeleteConfirmation
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetDeleteConfirmation($inValue) {
		$this->DeleteConfirmation = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetSortBy
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetSortBy($inValue) {
		if( is_null(RequestString("SortBy"))) {
			$this->SortBy = $inValue;
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetSortDirection
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetSortDirection($inValue) {
		if( is_null(RequestString("SortDirection"))) {
			$this->SortDirection = $inValue;
		}
	}
	


}
?>