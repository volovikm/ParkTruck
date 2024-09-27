<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");

$account = new Account();
$account->logoutUser();
?>