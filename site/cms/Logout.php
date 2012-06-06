<?php
require_once("_includes/constants.php");
require_once("_includes/functions.php");
session_unset();
session_destroy();
RedirectPage("Login.php");
?>