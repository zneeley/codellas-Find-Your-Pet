<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = "";
$imgExt_err = $imgSize_err = $bio_err = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Stop  nonshelter account from accessing this page
if($_SESSION["accountType"] != "user") {
    header("location: createProfileShelter.php");
}


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
            header("location: logout.php");
        }
    }
}

// Upload image system 
if(isset($_FILES['image'])){
    // Get images data
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileTmp = $_FILES['image']['tmp_name'];
    $fileType = $_FILES['image']['type'];
    $fileExtTemp = explode('.',$_FILES['image']['name']);
    $fileExt = strtolower(end($fileExtTemp));
    
    // List of allowed extensins
    $extensions = array("jpeg","jpg","png");
      
    // Check to see if the files extenion is allowed
    if(in_array($fileExt,$extensions)=== false){
        $imgExt_err = "File not allowed, please choose a JPEG or PNG file.";
    }
      
    // Check to see if the size is under 5mb
    if($fileSize > 5000000){
        $imgSize_err = 'File size must be smaller than 5 MB';
    }
      
    // If file passes checks push 
    $fileNameNew = $_SESSION['accountID'].".".$fileExt;
    $fileDir = "uploadContent/userImages/".$fileNameNew;
    move_uploaded_file($fileTmp,"uploadContent/userImages/".$fileNameNew);
}

// Check to see if textarea is empty
if(isset($_POST['bio'])){
   // Check to see if the bio is empty
    if (!strlen(trim($_POST['bio']))){
        $bio_err = "Please type a Bio.";
    } else {
        $accountBio = $_POST['bio'];
    }
}

// Store information into database and upload image
if(isset($_FILES['image']) && isset($_POST['bio'])) {
    if (empty($bio_err) && empty($imgExt_err) && empty($imgSize_err)) {
        // Prepare an insert statement
        $sql = "UPDATE users SET profileImage = ?, userBio = ? WHERE userID = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_profileImage, $param_userBio, $param_userID);

            // Set parameters
            $param_profileImage = base64_encode($fileDir);
            $param_userBio = $accountBio;
            $param_userID = $_SESSION['accountID'];

            if(mysqli_stmt_execute($stmt)){
                // Redirect user to welcome page
                header("location: welcome.php");
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
    <title>Profile Creation</title>
    
<!-- include bootstrap --> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--Bootsrap 4 CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<!--Fontawesome CDN-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="layout.php">
    <script src="https://www.google.com/recaptcha/api.js?render=6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7"></script>

    <style type="text/css">
        body{
            background-image: url(images/create.jpeg);            
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
        }

	</style>
</head>
    <body>
	<div class="card m-5 ">
        <h1 class="text-center card-header">Create Your Profile!<br></h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group m-5 <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                <h4>Choose a Profile Pic!</h4><br>
                <input type="file" name="image" />
                <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                <span class="help-block"><?php echo $imgSize_err; ?></span>
            </div>
            
            <div class="m-5 form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                <br><h5>Create Your Bio!</h5><br>
                <textarea class="form-control" rows="4" cols="50" name="bio"></textarea><br>
                <span class="help-block"><?php echo $bio_err; ?></span>
            </div>
            <div class="m-5">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
                <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/><br>
            </div>
	</form>
	</div>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7', { action: 'profile' })
                .then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                console.log(recaptchaResponse);
                recaptchaResponse.value = token;
            });
        });
    </script> 
    </body>
</html>

