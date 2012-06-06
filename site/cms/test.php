<?php
// #################################################################################
//	require_onces
// #################################################################################
require_once("_includes/constants.php");
require_once("_includes/functions.php");
require_once("_includes/functions_site.php");

// #################################################################################
//	Validate Login
// #################################################################################
//ValidateUserLogin();


// #################################################################################
//	Initialization
// #################################################################################
$FormComplete = RequestBool("FormComplete");
$PageFunction = RequestString("PageFunction");
$AddAnother = RequestBool("AddAnother");
$Zip = RequestString("Zip");

// #################################################################################
//	MainProcessing
// #################################################################################
if ($FormComplete) {

}


// #################################################################################
//	Display
// #################################################################################
$PageTitle = "info";
$JavaLibraries = "Moobox007.js,swfobject/swfobject.js";
$StyleSheets = "Moobox007.css";
$OnPageLoad = "";
?>
<?php require_once("_includes/OpenPage.php"); ?>
<?php require_once("_includes/PageHeader.php"); ?>
<?php require_once("_includes/MainNav.php"); ?>
<?php require_once("_includes/Messages.php"); ?>



	<!-- Content Area -->
	<div id="ContentContainer" style="background: #fff;padding:30px; min-height: 400px;">
		<!-- Messages -->
		<?php require_once("_includes/Messages.php"); ?>
		<!-- /Messages -->
		
		
		<?php
		
		$IPAddress = $_SERVER["REMOTE_ADDR"];
		//$IPAddress = "161.132.13.1";
		$IPNumber = ConvertIPAddress($IPAddress);
		$sqlSelect = "Select * from cms_ipaddress where ip_from <= $IPNumber and $IPNumber <= ip_to";
		//debug("sqlSelect", $sqlSelect);
		$tbl = ExecuteQuery($sqlSelect);
		print "<b>IP Address Location Lookup</b><br>";
		print "IP Address: $IPAddress<br>";
		print "IP Number: $IPNumber<br>";
		if ($row = mysql_fetch_object($tbl)) {
			$country_name = $row->country_name;
			$country_region = $row->country_region;
			$city = $row->city;
			$latitude = $row->latitude;
			$longitude = $row->longitude;
			if ($country_region != "-") {
				print "Country: $country_name<br>";
				print "Region: $country_region<br>";
				print "City: $city<br>";
				print "Latitude: $latitude<br>";
				print "Longitude: $longitude<br>";
			} else {
				print "Lookup Failed";
			}
		}
		print "<div style=\"height: 20px;\"></div>\n";
		if (strlen($Zip) == 5) {
			$sqlSelect = "select * from cms_zip where ZipCode = '$Zip'";
			$tbl = ExecuteQuery($sqlSelect);
			print "<b>Zip Location Lookup</b><br>";
			print "Zip: $Zip<br>";
			if ($row = mysql_fetch_object($tbl)) {
				$ZipType = $row->ZipType;
				$CityName = $row->CityName;
				$CityType = $row->CityType;
				$StateName = $row->StateName;
				$StateAbbr = $row->StateAbbr;
				$AreaCode = $row->AreaCode;
				$Latitude = $row->Latitude;
				$Longitude = $row->Longitude;
				print "State: $StateName<br>";
				print "City: $CityName<br>";
				print "Area Code: $AreaCode<br>";
				print "Latitude: $Latitude<br>";
				print "Longitude: $Longitude<br>";
			}
		}
		
		
		function ConvertIPAddress($IPAddress) {
			$arrParts = explode(".", $IPAddress);
			$rv = ($arrParts[0] * (256 * 256 * 256)) +
				($arrParts[1] * (256 * 256)) +
				($arrParts[2] * 256) +
				$arrParts[3];
			return $rv;
		}
		?>
		<p>
		<form name="frmMain" method="post" action="test.php">
		
			Zip: <input type="text" name="Zip" maxlength="5" value="<?php print $Zip?>">
			<br>
			<input type="submit" value="Lookup">
		</form>

		<div class="clear"></div>
	</div>
	<!-- /Content Area -->
			
<?php require_once("_includes/ClosePage.php"); ?>


<?php
// #################################################################################
//	Functions
// #################################################################################


?>