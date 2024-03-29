<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $profileImgDir = $profileBio = $profileAddress = $profilePhone = "";
$profileType =  $editType = $profileImgDir = $pets = $shelterName = "";

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
    $profileType = 'profileShelterViewer.php?id='.base64_encode($_SESSION['accountID']);
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
$sql = "SELECT profileImage, shelterBio, address, phoneNum, shelterName FROM shelters WHERE shelterID = ?";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_shelterID);

    // Set parameters
    $param_shelterID = base64_decode($_GET['id']);
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_store_result($stmt);
        
        mysqli_stmt_bind_result($stmt, $param_shelterImage, $param_shelterBio, $param_address, $param_phoneNum, $param_shelterName);
        if(mysqli_stmt_fetch($stmt)){
            $profileBio = $param_shelterBio;
            $profileImgDir = base64_decode($param_shelterImage);
            $profileAddress = $param_address;
            $profilePhone = $param_phoneNum;
            $shelterName = $param_shelterName;
            
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);
 
// Get the pets from the database for use with cards
$sql="SELECT * FROM pets";
$pets =  mysqli_query($link,$sql);

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
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
            font: 14px sans-serif;
        }
        h3{ font: sans-serif; }
        .container{
            height: 100%;
            align-content: center;
            margin-left: 5px;
           
        }
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            margin: auto;
            text-align: center;
            font-family: sans-serif;
        }

        .bio {
            color: grey;
            font-size: 16px;
        }

    </style>
</head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <img class="navbar_pic" src="images/navbarlogo.png" alt="Your image">
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
                    <label><?php echo htmlspecialchars($shelterName); ?>'s Profile.</label><br>
		</div>
		<div class="card-body">
             <img class="card-body" src="<?php echo $profileImgDir; ?>" alt="Your image" style="width:auto; height: 300px">
                    <p class="profile_text"><?php echo $profileBio; ?></p><br>
                    <p class="profile_text">Shelter Address: <?php echo $profileAddress; ?></p><br>
                    <p class="profile_text">Shelter Phone Number: <?php echo $profilePhone; ?></p><br>
                    
                   
           <div class="card-header"><label>Pets</label></div>
                    <div class ="row justify-content-center">
                    <?php foreach($pets as $pet){ if ($pet["shelterID"] == base64_decode($_GET['id'])) {?>
                    <div class="card m-4 col-xl-2" style="width: 18rem;">
                      <div class="card-body">
                        <img src="<?php echo base64_decode($pet["petImage"]); ?>" class="card-img-top" alt="Pet image">
                      </div>
                      <div class="card-footer">
                        <h5 class="card-title"><?php echo($pet["petName"]) ?></h5>
                        <p class="card-text"><?php echo($pet["breed"]) ?></p>
                        <p class="card-text"><?php echo($pet["gender"]) ?></p>
                        <a href="/petProfile.php?id=<?php echo base64_encode($pet["petID"]) ?>" class="btn btn-primary">View Profile</a>
                      </div>
                    </div>
                    <?php } } ?>

                    </div>
                </div>
		<div class="card-footer">
		    <a href="welcome.php" class="btn btn-primary">Home</a>
                    <?php if (base64_decode($_GET['id']) == $_SESSION['accountID']) { ?>
                    <a href="profileShelterEditor.php" class="btn btn-warning">Edit</a>
                    <a href="createProfilePet.php" class="btn btn-success">Add a Pet</a><br>
                    <?php } ?>
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
