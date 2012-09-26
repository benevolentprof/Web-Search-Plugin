<?php
	//$CONFIG['db_host'] = "sylvester-mccoy-v2.ics.uci.edu";
	$CONFIG['db_host'] = "hannibalv0";	//hannibalv0.ics.uci.edu
	$CONFIG['db_port'] = "3306";
	$CONFIG['db_name'] = "pligg";
	$CONFIG['db_user'] = "pligguser";
	$CONFIG['db_pass'] = "EZYAu45eyhRRyuAH";
	$CONFIG['db_tb_participants'] = "logger_participants";
	$CONFIG['db_tb_logquery'] = "logger_logquery";
	$CONFIG['db_tb_logclick'] = "logger_logclick";
	$CONFIG['db_tb_questionaire']="logger_questionaire";
	
	$CONFIG['admin_user']="admin";
	$CONFIG['admin_pass']="Chr0m3L0gg3rP@ss!";

	$conn = mysql_pconnect($CONFIG['db_host'].':'.$CONFIG['db_port'],$CONFIG['db_user'],$CONFIG['db_pass']) or die("Cannot connect to database : ".mysql_error());
	//$conn = mysql_pconnect('localhost',$CONFIG['db_user'],$CONFIG['db_pass']) or die("Cannot connect to database : ".mysql_error());
	mysql_select_db($CONFIG['db_name'], $conn) or die("Cannot change database : ".mysql_error());
	mysql_query("SET SESSION character_set_results = 'UTF8'") or die("Cannot change encoding : ".mysql_error());

?>
