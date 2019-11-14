<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $petID = $shelterID = $petType = $gender = $neutered = $vaccinationRecords = $petName = $petAge = $breed = $shelterIDCheck = "";
$imgExt_err = $imgSize_err = $bio_err = $gender_err = $petType_err = $petName_err = $neutered_err = $petAge_err = $breed_err = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Stop none shelter and shelter accounts the pet doesnt belong to from accessing this page
if($_SESSION["accountType"] != "shelter") {
    header("location: welcome.php");
} else {
    // Prepare a select statement
    $sql = "SELECT shelterID FROM pets WHERE petID = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_petID);
        
        // Set parameters
        $param_petID = base64_decode($_GET['id']);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $param_shelterID);
            if(mysqli_stmt_fetch($stmt)){
                $shelterIDCheck = $param_shelterID;

            }
        }
    }
    
    if ($shelterIDCheck != $_SESSION["accountID"]){
        header("location: welcome.php");
    }
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


?>

