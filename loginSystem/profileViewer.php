<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $profileImgDir = $profileBio = "";
$imgExt_err = $imgSize_err = $bio_err = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
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

// Prepare a select statement
$sql = "SELECT profileImage, userBio FROM users WHERE userID = ?";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_userID);

    // Set parameters
    $param_userID = $_SESSION['accountID'];
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $param_userImage, $param_userBio);
        if(mysqli_stmt_fetch($stmt)){
            $profileBio = $param_userBio;
            $profileImgDir = base64_decode($param_userImage);
            
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);
        
}

// Edit mode
if($_SERVER["REQUEST_METHOD"] == "POST") {
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
        
        // If file passes checks push delete old image
        if(file_exists('uploadContent/userImages/'.$_SESSION['accountID'].'.png')) {
            // Delete png file
            unlink('uploadContent/userImages/'.$_SESSION['accountID'].'.png');
        }
        
        if(file_exists('uploadContent/userImages/'.$_SESSION['accountID'].'.jpg')) {
            // Delete jpg file
            unlink('uploadContent/userImages/'.$_SESSION['accountID'].'.jpg');
        }
        
        if(file_exists('uploadContent/userImages/'.$_SESSION['accountID'].'.jpeg')) {
            // Delete jpeg file
            unlink('uploadContent/userImages/'.$_SESSION['accountID'].'.jpeg');
        }
        
        // Upload File
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
    
// Close connection
mysqli_close($link);    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    
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
</head>
    <body>
        
        <div id="normal">
			<div class="card w-auto">
			  <div class="card-header">
				  <label><?php echo htmlspecialchars($_SESSION["accountHolderName"]); ?>'s Profile.</label><br>
			  </div>
			  <div class="card-body">
				  <img class="profile_pic" src="<?php echo $profileImgDir; ?>" alt="Your image">
				  <p class="profile_text"><?php echo $profileBio; ?></p>
			  </div>
			  <div class="card-footer">
			      <a href="welcome.php" class="btn btn-primary">Home</a>
			      <a href="profileEditor.php" class="btn btn-warning">Edit</a><br>
			  </div>
			</div>
        </div>
        
        <div id="editer" style="display:none;s">
            <label><?php echo htmlspecialchars($_SESSION["accountHolderName"]); ?>'s Profile Creation.</label><br>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                    <label>Upload a Profile Picture:</label><br>
                    <input type="file" name="image" />
                    <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                    <span class="help-block"><?php echo $imgSize_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                    <br><label>Your Bio:</label><br>
                    <textarea rows="4" cols="50" name="bio"></textarea><br>
                    <span class="help-block"><?php echo $bio_err; ?></span>
                </div>
                <input type="submit" class="btn btn-success" value="Save">
                <a href="profileViewer.php" class="btn btn-warning">Cancel</a>
                <input type="hidden" value="" name="enableEdit" id="enableEdit"/><br>
            </form>    
        </div> 
    <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/><br>    
    <script>
        // Edit mode
        $("#toggle-button").on("click", function(){
            $("#editer").show();
            $("#normal").hide();
            $("#enableEdit").val('Y');
        });
        
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