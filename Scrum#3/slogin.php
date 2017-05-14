<?php 

    // First we execute our common code to connection to the database and start the session 
    require("common.php"); 
     
    // This variable will be used to re-display the user's username to them in the 
    // login form if they fail to enter the correct password.  It is initialized here 
    // to an empty value, which will be shown if the user has not submitted the form. 
    $submitted_username = ''; 
     
    // This if statement checks to determine whether the login form has been submitted 
    // If it has, then the login code is run, otherwise the form is displayed 
    if(!empty($_POST)) 
    { 
        // This query retreives the user's information from the database using 
        // their username. 
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                email 
            FROM users 
            WHERE 
                username = :username 
        "; 
         
        // The parameter values 
        $query_params = array( 
            ':username' => $_POST['username'] 
        ); 
         
        try 
        { 
            // Execute the query against the database 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        // This variable tells us whether the user has successfully logged in or not. 
        // We initialize it to false, assuming they have not. 
        // If we determine that they have entered the right details, then we switch it to true. 
        $login_ok = false; 
         
        // Retrieve the user data from the database.  If $row is false, then the username 
        // they entered is not registered. 
        $row = $stmt->fetch(); 
        if($row) 
        { 
            // Using the password submitted by the user and the salt stored in the database, 
            // we now check to see whether the passwords match by hashing the submitted password 
            // and comparing it to the hashed version already stored in the database. 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $check_password = hash('sha256', $check_password . $row['salt']); 
            } 
             
            if($check_password === $row['password']) 
            { 
                // If they do, then we flip this to true 
                $login_ok = true; 
            } 
        } 
         
        // If the user logged in successfully, then we send them to the private members-only page 
        // Otherwise, we display a login failed message and show the login form again 
        if($login_ok) 
        { 
            // Here I am preparing to store the $row array into the $_SESSION by 
            // removing the salt and password values from it.  Although $_SESSION is 
            // stored on the server-side, there is no reason to store sensitive values 
            // in it unless you have to.  Thus, it is best practice to remove these 
            // sensitive values first. 
            unset($row['salt']); 
            unset($row['password']); 
             
            // This stores the user's data into the session at the index 'user'. 
            // We will check this index on the private members-only page to determine whether 
            // or not the user is logged in.  We can also use it to retrieve 
            // the user's details. 
            $_SESSION['user'] = $row; 
             
            // Redirect the user to the private members-only page. 
            header("Location: edit.php"); 
            die("Redirecting to: edit.php"); 
        } 
        else 
        { 
            // Tell the user they failed 
            print("Login Failed."); 
             
            // Show them their username again so all they have to do is enter a new 
            // password.  The use of htmlentities prevents XSS attacks.  You should 
            // always use htmlentities on user submitted values before displaying them 
            // to any users (including the user that submitted them).  For more information: 
            // http://en.wikipedia.org/wiki/XSS_attack 
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8'); 
        } 
    } 
     
?> 
<html>
<style>
input[type=text], input[type=password] {
    width: 400px;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border-radius: 25px;
    border: 1px solid #ccc;
    box-sizing: border-box;

}
body{
    background-color: lightblue;
}.ok{
    background-color: darkblue;
    width: 1230px;
    height: 70px;
    border-radius: 15px;
}h2{
    text-align: center; color: white;
    position: absolute; top: -30px; left: 440px;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 50px;
}.french{
    position: absolute; left: 1170px; top: 10px;
}.eng{
    position: absolute; left: 1100px; top: 10px;
}.logo{
    position: absolute; left: 0px;
}h1{
    color: black;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 45px;
    position: absolute; top: 70px; left: 45%;
}p{
    color: white;
    font-family: "Lucida Console", Lucida, monospace;
    font-size: 15px;
}.box{
    background-color: darkblue;
    position: absolute; top: 280px; left: 405px;
    width: 400px;
    border-radius: 20px;
    padding: 20px;
}.sat1{
    background-color: darkblue;
    color: white;
    position: absolute; top: 120px; left: 35px;
    width: 300px;
    border-radius: 20px;
    padding: 20px;
    word-wrap: break-word;
}.sc1{
    border-radius: 30px;
}.sat2{
    background-color: darkblue;
    color: white;
    position: absolute; top: 420px; left: 35px;
    width: 300px;
    border-radius: 20px;
    padding: 20px;
    word-wrap: break-word;
}.sc2{
    border-radius: 30px;
}.sat3{
    background-color: darkblue;
    color: white;
    position: absolute; top: 420px; left: 875px;
    width: 300px;
    border-radius: 20px;
    padding: 20px;
    word-wrap: break-word;
}.sc3{
    border-radius: 30px;
}.sat4{
    background-color: darkblue;
    color: white;
    position: absolute; top: 120px; left: 875px;
    width: 300px;
    border-radius: 20px;
    padding: 20px;
    word-wrap: break-word;
}.sc4{
    border-radius: 30px;
}
</style>
<body>
<!--
<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
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
    <a href = flogin.php><img src = "french.png" class = "french"></a>
    <a href = login.php><img src = "eng.png" class = "eng"></a>
    <h2>Shrimstagram</h2>
    </div>
    </div>
</nav>

<div class = "sat1">
"Mi cosa favorita de Shrimstagram es que no funciona en ningún otro servidor o PC!"<br>
<img src = "sc.png" class = "sc1">
-Cliente Satisfecho
</div>

<div class = "sat2">
"Mi cosa favorita de Shrimstagram es que no funciona en ningún otro servidor o PC!"
<br>-Cliente Satisfecho
<img src = "sc1.png" class = "sc2">
</div>

<div class = "sat3">
"Mi cosa favorita de Shrimstagram es que no funciona en ningún otro servidor o PC!"
<br>-Cliente Satisfecho
<img src = "sc2.png" class = "sc3" alt = "idk">
</div>

<div class = "sat4">
"Mi cosa favorita de Shrimstagram es que no funciona en ningún otro servidor o PC!"
<br>-Cliente Satisfecho
<img src = "sc3.png" class = "sc4" alt = "idk">
</div>



<h1>Login</h1>
<div class = "box">
<form action="edit.php" method="post"> 
    <p>Username:</p><input type="text" name="username" placeholder = "@jappleseed"value="<?php echo $submitted_username; ?>" /> 
    <p>Password:</p>
    <input type="password" name="password" placeholder="johnlovesshrimstagram"> 
    <br>
    <input type="submit" value="Login"> 
</form> 
</div>
<a href="register.php">Register</a>