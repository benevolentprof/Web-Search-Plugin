<?php
	//check login
	session_start();
	if(!isset($_SESSION['adminlogin'])||$_SESSION['adminlogin']!=true){
		die("Login required to access this page. ".
				"Please click <button onclick=\"location.href='index.php';return false;\">Login</button> to login");
	}

	require "config.php";
	if(!($_GET['ParticipantID'])){
		echo "no ParticipantID found!";
		return;
	}
	$ParticipantID = $_GET['ParticipantID'];
	$sql = "SELECT ".
				"q.queryid as qid, ".
				"q.timestamp as qtime, ".
				"q.site as site, ".
				"q.query as query, ".
				"c.clickid as cid, ".
				"c.timestamp as ctime, ".
				"c.page as page, ".
				"c.`index` as `index`, ".
				"c.title as title, ".
				"c.url as url, ".
				"c.button as button, ".
				"qa.SearchGoal as SearchGoal, ".
				"qa.IsTryRemember as IsTryRemember, ".
				"qa.SearchTime as SearchTime, ".
				"qa.IsSuccessful as IsSuccessful ".
			" FROM ".$CONFIG['db_tb_logquery']." AS q ".
			" LEFT JOIN ".$CONFIG['db_tb_logclick']." AS c ".
				" ON q.queryid = c.queryid ".
				" AND q.participantid = c.participantid ".
			" LEFT JOIN ".$CONFIG['db_tb_questionaire']." AS qa ".
				" ON q.queryid = qa.queryid ".
			" WHERE q.participantid='".$ParticipantID."' ".
			" ORDER BY qtime ASC,ctime ASC ;";
	$result = mysql_query($sql);
	if($result){
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=log_".$ParticipantID.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		//start looping to create table/or group of clicks
		while(($row = mysql_fetch_assoc($result))!=null){
			
			$qid = $row['qid'];
			$qtime = $row['qtime'];
			$site = $row['site'];
			$query = urldecode(str_replace("+"," ",$row['query']));
			$cid = $row['cid'];
			$ctime = $row['ctime'];
			$page = $row['page'];
			$index = $row['index'];
			$title = $row['title'];
			$url = urldecode($row['url']);
			switch($row['button']){
				case 0:$button='left click';break;
				case 1:$button='middle click';break;
				case 2:$button='right click'; break;
				default: $button='unknown button';
			}			
			$SearchGoal = $row['SearchGoal'];
			$IsTryRemember = $row['IsTryRemember']?"Yes":"No";
			$SearchTime = $row['SearchTime'];
			$IsSuccessful = $row['IsSuccessful']?"Yes":"No";
		
			echo "$qid,$qtime,$site,$query,$cid,$ctime,$page,$index,$title,$url,$button,$SearchGoal,$IsTryRemember,$SearchTime,$IsSuccessful\n";	
		}
	} else {
		echo "cannot get data from database. ".mysql_error();
	}
?>
