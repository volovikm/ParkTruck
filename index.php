<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");

$redirect = new Redirect();
$page= "ParkTruck/client-new/map.php";
$redirect->redirectTo($page);
?>