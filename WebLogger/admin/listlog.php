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
?>
<html>
<header>
	<title>list logs</title>
	<style type="text/css">
		#header,#successheader {
			color:#7777FF;
			font-weight:bold;
		}

		table{
			border: 1px solid black;
			color:black;
			width:100%;
		}
	
		th{ 
			background-color:#333333;
			color:white;
		}

		tr{
			border-top:
		}
		td{ 
			/*padding: 0px 15px 0px 15px; */
			min-width:2em;
			text-align:center;
			border: 1px solid black;
		}
		/*tr:nth-child(odd){ background-color:#FFFFFF; }
		tr:nth-child(even){ background-color:#DDDDFF; }*/
	</style>
</header>
<body>
	<h1 id="header">List of Participants</h1>
	<div id="data">
<?php
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
	$TABLEHEAD= <<<TABLEHEAD
		<table>
		<thead>
			<tr>
			<th>#</th>
			<th>query id</th>
			<th>query time</th>
			<th>site</th>
			<th>query</th>
			<th>Goal?</th>
			<th>Trying to Remember?</th>
			<th>Search Time?</th>
			<th>Success?</th>
			<th>click id</th>
			<th>click time</th>
			<th>page</th>
			<th>index</th>
			<th>title</th>
			<th>button</th>
			</tr>
		</thead>
		<tbody>
TABLEHEAD;

	$TABLETAIL= <<<TABLETAIL
		</tbody>
		</table>
TABLETAIL;


	if ($result){
		
		$current_qid=-1; //set default value, no qid=1 ever

		$current_date=""; //for grouping purpose

		//clear all grouping var
		$grp_qid=$grp_qtime=$grp_site=$grp_query=$grp_cid=$grp_ctime=$grp_page=$grp_index=$grp_title=$grp_url="";
		$grp_SearchGoal=$grp_IsTryRemember=$grp_SearchTime=$grp_IsSuccessful=""; //questionaires
		$grp_count=0;		//count number of clicks in the query.
		$table_html="";		//storage for generatin html table.
		$query_number=1;	//count number of queries
		
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

			//for first time , print table head
			if($current_qid== -1){
				echo "<br><br><center><h2>Date: ".substr($qtime,0,10)."</h2></center><br>";
				echo $TABLEHEAD;
			}


			//if query changes, dump result into table
			if($current_qid!=$qid ){
				
				if($current_qid!= -1){
					$table_html = "\t\t\t<tr>\n".
								"<td rowspan=$grp_count>$query_number</td>\n".
								"<td rowspan=$grp_count>$grp_qid</td>\n".
								"<td rowspan=$grp_count>$grp_qtime</td>\n".
								"<td rowspan=$grp_count>$grp_site</td>\n".
								"<td rowspan=$grp_count>$grp_query</td>\n".
								"<td rowspan=$grp_count>$grp_SearchGoal</td>\n".
								"<td rowspan=$grp_count>$grp_IsTryRemember</td>\n".
								"<td rowspan=$grp_count>$grp_SearchTime</td>\n".
								"<td rowspan=$grp_count>$grp_IsSuccessful</td>\n".
								$table_html;
					echo $table_html;
					$query_number=$query_number+1;
				
					//if different day, new table
					if($current_date!=substr($qtime,0,10)){
						$query_number = 1;
						echo $TABLETAIL;
						echo "<br><br><center><h2>Date: ".substr($qtime,0,10)."</h2></center><br>";
						echo $TABLEHEAD;
					}

				}	

				$current_qid = $qid;
				$current_date = substr($qtime,0,10);	//get date from qtime

				//initialize grouping values
				$table_html="";
				$grp_count=0;

				//set query's value (group's value)
				$grp_qid=$qid;
				$grp_qtime=$qtime;
				$grp_site=$site;
				$grp_query=$query;
				
				//set group's value for questionaire
				$grp_SearchGoal = ($SearchGoal==null)?"N/A":$SearchGoal;
				$grp_IsTryRemember = ($IsTryRemember==null)?"N/A":$IsTryRemember;
				$grp_SearchTime = ($SearchTime==null)?"N/A":$SearchTime;
				$grp_IsSuccessful = ($IsSuccessful==null)?"N/A":$IsSuccessful;
				
				//set click's value (individual's value)
				$table_html = $table_html.
						"<td>$cid</td>".
						"<td>$ctime</td>".
						"<td>$page</td>".
						"<td>$index</td>".
						"<td><a href='$url'>$title</a></td>".
						"<td>$button</td>".
						"</tr>\n";
								
			} else {		
					//set click result (inidividual result)
					$table_html = $table_html.
						"\t\t\t<tr>\t".
						"<td>$cid</td>".
						"<td>$ctime</td>".
						"<td>$page</td>".
						"<td>$index</td>".
						"<td><a href='$url'>$title</a></td>".
						"<td>$button</td>".
						"</tr>\n";
			}
			$grp_count=$grp_count+1;
		}	

		//dump the last query (only there is atleast one result)
		if($result) {
			//dump result
			$table_html = "\t\t\t<tr>\n".
				"<td rowspan=$grp_count>$query_number</td>\n".
				"<td rowspan=$grp_count>$grp_qid</td>\n".
				"<td rowspan=$grp_count>$grp_qtime</td>\n".
				"<td rowspan=$grp_count>$grp_site</td>\n".
				"<td rowspan=$grp_count>$grp_query</td>\n".
				"<td rowspan=$grp_count>$grp_SearchGoal</td>\n".
				"<td rowspan=$grp_count>$grp_IsTryRemember</td>\n".
				"<td rowspan=$grp_count>$grp_SearchTime</td>\n".
				"<td rowspan=$grp_count>$grp_IsSuccessful</td>\n".
				$table_html;
			echo $table_html;
		}
		echo $TABLETAIL;
		mysql_free_result($result);
	} else {
		echo "Cannot perform sql query.".mysql_error();
	}
?>	
	</div>
</body>
</html>
