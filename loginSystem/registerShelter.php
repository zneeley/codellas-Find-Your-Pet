<?php
// Include config file
require_once "config.php";
// Code for reCaptcha
// Set reCaptcha Variables
$reCaptchaVal = "";
// Check the post and see if ask Google what value the user is getting from interacting with the site
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Lc7Cb0UAAAAAEYFNQkPzlrav9ZspKcNV4OxR3he';
    $recaptcha_response = $_POST["recaptcha_response"];
    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);
    if($recaptcha->success==true){
    // Take action based on the score returned:
        if ($recaptcha->score >= 0.5) {
            $reCaptchaVal = "human";
        } else {
            // Redirect bot to index
            header("location: index.php");
        }
    }
}
 
// init variables
$username = $password = $confirm_password = $email = $shelterName = "" ;
$username_err = $password_err = $confirm_password_err = $email_err = $shelterName_err = "";
 
// Get and process the information sent from the form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Shelter Names
    if(empty(trim($_POST["shelterName"]))){
        $shelterName_err = "Please enter the shelders name.";     
    } else {
        $shelterName = trim($_POST["shelterName"]);
    }
    
    // Validate email
    if(strpos(trim($_POST["email"]), '@') == True){
        // Prepare a select statement
        $sql = "SELECT id 
        FROM users 
        WHERE email = ?
        UNION
        SELECT id 
        FROM shelters 
        WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_email);
            
            // Set paramaters
            $param_email = base64_encode(trim($_POST["email"]));
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store results
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                    $emailEncoded = base64_encode(trim($_POST["email"]));
                }
            } else{
                echo "Oh No! Something went wrong. Please try again later.";
            }
        }
    } else {
        $email_err = "Please enter a valid email address ex: name@website.com";
    }
    
    // Validate username
    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "
        SELECT id 
        FROM shelters 
        WHERE username = ?
        UNION
        SELECT id 
        FROM users 
        WHERE username = ?
        ";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store results
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oh No! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($shelterName_err) && empty($email_err) && $reCaptchaVal == "human"){
        
        // Prepare an insert statement
        $sql = "INSERT INTO shelters (shelterID, shelterName, email, username, password) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_shelterID, $param_shelterName, $param_email, $param_username, $param_password);
            
            // Set parameters
            $param_shelterID = uniqid("USID-");
            $param_shelterName = $shelterName;
            $param_email = $emailEncoded;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password is correct, so start a new session
                session_start();
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = "";
                $_SESSION["username"] = $username;
                $_SESSION["accountID"] = $param_shelterID;
                $_SESSION["accountType"] = "shelter";
                $_SESSION["accountHolderName"] = $shelterName;
                
                // Redirect user to welcome page
                header("location: welcome.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shelter Sign Up</title>
    
     <!-- include bootstrap --> 
    
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
	<!--Bootsrap 4 CDN-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <!--Fontawesome CDN-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

        <link rel="stylesheet" href="layout.php">
        
    <style type="text/css">
        
        body{
            background-image: url("images/register_shelter.jpg");            
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
            font: 14px sans-serif;
        }
        h3{ font: sans-serif; }

        .wrapper{ width: 350px; padding: 20px; }
    </style>

    <script src="https://www.google.com/recaptcha/api.js?render=6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7"></script>
</head>
<body>
    
    <div class="container">
	<div class="d-flex h-100">
		<div class="card">
			<div class="card-header">
			 <h2>Shelter Sign Up</h2>
                <p>Please fill this form to create an account.</p>
			
			</div>
			<div class="card-body">
				
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($shelterName_err)) ? 'has-error' : ''; ?>">
                <label>Shelter Name</label>
                <input type="text" name="shelterName" class="form-control" value="<?php echo $shelterName; ?>">
                <span class="help-block"><?php echo $shelterName_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
            <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/><br>
        </form>
	</div>		
	</div>
    </div>
</div>
  <footer class="mastfoot mt-auto">
    <div class="inner">
      <p>@2019 Find your Pet</p>
    </div>
  </footer>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7', { action: 'register' })
                .then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                console.log(recaptchaResponse);
                recaptchaResponse.value = token;
            });
        });
    </script>  
</body>
</html>
