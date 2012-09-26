<?php 
	session_start();
	require 'config.php';
	
	if($_POST['submit']){
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if( $username == $CONFIG['admin_user'] &&
			$password == $CONFIG['admin_pass']){
			$_SESSION['adminlogin']=true;
		}
	}
	
	if(!isset($_SESSION['adminlogin'])):
?>
<html>
<head>
<title>UCI Web Search Logger Administration</title>
</head>
<body>
	<h1>UCI Web Search Logger</h1>
	<div>
		Please login<br>
		<form name="adminlogin" id="adminlogin" method="post" action="" >
			<table>
				<tr>
					<td>User Name</td>
					<td><input name="username" id="username" type="text"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input name="password" id="password" type="password"></td>
				</tr>
			</table>
			<input name="submit" type="submit" value="Submit">
		</form>
	</div>
</body>
</html>
<?php 
		//end if no admin session
	else: 	
		//if has admin session show the following
?>
<html>
<head>
<title>UCI Web Search Logger Administration</title>
</head>
<script type="text/javascript">
	function viewlog(){
		var userid = document.getElementById("userid").value;
		document.location = "listlog.php?ParticipantID="+userid;
		return false;
	}

	function downloadlog(){
		var userid = document.getElementById("userid").value;
		document.location = "downloadlog.php?ParticipantID="+userid;
		return false;
	}
</script>
<body>
	<h1>UCI Web Search Logger</h1>
	<div>
		<h2>Select an operation from the list below: </h2>
		<ul>
			<li><a href="userlist.php">List all users</a></li>
			<li><a href="createuser.php">Create a new user</a></li>
			with user id = <input id="userid" type="text" value=""> do the followings:
			<li><a href="#" onclick="viewlog();return false;">View log of this user</a></li>
			<li><a href="#" onclick="downloadlog();return false;">Download log of this user</a></li>
		</ul>
	</div>
</body>
</html>
<?php 
	endif;
?>