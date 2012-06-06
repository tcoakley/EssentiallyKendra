<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 5/17/12
 * Time: 10:03 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('includes/constants.php');
require_once('includes/functions.php');

$emailAddress = RequestString('emailAddress');

openDatabase();
ExecuteQuery("insert into signups (emailAddress) values('$emailAddress')");
