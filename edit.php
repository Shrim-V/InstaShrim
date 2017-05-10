<html>
	<body>	

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
		echo "Welcome " . $arr[1] . "!";

		// open connection
		$connection = mysqli_connect($host, $username, $password) or die ("Unable to connect!");

		// select database
		mysqli_select_db($connection, $dbname) or die ("Unable to select database!");

		// create query
		$query = "SELECT * FROM symbols";
       
		// execute query
		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());

		echo "<br>";
		echo "<br>";
		echo "<br>";

		// see if any rows were returned
		if (mysqli_num_rows($result) > 0) {

    		// print them one after another
    		echo "<table cellpadding=10 border=1>";
    		while($row = mysqli_fetch_row($result)) {
        		echo "<tr>";
				echo "<td>".$row[0]."</td>";
        		echo "<td>" . $row[1]."</td>";
        		echo "<td>".$row[2]."</td>";
				echo "<td><a href=".$_SERVER['PHP_SELF']."?id=".$row[0].">Delete</a></td>";
        		echo "</tr>";
    		}
		    echo "</table>";

		} else {
			
    		// print status message
    		echo "No rows found!";
		}

		// free result set memory
		mysqli_free_result($connection,$result);

		// set variable values to HTML form inputs
		$country = $_POST['country'];
    	$animal = $_POST['animal'];
		
		// check to see if user has entered anything
		if ($animal != "") {
	 		// build SQL query
			$query = "INSERT INTO symbols (country, animal) VALUES ('$country', '$animal')";
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

	<style>
	.searchbar {
    width: 100px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-image: url('searchicon.png');
    background-position: 10px 10px; 
    background-repeat: no-repeat;
    padding: 10px 10px 10px 10px;
    -webkit-transition: width 0.4s ease-in-out;
    
	}

	input[type=text]:focus {
    width: 30%;

    }
	}
}

	</style>

	<title>W3.CSS Template</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {font-family: "Lato", sans-serif}
.mySlides {display: none}
</style>
<body>

<!-- Navbar -->
<div class="w3-top">
  <div class="w3-bar w3-black w3-card-2">
    <a class="w3-bar-item w3-button w3-padding-large w3-hide-medium w3-hide-large w3-right" href="javascript:void(0)" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>
    <a href="#" class="w3-bar-item w3-button w3-padding-large">HOME</a>
    <a href="#Login" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Login</a>
    <a href="#Logout" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Logout</a>
    <div class="w3-dropdown-hover w3-hide-small">
      <button class="w3-padding-large w3-button" title="More">MORE <i class="fa fa-caret-down"></i></button>     
      <div class="w3-dropdown-content w3-bar-block w3-card-4">
        <a href="#" class="w3-bar-item w3-button">About</a>
        <a href="#" class="w3-bar-item w3-button">FAQ</a>
        <a href="#" class="w3-bar-item w3-button">Extras</a>
      </div>
    </div>
  

  </div>
</div>


	<form action = "search.php?go" id = "searchform" method = "post">
	<br>

	Search : <input type = "text" class = "searchbar" name = "search" placeholder="Type Here!">
	<br>
	<input type = "submit" name = "submit">
	<br>
	</form>
    
    <!-- This is the HTML form that appears in the browser -->
   	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    	Tweet: <input type="text" name="country" class = "searchbar" placeholder="Tweet!">
    	Hashtag: <input type="text" name="animal" class="searchbar" placeholder="Type!">
    	<input type="submit" name="submit">
    </form>
    <form action="logout.php" method="post"><button>Log out</button></form>
    
	</body>
</html>