<ul id="dropdown1" class="dropdown-content">
  <li><a href="about.html">About the Website</a></li>
  <li><a href="us.html"> About the Creators </a></li>
  <li class="help.html">Need Help?</li>
</ul>

<nav>
	<div class = "ok">
    <div class="nav-wrapper">
      <a href="edit.php" class="brand-logo">
      	<img src = "logo.png">
      </a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a class="dropdown-button" href="#!" data-activates="dropdown1">More<i class="material-icons right">arrow_drop_down</i></a></li>
      </ul>
    </div>
    </div>
  </nav>


<?php
		include 'calendar.php';
 
		$calendar = new Calendar();
 
		echo $calendar->show();
	    // pass in some info;
		require("common.php"); 
		
		if(empty($_SESSION['user'])) { 
  
			// If they are not, we redirect them to the login page. 
			$location = "http://" . $_SERVER['HTTP_HOST'] . "/login.php";
			echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$location.'">';
			//exit;
         
        	// Remember that this die statement is absolutely critical.  Without it, 
        	// people can view your members-only content without logging in. 
        	die("Redirecting to login.php"); 
    	} 

		// To access $_SESSION['user'] values put in an array, show user his username
		$arr = array_values($_SESSION['user']);
		echo "Welcome " .'@'. $arr[1] . '!';
		// open connection
		$connection = mysqli_connect($host, $username, $password) or die ("Unable to connect!");
		// select database
		mysqli_select_db($connection, $dbname) or die ("Unable to select database!");
		// create query
		$query = "SELECT * FROM DM";
       
		// execute query
		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
		// see if any rows were returned
		if (mysqli_num_rows($result) > 0) {
    		// print them one after another
    		echo "<table cellpadding=10 border=1>";
    		while($row = mysqli_fetch_row($result)) {
        		echo "<tr>";
				//echo "<td>".'@'.$row[0]."</td>";
        		echo "<td>" . $row[2]."</td>";
        		if ($row[2] !=""){
        			echo "<td>".'#'.$row[3]."</td>";
        		}else{
        			echo "<td>".$row[3]."</td>";
        		}
        		
				echo "<td><a href=".$_SERVER['PHP_SELF']."?id=".$row[0].">Delete</a></td>";
        		echo "</tr>";
    		}
		    echo "</table>";
		} else {
			
    		// print status message
    		echo "Tweet Something!";
		}
		// free result set memory
		mysqli_free_result($connection,$result);
		// set variable values to HTML form inputs
		$Tweet = $_POST['Tweet'];
    	$User = $_POST['User'];
    	$Hash = $_POST['Hashtag'];

    	if ($Hash != ""){
    		$query = "SELECT * FROM DM WHERE Hashtag LIKE '%Hash%'";
    		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
    		echo "<meta http-equiv='refresh' content='0'>";
    	}
		
		// check to see if user has entered anything
		if ($User != "") {
	 		// build SQL query
			$query = "INSERT INTO DM (Tweet, Hashtag) VALUES ('$Tweet', '$User')";
			// run the query
     		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
			// refresh the page to show new update
	 		echo "<meta http-equiv='refresh' content='0'>";
		}
		
		// if DELETE pressed, set an id, if id is set then delete it from DB
		if (isset($_GET['id'])) {
			// create query to delete record
			echo $_SERVER['PHP_SELF'];
    		$query = "DELETE FROM DM WHERE id = ".$_GET['id'];
			// run the query
     		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
			
			// reset the url to remove id $_GET variable
			$location = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$location.'">';
			exit;

		}

		// close connection
		mysqli_close($connection);
	?>
	<html>
		<head>
		<style>
			body{
				background-color: skyblue;
				/*background-image: url("thing.jpg");*/
			}
			.ok{
				background-color: darkblue;
			}
		</style>

	<ul id="slide-out" class="side-nav">
    <li><div class="userView">
      <div class="background">
        <img src="images/office.jpg">
      </div>
      <a href="#!user"><img class="circle" src="images/yuna.jpg"></a>
      <a href="#!name"><span class="white-text name">John Doe</span></a>
      <a href="#!email"><span class="white-text email">jdandturk@gmail.com</span></a>
    </div></li>
    <li><a href="#!"><i class="material-icons">cloud</i>First Link With Icon</a></li>
    <li><a href="#!">Second Link</a></li>
    <li><div class="divider"></div></li>
    <li><a class="subheader">Subheader</a></li>
    <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>
  </ul>
  <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>

    <div class="preloader-wrapper big active">
      <div class="spinner-layer spinner-blue">
        <div class="circle-clipper left">
          <div class="circle"></div>
        </div><div class="gap-patch">
          <div class="circle"></div>
        </div><div class="circle-clipper right">
          <div class="circle"></div>
        </div>
      </div>

      <div class="spinner-layer spinner-red">
        <div class="circle-clipper left">
          <div class="circle"></div>
        </div><div class="gap-patch">
          <div class="circle"></div>
        </div><div class="circle-clipper right">
          <div class="circle"></div>
        </div>
      </div>
<!--
      <div class="spinner-layer spinner-yellow">
        <div class="circle-clipper left">
          <div class="circle"></div>
        </div><div class="gap-patch">
          <div class="circle"></div>
        </div><div class="circle-clipper right">
          <div class="circle"></div>
        </div>
      </div>

      <div class="spinner-layer spinner-green">
        <div class="circle-clipper left">
          <div class="circle"></div>
        </div><div class="gap-patch">
          <div class="circle"></div>
        </div><div class="circle-clipper right">
          <div class="circle"></div>
        </div>
      </div>
      -->
    </div>

	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	
	<link href="calendar.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type = "text/javascrip"> $(".button-collapse").sideNav(); </script>
    
    <!-- This is the HTML form that appears in the browser -->

    <form class = "" role = "search" action = "<?=$_SERVER['PHP_SELF']?>" method = "post">
    	<div class = "">
    		<input type = "text" placeholder = "Inquire" name = "mysearch" id = "mysearch">
    	</div>
    	<button type = "submit">Search!</button>
    </form>

   	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    	Tweet: <input type="text" name="Tweet">
    	Hashtag: <input type="text" name="User">
    	<input type="submit" name="submit">
    </form>
    <form action="logout.php" method="post"><button>Log out</button></form>
    
	</body>
</html>