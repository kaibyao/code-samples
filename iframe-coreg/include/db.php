<?php
$dbh=mysql_connect ("fake_host", "fake_user", "fake_pw") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("fake_db", $dbh);
?>