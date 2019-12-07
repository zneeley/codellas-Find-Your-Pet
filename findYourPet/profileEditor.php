<?php
//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $profileImgDir = $profileBio = "";
$imgExt_err = $imgSize_err = $bio_err = "";
$profileType =  $editType = $profileImgDir = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// set navbar variables
if ($_SESSION['accountType'] === "user") {
    $profileType = 'profileViewer.php';
    $editType = 'profileEditor.php';
    
    // Prepare a select statement
    $sql = "SELECT profileImage FROM users WHERE userID = ?";
    
} else {
    $profileType = 'profileShelterViewer.php';
    $editType = 'profileShelterEditor.php';
    
    // Prepare a select statement
    $sql = "SELECT profileImage FROM shelters WHERE shelterID = ?";
}

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_userID);
    
    // Set parameters
    $param_userID = $_SESSION['accountID'];
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $param_userImage);
        if(mysqli_stmt_fetch($stmt)){
            $profileImgDir = base64_decode($param_userImage);
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);
   
}

// Stop  nonshelter account from accessing this page
if($_SESSION["accountType"] != "user") {
    header("location: profileShelterEditor.php");
}

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
}

// Edit mode
if($_SERVER["REQUEST_METHOD"] == "POST") {
// Store information into database and upload image
    if(isset($_FILES['image']) && isset($_POST['bio'])) {
        if (empty($bio_err) && empty($imgExt_err) && empty($imgSize_err)) {
            // Prepare an update statement
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
                    header("location: profileViewer.php");
                }

            }

            // Close statement
            mysqli_stmt_close($stmt);

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
	<style type="text/css">
        body{
            background-image: url(images/user_edit.jpg);            
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
            font: 14px sans-serif;
        }
        h3{ font: sans-serif; }
        .container{
            height: 100%;
            align-content: center;
            margin-left: 10px;
           
        }

	</style>
</head>
    <body class=""> 
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <img class="navbar_pic" src="images/pawprint.jpg" alt="Your image">
          <a class="navbar-brand" href="#">F.Y.P</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-toggle" aria-controls="navbar-toggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbar-toggle">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
              <li class="nav-item active">
                <a class="nav-link" href="welcome.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Find Pets!</a>
              </li>
            </ul>
            
            <ul class="navbar-nav mt-2 mt-lg-0">
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo $_SESSION["username"] ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="<?php echo $profileType; ?>">View Profile</a>
                  <a class="dropdown-item" href="<?php echo $editType; ?>">Edit Profile</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
              </li>
            </ul>
              <img class="profile_pic" src="<?php echo $profileImgDir ?>" alt="Your image">
          </div>
        </nav>    
         <div class="container">
	       <div class="d-flex h-100">
            <div class="card m-5" style="height: 30rem; width: 25rem">
		<div class="card-header">
                    <h2>Edit Your Profile</h2>
                    <p>Please fill this form to edit your profile.</p>

		</div>
		<div class="card-body">		

            <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                        <h6>Change your profile picture</h6>
                        <input type="file" name="image" accept=".png,.jpg,.jpeg"/>
                        <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                        <span class="help-block"><?php echo $imgSize_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                        <h6>Edit your bio</h6>
                        <textarea rows="4" cols="50" name="bio"></textarea><br>
                        <span class="help-block"><?php echo $bio_err; ?></span>
            </div>
            <div class="form-group">

                    <input type="submit" class="btn btn-primary" value="Save">
            <a href="profileViewer.php" class="btn btn-warning">Cancel</a>
                </div>
            <div class="mb-2">
                <p>Want to change to password? <a href="passwordReset.php" class="btn btn-primary">Reset Password</a></p>
            </div>
            </form>  
                </div> 
        <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/><br>    
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
        </div>
             </div>
        </div>
        
    <!-- include jquery, popper.js, and bootstrap js -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    </body>
</html>