<?php 


    // First we execute our common code to connection to the database and start the session 
    require("common.php"); 
     
    // This if statement checks to determine whether the registration form has been submitted 
    // If it has, then the registration code is run, otherwise the form is displayed 
    if(!empty($_POST)) 
    { 
        // Ensure that the user has entered a non-empty username 
        if(empty($_POST['username'])) 
        { 
            // Note that die() is generally a terrible way of handling user errors 
            // like this.  It is much better to display the error with the form 
            // and allow the user to correct their mistake.  However, that is an 
            // exercise for you to implement yourself. 
            imp_alert("Please enter a username."); 
        } 
         
        // Ensure that the user has entered a non-empty password 
        if(empty($_POST['password'])) 
        { 
            $passerror = "wrong answer";
            echo "<script type='text/javascript'>alert('$passerror');</script>";
           
        } 
         
        // Make sure the user entered a valid E-Mail address 
        // filter_var is a useful PHP function for validating form input, see: 
        // http://us.php.net/manual/en/function.filter-var.php 
        // http://us.php.net/manual/en/filter.filters.php 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            echo("Invalid E-Mail Address"); 
        } 
         
        // We will use this SQL query to see whether the username entered by the 
        // user is already in use.  A SELECT query is used to retrieve data from the database. 
        // :username is a special token, we will substitute a real value in its place when 
        // we execute the query. 
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username 
        "; 
         
        // This contains the definitions for any special tokens that we place in 
        // our SQL query.  In this case, we are defining a value for the token 
        // :username.  It is possible to insert $_POST['username'] directly into 
        // your $query string; however doing so is very insecure and opens your 
        // code up to SQL injection exploits.  Using tokens prevents this. 
        // For more information on SQL injections, see Wikipedia: 
        // http://en.wikipedia.org/wiki/SQL_Injection 
        $query_params = array( 
            ':username' => $_POST['username'] 
        ); 
         
        try 
        { 
            // These two statements run the query against your database table. 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        // The fetch() method returns an array representing the "next" row from 
        // the selected results, or false if there are no more rows to fetch. 
        $row = $stmt->fetch(); 
         
        // If a row was returned, then we know a matching username was found in 
        // the database already and we should not allow the user to continue. 
        if($row) 
        { 
            die("This username is already in use"); 
        } 
         
        // Now we perform the same type of check for the email address, in order 
        // to ensure that it is unique. 
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        "; 
         
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row) 
        { 
            die("This email address is already registered"); 
        } 
         
        // An INSERT query is used to add new rows to a database table. 
        // Again, we are using special tokens (technically called parameters) to 
        // protect against SQL injection attacks. 
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email 
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email 
            ) 
        "; 
         
        // A salt is randomly generated here to protect again brute force attacks 
        // and rainbow table attacks.  The following statement generates a hex 
        // representation of an 8 byte salt.  Representing this in hex provides 
        // no additional security, but makes it easier for humans to read. 
        // For more information: 
        // http://en.wikipedia.org/wiki/Salt_%28cryptography%29 
        // http://en.wikipedia.org/wiki/Brute-force_attack 
        // http://en.wikipedia.org/wiki/Rainbow_table 
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
         
        // This hashes the password with the salt so that it can be stored securely 
        // in your database.  The output of this next statement is a 64 byte hex 
        // string representing the 32 byte sha256 hash of the password.  The original 
        // password cannot be recovered from the hash.  For more information: 
        // http://en.wikipedia.org/wiki/Cryptographic_hash_function 
        $password = hash('sha256', $_POST['password'] . $salt); 
         
        // Next we hash the hash value 65536 more times.  The purpose of this is to 
        // protect against brute force attacks.  Now an attacker must compute the hash 65537 
        // times for each guess they make against a password, whereas if the password 
        // were hashed only once the attacker would have been able to make 65537 different  
        // guesses in the same amount of time instead of only one. 
        for($round = 0; $round < 65536; $round++) 
        { 
            $password = hash('sha256', $password . $salt); 
        } 
         
        // Here we prepare our tokens for insertion into the SQL query.  We do not 
        // store the original password; only the hashed version of it.  We do store 
        // the salt (in its plaintext form; this is not a security risk). 
        $query_params = array( 
            ':username' => $_POST['username'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':email' => $_POST['email'] 
        ); 
         
        try 
        { 
            // Execute the query to create the user 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        // This redirects the user back to the login page after they register 
        header("Location: login.php"); 
         
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
        die("Redirecting to login.php"); 
    } 
     
?> 
<html>
<head>
<style>
    body{
        background-color: lightblue;
    }
/*button{
background-color: lightgrey; color: darkblue;;
font:2.4em Futura, ‘Century Gothic’, AppleGothic, sans-serif;
padding:14px;
/*background:url(overlay.png) repeat-x center #ffcc00;/*background-color:#09dae5;
border:1px solid #ffcc00;
-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;
border-bottom:1px solid #9f9f9f;
position: absolute; top: 45%; left: 45%;
/*-moz-box-shadow:inset 0 1px 0 rgba(255,255,255,0.5);-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,0.5);box-shadow:inset 0 1px 0 rgba(255,255,255,0.5); 
cursor:pointer;
}

button:hover{background-color: #ffd944;}

button:active{position: absolute; top: 45%; left: 45%;}

/* Full-width input fields */

input[type=text], input[type=password] {
    width: 90%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border-radius: 25px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.modal {
    border-radius: 20px;
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 3; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */

}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: lightblue;
    margin: auto;
    padding: 10px;
    border: 1px solid #888;
    width: 80%;
    height: 80%
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s

}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}

.modal-body {padding: 2px 16px;}

.modal-footer {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}.card1 {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    width: 350px;
    border-radius: 5px;
    position: absolute; left: 40px; top: 90px;
    background-color: white;
}.card1:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}.img1 {
    border-radius: 5px 5px 0 0;
}.container1 {
    padding: 2px 16px;
}.card2 {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    width: 350px;
    border-radius: 5px;
    position: absolute; left: 850px; top: 90px;
    background-color: white;
}

.card2:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}.img2 {
    border-radius: 5px 5px 0 0;
}
.container2 {
    padding: 2px 16px;
}.closebutton{
    position: absolute; top: 6px; left: 980px;
    /*border-radius: 5px;*/
    /*color: blue;*/
}h1{
    text-align: center;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 25px;
}h2{
    text-align: center; color: white;
    position: absolute; top: -30px; left: 440px;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 50px;

}p{
    text-align: center; color: white;
    position: absolute; top: 300px; left: 32%;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 15px;
}
h6{
    color: darkblue;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 15px;
}
.ca1{
    position: absolute; left: 800px; top: 100px;
}
.ca2{
    position: absolute; left: 900px; top: 300px;
}.cardting{
    border-radius: 25px;
}.ok{
    background-color: darkblue;
    width: 1230px;
    height: 70px;
    border-radius: 15px;
}.french{
    position: absolute; left: 1170px; top: 10px;
}.spanish{
    position: absolute; left: 1100px; top: 10px;
}.signup{
    position: absolute; left: 41.5%; top: 200px;
    padding: 14px;
    background-color: lightgrey;
    color: darkblue;
    border-radius: 10px;
    width: 200px;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 20px;
}
.signup:hover{
    background-color: gold;
}
.signup:active{
    position: absolute; left: 41.5%; top: 203px;
}
.logbutt{
    position: absolute; left: 41.5%; top: 280px;
    padding: 12px;
    background-color: lightgrey;
    color: darkblue;
    border-radius: 10px;
    width: 200px;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 20px;

}.anshpic{
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 20px;
    border-bottom-left-radius: 20px;
}.logo{
    position: absolute; left: 0px;
}
</style>
</head>
<body>
<!--
<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      Import materialize.css
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

Let browser know website is optimized for mobile
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


    <body>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type = "text/javascrip"> $(".button-collapse").sideNav(); </script>
-->
<nav>
    <div class = "ok">
    <div class="nav-wrapper">
    <img src = "logo.png" class = "logo">
    <a href = fregister.php><img src = "french.png" class = "french"></a>
    <a href = spegister.php><img src = "spanish.png" class = "spanish"></a>
    <h2>Shrimstagram</h2>
    </div>
    </div>
</nav>
<h1>Welcome to Shrimstagram!</h1>

<button onclick="document.getElementById('id01').style.display='block'" class = "signup" style="width:auto;">Register Here!</button>
<a href = "login.php"><button type = "button" class = "logbutt">Login Here!</button></a>
<!--
<div class = "ca1">
<img src = "stars.png">
<p>"It's clear that this website <i>BOSSES EVERYONE"</i><br>-some guy at Adidas</p>
</div>
<div class = "ca2">
<img src = "stars.png">
<p>"Just <i>LOG IN"</i><br>-Shia LaBouef</p>
</div>
<div class = "ca3">
<p>"It is the <i>GREATEST</i> website ever built"<br>-Donald Duck</p>
</div>
-->

  <!-- Modal content -->
  
<div id="id01" class="modal">

<form class="modal-content animate" action="login.php">
  <h6>
  <div class = "trybox">
    Name: <input type="text" name="name"  placeholder = "John Appleseed"> <br>
    E-mail: <input type="text" name="email" placeholder = "john.appleseed@shrimstagram.com"><br>
    Username: <input type="text" name="username" placeholder = "@jappleseed" value="" /> 
    Password: <input type="password" name="password" placeholder = "johnlovesshrimstagram" value="" />  
    <a href = "register.php" class = "closebutton"><!--<input type = "button" class = "closebutton"--><img src = "icon-close.png"></a>
    <a href = "login.php"><input type="submit" value="Sign Up!"/> </a>
    </div>
    </h6>
</div>
    </div>

<div class="card1">
  <img src="shrimad.png" alt = "Shrim could not be with us today" >
  <div class="container1">
    <h4><b>Shrimad Vora</b></h4> 
    <p3>Expert ask asking Kais for help<br> Relies on partners way too much<br> Needs to learn to get by on his own<br> Has many issues<br> Loves Instashrim tho</p3> 
  </div>
</div>


<div class="card2">
  <img src="anish.jpg" class = "anshpic" alt = "ansh could not be with us today">
  <div class="container2">
    <h4><b>Ansh Kuckreja</b></h4> 
    <p3>Expert NOT ask asking Kais for help<br> DOES NOT Relies on partners way too much<br> Needs to learn to get by on his own<br> Has many issues<br> Loves Instashrim tho</p3> 
  </div>
</div>
</form>

<script>
// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</html>

</html>