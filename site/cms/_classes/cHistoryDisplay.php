<?php
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
//	HistoryDisplay
//		Version 1.0
//		Last Update: 7/7/2008
// #~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~#~
class cHistoryDisplay extends cPageing {

	private $TableName = null;
	private $TableKey = null;
	private $TableID = null;
	private $UserID = null;
	private $CurrentPage;
	private $SortDirection;
	private $SortBy;
	private $TabKey;
	private $TabValue;
	private $arrFormValues = array();
	
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	Constructor
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function __construct() {
		parent::__construct();
		$this->CurrentPage = $_SERVER['PHP_SELF'];
		$this->SetPrimaryKey("HistoryID");
		$this->SortBy = RequestString("SortBy");
		DefaultString($this->SortBy, "hst.CreatedDate");
		$this->SortDirection = RequestString("SortDirection");
		DefaultString($this->SortDirection, "desc");
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	DisplayHistory
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function DisplayHistory() {
		global $PageFunction;
		$this->SetFiltering(true);
		$TableName = $this->TableName;
		$TableKey = $this->TableKey;
		$TableID = $this->TableID;
		$UserID = $this->UserID;
		$RecordsPerPage = $this->RecordsPerPage;
		$CurrentPage = $this->CurrentPage;
		$PrimaryKey = $this->PrimaryKey;
		$arrHistoryID = array();
		$TabKey = $this->TabKey;
		$TabValue = $this->TabValue;
		
		$FilterUser = RequestString("FilterUser");
		$FilterTable = RequestString("FilterTable");
		$FilterID = RequestInt("FilterID");
		$FilterAction = RequestString("FilterAction");
		$FilterDateLT = RequestDate("FilterDateLT");
		$FilterDateGT = RequestDate("FilterDateGT");
		$FilterIPAddress = RequestString("FilterIPAddress");

		$TableWidth = $this->TableWidth;
		$SortBy =  $this->SortBy;
		$SortDirection = $this->SortDirection;
		
		
		// calculate all the div dimensions
		$MainDivWidth = floor($TableWidth / 6) - (14); // 14 accounts for border and padding in styles
		$DetailDivWidth = floor(($TableWidth - 40) / 3) - (14); //40 is margins
		
		// generate the sql statement
		$sqlSelect = "Select HistoryID, TableName, TableID, Action, IPAddress, Note, UNIX_TIMESTAMP(hst.CreatedDate) as CreatedDate, UserName" .
			" from cms_history hst" .
			" left join cms_users u on hst.UserID = u.UserID";
		if ($TableName != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableName = '$TableName'";
		}
		if ($TableKey != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableKey = '$TableKey'";
		}
		if ($TableID != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableID = '$TableID'";
		}
		if ($UserID != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " hst.UserID = '$UserID'";
		}
		if ($FilterUser != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " UserName like '%$FilterUser%'";
		}
		if ($FilterIPAddress != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " IPAddress like '%$FilterIPAddress%'";
		}
		if ($FilterTable != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableName like '%$FilterTable%'";
		}
		if ($FilterID != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableID = '$FilterID'";
		}
		
		if ($FilterAction != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " Action = '$FilterAction'";
		}
		
		if ($FilterID != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " TableID = '$FilterID'";
		}
		
		if ($FilterDateLT != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " hst.CreatedDate > FROM_UNIXTIME($FilterDateLT)";
		}
		
		if ($FilterDateGT != null) {
			$this->AddClause($sqlSelect);
			$sqlSelect .= " hst.CreatedDate < FROM_UNIXTIME($FilterDateGT)";
		}



		if (strlen($SortBy) > 0) {
			$sqlSelect .= " order by $SortBy $SortDirection";
		}
		
		//debug("sqlSelect", $sqlSelect);
		
		//Get RecordCount
		$PageDisplay = $this->BuildPageDisplay($sqlSelect);
		$tbl = ExecuteQuery($sqlSelect);
		
		// Pageing
		print($PageDisplay);
		
		//Filters Form
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
		<form name="frmMain" method="get" action="<?php print $CurrentPage?>">
		<input type="hidden" name="FA" value="<?php print RequestInt("FA")?>">
		<div id="FilterBox" style="width: <?php print ($TableWidth) ?>px; display: none;">
			<div id="FiltersContainer">
				<div class="FilterTitle">Filters:</div>
				<div class="FieldName">User</div>
				<div class="FieldInputs">
					<div class="FloatLeft"><input type="text" class="FilterInput" name="FilterUser" value="<?php print $FilterUser?>"></div>
				</div>
				<div class="clear"></div>
				
				<div class="FieldName">IP Address</div>
				<div class="FieldInputs">
					<div class="FloatLeft"><input type="text" class="FilterInput" name="FilterIPAddress" value="<?php print $FilterIPAddress?>"></div>
				</div>
				<div class="clear"></div>
				
				<div class="FieldName">Table</div>
				<div class="FieldInputs">
					<div class="FloatLeft"><input type="text" class="FilterInput" name="FilterTable" value="<?php print $FilterTable?>"></div>
				</div>
				<div class="clear"></div>
				
				<div class="FieldName">ID</div>
				<div class="FieldInputs">
					<div class="FloatLeft"><input type="text" class="FilterInput" name="FilterID" value="<?php print $FilterID?>"></div>
				</div>
				<div class="clear"></div>
				
				<div class="FieldName">Action</div>
				<div class="FieldInputs">
					<div class="FloatLeft">
						<select class="FilterInput" name="FilterAction">
							<option value=""></option>
							<option value="insert" <?php if ($FilterAction == "insert") { print " selected"; }?>>Insert</option>
							<option value="modify" <?php if ($FilterAction == "modify") { print " selected"; }?>>Modify</option>
							<option value="delete" <?php if ($FilterAction == "delete") { print " selected"; }?>>Delete</option>
							<option value="login" <?php if ($FilterAction == "login") { print " selected"; }?>>Login</option>
							<option value="failed login" <?php if ($FilterAction == "failed login") { print " selected"; }?>>Failed Login</option>
						</select>
					</div>
				</div>
				<div class="clear"></div>
				
				<div class="FieldName">Date</div>
				<div class="FieldInputs">
					<div class="FloatLeft">
						<input type="text" class="FilterInput" name="FilterDateLT" value="<?php print FormatDate($FilterDateLT, "m/d/y");?>">
					</div>
					<div class="FloatLeft" style="margin: 0 10px 0 10px;"> > < </div>
					<div class="FloatLeft">
						<input type="text" class="FilterInput" name="FilterDateGT" value="<?php print FormatDate($FilterDateGT, "m/d/y");?>">
					</div>
				</div>
				<div class="clear" style="height: 20px;"></div>
				
				<div class="FloatLeft" style="margin-right: 20px;"><input type="button" class="button" onClick="ApplyFilters();return false;" value="Apply Filters"></div>
				<div class="FloatLeft"><input type="button" class="button" onClick="ClearFilters();return false;" value="Clear Filters"></div>
				<div class="clear"></div>
			</div>
		</div>
		
		<!-- End Filters -->
		<?php



		// Print Headers
		?>
		
			<input type="hidden" name="<?print $TabKey?>" value="<?php print $TabValue; ?>">
			<input type="hidden" name="PageNumber" value="<?php print($CurrentPage); ?>">
			<input type="hidden" name="RecordsPerPage" value="<?php print($RecordsPerPage); ?>">
			<input type="hidden" name="SortBy" value="<?php print($SortBy); ?>">
			<input type="hidden" name="SortDirection" value="<?php print($SortDirection); ?>">
			<input type="hidden" name="PageFunction" value="<?php print($PageFunction); ?>">
			<input type="hidden" name="FormComplete" value="0">
			<?php
			foreach ($this->arrFormValues as $key => $value) {
				print "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
			}
			?>
			<div id="HistoryTable">
				<div id="thead">
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('UserName');return false">User</a>
						<?php if ($SortBy == "UserName") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('IPAddress');return false">IP Address</a>
						<?php if ($SortBy == "IPAddress") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('TableName');return false">Table</a>
						<?php if ($SortBy == "TableName") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('TableID');return false">ID</a>
						<?php if ($SortBy == "TableID") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('Action');return false">Action</a>
						<?php if ($SortBy == "Action") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
					<div style="width: <?php print $MainDivWidth; ?>px;">
						<a href="javascript:;" onClick="ChangeSortBy('hst.CreatedDate');return false">Date</a>
						<?php if ($SortBy == "hst.CreatedDate") { ?>
							<a href="javascript:;" onClick="ChangeSortDirection();return false;" title="Sort"><img src="_img/s_<?php print $SortDirection?>.png" class="pngFix" style="margin: 0 0 0 4px;"></a>
						<?php } ?>
					</div>
				</div>
				<div class="clear"></div>
				<div id="tbody">		
					<?php
					$css = "";
					while ($row = mysql_fetch_object($tbl)) {
						if ($css == "odd") {
							$css = "even";
						} else {
							$css = "odd";
						}
						$HistoryID = $row->HistoryID;
						$UserName = $row->UserName;
						DefaultString($UserName, "<i>user deleted</i>");
						$IPAddress = $row->IPAddress;
						$TableName = $row->TableName;
						$TableID = $row->TableID;
						$Action = $row->Action;
						$CreatedDate = $row->CreatedDate;
						$Note = $row->Note;
						?>
						<div class="<?php print $css?>">
							<div style="width: <?php print $MainDivWidth ?>px;"><?php print $UserName?></div>
							<div style="width: <?php print $MainDivWidth ?>px;"><?php print $IPAddress?></div>
							<div style="width: <?php print $MainDivWidth ?>px;"><?php print $TableName?></div>
							<div style="width: <?php print $MainDivWidth ?>px;"><?php print $TableID?></div>
							<?php if ($Action == "modify" || $Action == "insert" || $Action == "failed login") { ?>
								<div style="width: <?php print $MainDivWidth ?>px;"><a href="#" id="sa<?php print $HistoryID ?>" name="sa<?php print $HistoryID ?>"><?php print $Action?></a></div>
							<?php } else { ?>
								<div style="width: <?php print $MainDivWidth ?>px;"><?php print $Action?></div>
							<?php } ?>
							<div style="width: <?php print $MainDivWidth ?>px;"><?php print FormatDate($CreatedDate)?></div>

						</div>
						<div class="clear"></div>
						<?php if ($Action == "modify" || $Action == "insert" || $Action == "failed login") { ?>
							<div class="details" id="md<?php print $HistoryID ?>">	
								<?php if ($Action == "failed login") { ?>
									<?php array_push($arrHistoryID, $HistoryID); ?>
									<div class="odd">
										<div style="width: <?php print $DetailDivWidth ?>px;">Attempted Password</div>
										<div style="width: <?php print $DetailDivWidth ?>px;"><?php print $Note; ?></div>
									</div>
									<div class="clear"></div>									
								<?php } else { ?>
									<div class="head" style="width: <?php print $DetailDivWidth ?>px;">Field Name</div>
									<div class="head" style="width: <?php print $DetailDivWidth ?>px;">Old Value</div>
									<div class="head" style="width: <?php print $DetailDivWidth ?>px;">New Value</div>
									<div class="clear"></div>
									<?php

										$sqlSelect = "Select * from cms_historydetail where HistoryID = $HistoryID";
										$tbl2 = ExecuteQuery($sqlSelect);
										$css2 = "";
										array_push($arrHistoryID, $HistoryID);
										while ($row = mysql_fetch_object($tbl2)) {
											if ($css2 == "odd") {
												$css2 = "even";
											} else {
												$css2 = "odd";
											}
											$FieldName = $row->FieldName;
											$OldValue = $row->OldValue;
											$NewValue = $row->NewValue;
											DefaultString($NewValue, "&nbsp;");
											DefaultString($OldValue, "&nbsp;");
											$NewValue = strip_tags($NewValue);
											$OldValue = strip_tags($OldValue);
											?>
											<div class="<?php print $css2?>" 
												<div style="width: <?php print $DetailDivWidth ?>px;"><?php print $FieldName?></div>
												<div style="width: <?php print $DetailDivWidth ?>px;"><?php print $OldValue?></div>
												<div style="width: <?php print $DetailDivWidth ?>px;"><?php print $NewValue?></div>
											</div>
											<div class="clear"></div>
											<?php

										}
									}
								?>
							</div>
							<div class="clear"></div>
						<?php } ?>
						<?php
						
					}
					?>
				</div>
			</div>
		</form>
		<div style="height: 40px;"></div>
		<script type="text/javascript">
			var frm = document.forms["frmMain"];
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
			//ChangePage
			function ChangePage(inPage) {
				frm.PageNumber.value = inPage;
				frm.FormComplete.value = 0;
				frm.submit();
			}
			// PrepSlides
			function PrepSlides() {
				<?php foreach($arrHistoryID as $HistoryID) { ?>
				var vs<?php print $HistoryID ?> = new Fx.Slide('md<?php print $HistoryID ?>');
				vs<?php print $HistoryID ?>.hide();
				$('md<?php print $HistoryID ?>').setStyle("display", "block");
				$('sa<?php print $HistoryID ?>').addEvent('click', function(e){
					e.stop();
					vs<?php print $HistoryID ?>.toggle();
				});
				<?php } ?>
			}

		
		</script>
		<?php
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddWhere
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	private function AddClause(&$sqlSelect) {
		if (!strpos(strtolower($sqlSelect), "where")) {
			$sqlSelect .= " where";
		} else {
			$sqlSelect .= " and";
		}
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	AddFormValue
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function AddFormValue($inName, $inValue) {
		$this->arrFormValues[$inName] = $inValue;
	}
	
	// ---------------------------------------------------------------------------------
	//	Get/Set functions
	// ---------------------------------------------------------------------------------
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTableName
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTableName($inTableName) {
		$this->TableName = $inTableName;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetTableName
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetTableName() {
		return $this->TableName;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTableKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTableKey($inTableKey) {
		$this->TableKey = $inTableKey;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetTableKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetTableKey() {
		return $this->TableKey;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTableID
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTableID($inTableID) {
		$this->TableID = $inTableID;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetTableID
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetTableID() {
		return $this->TableID;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetUserID
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetUserID($inUserID) {
		$this->UserID = $inUserID;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetUserID
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetUserID() {
		return $this->UserID;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTabKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTabKey($inValue) {
		$this->TabKey = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetTabKey
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetTabKey() {
		return $this->TabKey;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	SetTabValue
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function SetTabValue($inValue) {
		$this->TabValue = $inValue;
	}
	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//	GetTabValue
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	function GetTabValue() {
		return $this->TabValue;
	}

	
}
?>