<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $this->pageTitle ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<script type="text/javascript" src="/js/jquery-1.5.1.min.js"></script>
	<link type="text/css" rel="stylesheet" href="/templates/base.css" />
	<link type="text/css" rel="stylesheet" href="/templates/<?= $this->templateFolder ?>/template.css" />
</head>
<body>
	<?php if ($_REQUEST['debug'] == '1') : ?>
	<div style="font-family: consolas, monaco, courier new; font-size: 11px; border: 1px solid #ccc; padding: 20px;">
		SESSION:<br />
		<?= var_export($_SESSION, true) ?>
		<br /><br />
		REQUEST:<br />
		<?= var_export($_REQUEST, true) ?>
		<br /><br />
		ccReg urlParams:<br />
		<?= var_export($this->urlParams, true) ?>
		<br /><br />
		cctts Params:<br />
		<?= var_export($this->ccttsParams, true) ?>
		<br /><br />
	</div>
	<?php endif; ?>
	<div id="pageWrapper">
