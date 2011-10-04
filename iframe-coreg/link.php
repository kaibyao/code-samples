<?php
require_once("include/config.php");
require_once($GLOBALS['includePath'] . 'class.ccReg.php');

$ccReg = new CCReg();
$paramsStr = $_GET['params'];
$params = json_decode(base64_decode($paramsStr));

$ccReg->pixelPathClick($paramsStr);

$url = (string)$params->linkURL;
header('Location: '.$url);
exit;
?>