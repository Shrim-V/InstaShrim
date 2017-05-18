<html>
	<body>	

	<link rel="stylesheet" type="text/css" href="edit.css">


<ul id="dropdown1" class="dropdown-content">
  <li><a href="about.php" class = "about">About</a></li>
  <li><a href="help.html" class = "about">Help</a></li>
  <li><a target = "_blank" href ="https://github.com/Shrim-V/InstaShrim" class = "about">Github</a></li>
  <li class="divider"></li>
</ul>
<nav class = "ok"
    <div class="nav-wrapper">
    <a href = "edit.php"><img src = "logo.png" class = "logo"></a>
    <ul class="right hide-on-med-and-down">
  
    <h2><b>Shrimstagram</b></h2>
    </div>

       <li><div class = "dropdown"><a class="dropdown-button" data-activates="dropdown1">More<i class="material-icons right">arrow_drop_down</i></div></a></li>
    </ul>
  </div>
    </div>
    </nav>
<br><br><br><br>
    
	<?php
	
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
		echo "Welcome " . $arr[1];

		// open connection
		$connection = mysqli_connect($host, $username, $password) or die ("Unable to connect!");

		// select database
		mysqli_select_db($connection, $dbname) or die ("Unable to select database!");


		if(isset($_POST['search'])){
			$searchq = $_POST['search'];
			$query = "SELECT * FROM symbols WHERE Tweet LIKE '%".$searchq."%' OR Hashtag LIKE '%".$searchq."%'";
}



		// execute query
		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());

		// see if any rows were returned
		if (mysqli_num_rows($result) > 0) {

    		// print them one after another
    		echo "<table cellpadding=10 border=1>";
    		while($row = mysqli_fetch_row($result)) {
        		/*echo "<tr>";
				echo "<td>".$row[0]."</td>";
        		echo "<td>" . $row[1]."</td>";
        		echo "<td>".$row[2]."</td>";
				echo "<td><a href=".$_SERVER['PHP_SELF']."?id=".$row[0].">Delete</a></td>";
        		echo "</tr>";*/
        		echo 
        		' 
       <div class="row">
        <div class="col s12 m6">
          <div class="card blue darken-1">
            <div class="card-content white-text">
              <span class="card-title">' .'@'.  $row[4] . " says: ". $row[1] .'</span>
            </div>
            <div class="card-action">
            <p>'. '#'.$row[2].'</p>
            <p>'. $row[5].'</p>
            </div>
            </div>
          </div>
        </div>
      </div>';



    		}
		  

		} else {
			
    		// print status message
    		echo "No rows found!";
		}

		// free result set memory
		mysqli_free_result($connection,$result);

		// set variable values to HTML form inputs
		$Tweet = $_POST['Tweet'];
    	$Hashtag = $_POST['Hashtag'];
		
		// check to see if user has entered anything
		if ($Hashtag != "") {
	 		// build SQL query
			$query = "INSERT INTO symbols (Tweet, Hashtag) VALUES ('$Tweet', '$Hashtag')";
			// run the query
     		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
			// refresh the page to show new update
	 		echo "<meta http-equiv='refresh' content='0'>";
		}
		
		// if DELETE pressed, set an id, if id is set then delete it from DB
		if (isset($_GET['id'])) {

			// create query to delete record
			echo $_SERVER['PHP_SELF'];
    		$query = "DELETE FROM symbols WHERE id = ".$_GET['id'];

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

	<head>
      <!--Import Google Icon Font-->
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>

    <body>
      <!--Import jQuery before materialize.js-->
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="js/materialize.min.js"></script>
    </body>

	
    <div class = "searchbox">
    <!-- This is the HTML form that appears in the browser -->
   	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    	Tweet: <input type="text" name="Tweet">
    	Hashtag: <input type="text" name="Hashtag">
    	<input type="submit" name="submit">
    </form>
    <form action="logout.php" method="post"><button>Log out</button></form>
    </div>
	</body>
</html>