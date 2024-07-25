<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/request.php");

$request = new Request();
$request->post = $_POST;
$request->parseRequest();
$request->sendResponse();
?>