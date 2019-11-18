<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $accountBio = $petID = $shelterID = $petType = $gender = $neutered = $vaccinationRecords = $petName = $petAge = $breed = "";
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
    
// Close connection
mysqli_close($link);    
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

// Prepare a select statement
$sql = "SELECT shelterID, petType, petName, breed, gender, age, neutered, vaccinationRecords, petImage, bio FROM pets WHERE petID= ?";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_petID);

    // Set parameters
    $param_petID = base64_decode($_GET['id']);

    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $param_shelterID, $param_petType, $param_petName, $param_breed, $param_gender, $param_age, $param_neutered, $param_vaccinationRecords, $param_petImage, $param_bio);
        if(mysqli_stmt_fetch($stmt)){
            $shelterID = $param_shelterID;
            $petType = $param_petType;
            $petName = $param_petName;
            $breed = $param_breed;
            $gender = $param_gender;
            $petAge = $param_age;
            $neutered = $param_neutered;
            $vaccinationRecords = $param_vaccinationRecords;
            $petImage = base64_decode($param_petImage);
            $accountBio = $param_bio;
            
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);
    
// Close connection
mysqli_close($link);    
}

if ($shelterID === $_SESSION['accountID']) {
    $editButton = "<a href=\"petProfileEditor.php?id=".$_GET['id']."\" class\"=btn btn-warning\">Edit</a><br>";
} else {
    $editButton = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $petName; ?> Profile</title>
    
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
        
        <div id="normal">
            <div class="card w-auto">
                <div class="card-header">
                    <label><?php echo htmlspecialchars($petName); ?>'s Profile.</label><br>
		</div>
		<div class="card-body">
		    <img class="profile_pic" src="<?php echo $petImage; ?>" alt="Pet image">
                    <p class="profile_text"><?php echo $accountBio; ?></p><br><br><br>
                    <label>Type: </label>
                    <p class="profile_text"><?php echo $petType; ?></p><br>
                    <label>Breed: </label>
                    <p class="profile_text"><?php echo $breed; ?></p><br>
                    <label>Gender: </label>
                    <p class="profile_text"><?php echo $gender; ?></p><br>
                    <label>Age: </label>
                    <p class="profile_text"><?php echo $petAge; ?></p><br>
                    <label>Neutered: </label>
                    <p class="profile_text"><?php echo $neutered; ?></p><br>
                    <label>Vaccination Records: ADD PROPER INFORMATION WHEN ITS DONE BY JOSH</label>
                    <p class="profile_text"><?php echo $vaccinationRecords; ?></p><br>
                    
                </div>
		<div class="card-footer">
		    <?php echo $editButton; ?><br>
		</div>
            </div>
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
    <!-- include jquery, popper.js, and bootstrap js -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    </body>
</html>

