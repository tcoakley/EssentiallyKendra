<?php
/// =======================================================================================================
///	Site Specific Functions
///		For project: WA007 admin
///		version 1.1
///		Last Updated: 10/30/2008
///
/// =======================================================================================================


// ---------------------------------------------------------------------------------
//	Validate Login
// ---------------------------------------------------------------------------------
function ValidateLogin($Permission, $FailPage = "Login.php") {
	// Check for any login
	if (!is_object($_SESSION["User"])) {
		AddErrorMessage("You must login to access the requested page.");
		RedirectPage($FailPage);
	}
	// Check specific page access
	if (!CheckPermission($Permission)) {
		AddErrorMessage("You account does not have access to the requested page");
		RedirectPage($FailPage);
	}
}

// ---------------------------------------------------------------------------------
//	CheckPermission
// ---------------------------------------------------------------------------------
function CheckPermission($Permission, $arrPermissions = null) {
	if (!is_array($arrPermissions)) {
		$arrPermissions = $_SESSION["arrPermissions"];
	}
	if (array_key_exists($Permission,$arrPermissions)) {
		return true;
	} else {
		// Allow Full privileges for anyone with manually added permission "IsTom"
		if (array_key_exists("IsTom",$arrPermissions)) {
			return true;
		} else {
			return false;
		}
	}
}

// ---------------------------------------------------------------------------------
//	img (Display images with text for languages)
// ---------------------------------------------------------------------------------
function img($ImageName, $Alt=null, $Class=null) {
	$CurrentLanguage = $_SESSION["CurrentLanguage"];
	DefaultString($CurrentLanguage, "english");
	if ($CurrentLanguage == "english") {
		$ImagePath = "_img/en/$ImageName";
	} else {
		$ImagePath = "_img/es/$ImageName";
	}
	list($width, $height) = getimagesize($ImagePath);
	DefaultString($Alt, "Language specific image");
	$rv = "<img src=\"$ImagePath\" width=\"$width\" $height=\"$height\" alt=\"$Alt\"";
	if ($Class != null) {
		$rv .= " class=\"$Class\"";
	}
	$rv .= ">";
	print $rv;
}

// ---------------------------------------------------------------------------------
//	css (Language specific style sheet to show)
// ---------------------------------------------------------------------------------
function css($inCSS) {
	$CurrentLanguage = $_SESSION["CurrentLanguage"];
	DefaultString($CurrentLanguage, "english");
	if ($CurrentLanguage == "english") {
		$CSSPath = "_css/en/$inCSS";
	} else {
		$CSSPath = "_css/es/$inCSS";
	}
	$rv = "<link href=\"$CSSPath\" rel=\"stylesheet\" type=\"text/css\" />\n";
	print $rv;
}

// ---------------------------------------------------------------------------------
//	txt (Language specific style sheet to show)
// ---------------------------------------------------------------------------------
function txt($inEnglish, $inSpanish) {
	$CurrentLanguage = $_SESSION["CurrentLanguage"];
	DefaultString($CurrentLanguage, "english");
	if ($CurrentLanguage == "english") {
		print $inEnglish;
	} else {
		$inSpanish = str_replace("á", "&#225;", $inSpanish);
		$inSpanish = str_replace("Á", "&#193;", $inSpanish);
		$inSpanish = str_replace("é", "&#233;", $inSpanish);
		$inSpanish = str_replace("É", "&#201;", $inSpanish);
		$inSpanish = str_replace("í", "&#237;", $inSpanish);
		$inSpanish = str_replace("Í", "&#205;", $inSpanish);
		$inSpanish = str_replace("ñ", "&#241;", $inSpanish);
		$inSpanish = str_replace("Ñ", "&#209;", $inSpanish);
		$inSpanish = str_replace("ó", "&#243;", $inSpanish);
		$inSpanish = str_replace("Ó", "&#211;", $inSpanish);
		$inSpanish = str_replace("Ü", "&#220;", $inSpanish);
		$inSpanish = str_replace("ü", "&#252;", $inSpanish);
		$inSpanish = str_replace("Ú", "&#218;", $inSpanish);
		$inSpanish = str_replace("ú", "&#250;", $inSpanish);
		print $inSpanish;
	}
}


?>