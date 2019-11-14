<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $petID = $shelterID = $petType = $gender = $neutered = $vaccinationRecords = $petName = $petAge = $breed = "";
$imgExt_err = $imgSize_err = $bio_err = $gender_err = $petType_err = $petName_err = $neutered_err = $petAge_err = $breed_err = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Stop  nonshelter account from accessing this page
if($_SESSION["accountType"] != "shelter") {
    header("location: welcome.php");
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

// Get and process the information sent from the form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Generate petID
    $petID = uniqid("UPID-");
    
    // Check to see if pet type is empty
    if(isset($_POST['petType'])){
       // Check to see if the bio is empty
        if (!strlen(trim($_POST['petType']))){
            $petType_err = "Please click on the pet type.";
        } else {
            $petType = $_POST['petType'];
        }
    }

    // Check to see if pet gender is empty
    if(isset($_POST['gender'])){
       // Check to see if the gender is empty
        if (!strlen(trim($_POST['gender']))){
            $gender_err = "Please the pets gender.";
        } else {
            $gender = $_POST['gender'];
        }
    }
    
    // Check to see if pet breed is empty
    if(isset($_POST['breed'])){
       // Check to see if the breed is empty
        if (!strlen(trim($_POST['breed']))){
            $breed_err = "Please type a the breed.";
        } else {
            $breed = $_POST['breed'];
        }
    }
    
    // Check to see if pet breed is empty
    if(isset($_POST['petName'])){
       // Check to see if the breed is empty
        if (!strlen(trim($_POST['petName']))){
            $petName_err = "Please type a name.";
        } else {
            $petName = $_POST['petName'];
        }
    }
    
    // Check to see if pet age is empty
    if(isset($_POST['age'])){
       // Check to see if the age is empty
        if (!strlen(trim($_POST['age']))){
            $petAge_err = "Please select an age.";
        } else {
            $petAge = $_POST['age'];
        }
    }
    
    // Check to see if pet neutered is empty
    if(isset($_POST['neutered'])){
       // Check to see if the neutered is empty
        if (!strlen(trim($_POST['neutered']))){
            $neutered_err = "Please select if the pet is neutered.";
        } else {
            $neutered = $_POST['neutered'];
        }
    }
    
    // Get Vaccination Records
    $vaccinationRecords = "REPLACE WITH REAL VALUE"; //$_POST['vacRecords'];
    
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
        $fileNameNew = $petID.".".$fileExt;
        $fileDir = "uploadContent/petImages/".$fileNameNew;
        move_uploaded_file($fileTmp,"uploadContent/petImages/".$fileNameNew);
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
     
    // Check input errors before inserting in database
    if(empty($bio_err) && empty($breed_err) && empty($gender_err) && empty($petAge_err) && empty($petType_err) && empty($imgSize_err) && empty($imgExt_err) && empty($neutered_err) 
            && empty($petName_err) && $reCaptchaVal == "human"){
        
        // Prepare an insert statement
        $sql = "INSERT INTO pets (petID, shelterID, petType, petName, breed, gender, age, neutered, vaccinationRecords, petImage, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssssssss", $param_petID, $param_shelterID, $param_petType, $param_petName, $param_breed, $param_gender, $param_age, $param_neutered, 
                    $param_vaccinationRecords, $param_petImage, $param_bio);
            
            // Set parameters
            $param_petID = $petID;
            $param_shelterID = $_SESSION["accountID"];
            $param_petType = $petType;
            $param_petName = $petName;
            $param_breed = $breed;
            $param_gender = $gender;
            $param_age = $petAge;
            $param_neutered = $neutered;
            $param_vaccinationRecords = $vaccinationRecords;
            $param_petImage = base64_encode($fileDir);
            $param_bio = $accountBio;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect user to welcome page
                header("location: profileShelterViewer.php");
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

</head>
    <body>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($petType_err)) ? 'has-error' : ''; ?>">
                <Label>TEMP VALUE add accepted types of pets used to other values also. Maybe use a dropdown box</Label><br>
                <input type="radio" name="petType" value="cat" checked>Cat<br>
                <input type="radio" name="petType" value="dog">Dog<br>
                <input type="radio" name="petType" value="TEMP">TEMP<br>
                <span class="help-block"><?php echo $petType_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($petName_err)) ? 'has-error' : ''; ?>">
                <label>Pets Name:</label><br>
                <input type="text" name="petName">
                <span class="help-block"><?php echo $petName_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                <label>Is the pet a ...</label><br>
                <input type="radio" name="gender" value="Male" checked> Male<br>
                <input type="radio" name="gender" value="Female"> Female<br>
                <span class="help-block"><?php echo $gender_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($petAge_err)) ? 'has-error' : ''; ?>">
                <label>How old is the pet?</label><br>
                <input type="radio" name="age" value="2" checked> Age: 0-2<br>
                <input type="radio" name="age" value="4"> Age: 2-4<br>
                <span class="help-block"><?php echo $gender_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($breed_err)) ? 'has-error' : ''; ?>">
                <Label>TEMP VALUE add away to choose the breed maybe a dropdown box.</Label><br>
                <input type="text" name="breed"><br>
                <span class="help-block"><?php echo $breed_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($neutered_err)) ? 'has-error' : ''; ?>">
                <label>Are they neutered?</label><br>
                <input type="radio" name="neutered" value="Yes" checked>Yes<br>
                <input type="radio" name="neutered" value="No">No<br>
                <span class="help-block"><?php echo $neutered_err; ?></span>
            </div>
            
            <div>
                <Label>TEMP VALUE add away to choose all needed based on type of pet.</Label><br>
                <input type="checkbox" name="vaccine1" value="vac1"> Vaccine A<br>
                <input type="checkbox" name="vaccine2" value="vac2"> Vaccine B<br><br>
            </div>
            
            <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                <label>Upload a Pet Picture:</label><br>
                <input type="file" name="image" />
                <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                <span class="help-block"><?php echo $imgSize_err; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                <br><label>Pet Bio:</label><br>
                <textarea rows="4" cols="50" name="bio" placeholder="Loves to go play with other pets and loves to suggle next to you."></textarea><br>
                <span class="help-block"><?php echo $bio_err; ?></span>
            </div>
            
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
            <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/><br>
        </form>
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