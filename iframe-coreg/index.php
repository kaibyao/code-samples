<?php
require_once('include/config.php');
require_once($GLOBALS['includePath'] . 'class.ccReg.php');

$ccReg = new CCReg();
$ccReg->loadProcess();
?>