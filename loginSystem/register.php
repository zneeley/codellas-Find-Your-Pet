<?php
// Include config file
require_once "config.php";

// Code for reCaptcha
// Set reCaptcha Variables
define('SITE_KEY', '6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7');
define('SECRET_KEY', '6Lc7Cb0UAAAAAEYFNQkPzlrav9ZspKcNV4OxR3he');
$reCaptchaVal = "";

// Check the post and see if ask Google what value the user is getting from interacting with the site
if ($_POST) {
    
        // Get the json info from Google using the SECRET_KEY
        function getCaptcha($secretKey) {
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response={$secretKey}");
        $Return = json_decode($response);
        return $Return;
        
        }
    
    // Get the value
    $Return = getCaptcha($_POST['g-recaptacha-response']);
    
    // See if they are human if so change the $reCaptchaVal to human
    if ($return->sucess == true && $return->score > 0.5) {
        $reCaptchaVal = "human";
    } else {
        // Redirect bot to index
        header("location: index.php");
    }
}
 
// init variables
$username = $password = $confirm_password = $firstName = $lastName = $email = "";
$username_err = $password_err = $confirm_password_err = $firstName_err = $lastName_err = $email_err = "";
 
// Get and process the information sent from the form
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Validate First Name
    if(empty(trim($_POST["firstName"]))){
        $firstName_err = "Please enter your First Name.";     
    } else {
        $firstName = trim($_POST["firstName"]);
    }
    
    // Validate Last Name
    if(empty(trim($_POST["lastName"]))){
        $lastName_err = "Please enter your Last Name.";     
    } else {
        $lastName = trim($_POST["lastName"]);
    }
    
    // Validate email
    if(strpos(trim($_POST["email"]), '@') == True){
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set paramaters
            $param_email = base64_encode(trim($_POST["email"]));
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store results
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else {
                    $email = base64_encode(trim($_POST["email"]));
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
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
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
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($firstName_err) && empty($lastName_err) && $reCaptchaVal == "human"){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (userID, FirstName, LastName, email ,username, password) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_userID, $param_firstName, $param_lastName, $param_email, $param_username, $param_password);
            
            // Set parameters
            $param_userID = uniqid("UUID-");
            $param_firstName = $firstName;
            $param_lastName = $lastName;
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Auto Sign in the account if the account was created
                session_start();

                 // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = "";
                $_SESSION["username"] = $username;
                $_SESSION["userID"] = $param_userID;
                $_SESSION["accountType"] = "user";

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
    <title>Account Sign Up</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; ?>"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Account Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($firstName_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>">
                <span class="help-block"><?php echo $firstName_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($lastName_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>">
                <span class="help-block"><?php echo $lastName_err; ?></span>
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
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" /> <br>
        </form>
    </div>
    <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'login'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
    </script>    
</body>
</html>