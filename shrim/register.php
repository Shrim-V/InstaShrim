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
            die("Please enter a username."); 
        } 


         
        // Ensure that the user has entered a non-empty password 
        if(empty($_POST['password'])) 
        { 
            die("Please enter a password."); 
        }  
        if ($_POST['password'] != $_POST['confirmpassword']){
            die("Passwords do not match.");
        }


        // Make sure the user entered a valid E-Mail address 
        // filter_var is a useful PHP function for validating form input, see: 
        // http://us.php.net/manual/en/function.filter-var.php 
        // http://us.php.net/manual/en/filter.filters.php 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            die("Invalid E-Mail Address"); 
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

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<h1>Register</h1>

<link rel="stylesheet" type="text/css" href="register.css">


<button onclick="document.getElementById('id01').style.display='block'" class = "signup" style="width:auto;">Register Here!</button>

<div class = "card3">
&nbsp&nbsp&nbsp&nbsp<p3>Already a member?</p3><br><br>
<a href="login.php"><button type = "button" class = "logbutt">Login Here!</button></a>
</div>




<div class="card1">
  <img src="shrimad.png" alt = "Shrim could not be with us today" >
  <div class="container1">
    <h4><b>Shrimad Vora</b></h4> 
    <p3>Hi! My nickname is Shrim and I cannot code for my life. I relies purely on StackOverflow and Google Search. I hope to learn how to use Github properly.</p3> 
  </div>
</div>


<div class="card2">
  <img src="anish.jpg" class = "anshpic" alt = "ansh could not be with us today">
  <div class="container2">
    <h4><b>Ansh Kuckreja</b></h4> 
    <p3>Hi, I'm the creator of this glorious website! It took a lot of blood, sweat, and tears, so I really, REALLY hope you like our website! I carried the team.</p3> 
  </div>
</div>
</form>


<form action="register.php" method="post"> 
<div id = "id01" class = "hov">
<h6>
    Username:<br /> 
    <input type="text" name="username" placeholder="@johnappleseed" /> 
    <br /><br /> 
    E-Mail:<br /> 
    <input type="text" name="email" placeholder="john.appleseed@shrimstagram.co" /> 
    <br /><br /> 
    Password:<br /> 
    <input type="password" name="password" placeholder="shrimstagramrocks" /> 
    <br /><br /> 
    Confirm Password:<br /> 
    <input type="password" name="confirmpassword" placeholder="shrimstagramrocks" /> 
    <br /><br /> 
    <input type="submit" value="Register" /> 
    <a href = "register.php"><button type = "button">Cancel</button></a>
    <a href = "register.php" class = "closebutt"><img src = "icon-close.png"></a>
</form>
</h6>

<!-- </form> -->
</div>
</form>
</div>

