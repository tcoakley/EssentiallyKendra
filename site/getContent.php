<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tom
 * Date: 4/29/12
 * Time: 6:18 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('includes/constants.php');
require_once('includes/functions.php');

$sectionName = requestString('sn');
$fileName = requestString('fn');

$pageToLoad = "includes/$sectionName/$fileName.php";

require_once($pageToLoad);
