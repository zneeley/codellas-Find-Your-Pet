<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $petID = $shelterID = $petType = $gender = $neutered = $vaccinationRecords = $petName = $petAge = $shelterID = $breed = "";
$imgExt_err = $imgSize_err = $bio_err = $gender_err = $petType_err = $petName_err = $neutered_err = $petAge_err = $breed_err = "";

// Start Session
session_start();

// Check to see if the username is still logged in, if not send them to login

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//NAVBAR//
$profileType =  $editType = $profileImgDir = "";
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
            $breed_err = "Please select a breed.";
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
        $fileNameNew = $_SESSION['accountID'].".".$fileExt;
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
            $param_petID = uniqid("UPID-");
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
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <img class="navbar_pic" src="images/pawprint.jpg" alt="Your image">
		  <a class="navbar-brand" href="#">F.Y.P</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-toggle" aria-controls="navbar-toggle" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbar-toggle">
			<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
			  <li class="nav-item active">
				<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
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
            <div class="form-group m-4 <?php echo (!empty($petType_err)) ? 'has-error' : ''; ?>">
                <h4 class="">Please select a type of pet</h4>
                <select class="form-control " name="pet" id="pet-select">
                    <option value="" selected disabled>Select a Pet!</option>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                </select>
            </div>
            
            <div id="dog" class="d-none m-4 pet-form">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="petType" value="dog">
                    <div class="form-group <?php echo (!empty($petName_err)) ? 'has-error' : ''; ?>">
                        <h5>Pet's Name:</h5>
                        <input class="form-control" type="text" name="petName">
                        <span class="help-block"><?php echo $petName_err; ?></span>
                    </div>
                    
                    <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                        <h5>Is the pet a ...</h5>
                        <label for="male-dog">Male</label>
                        <input type="radio" name="gender" id="male-dog" value="male" checked><br>
                        <label for="female-dog">Female</label>
                        <input type="radio" name="gender" id="female-dog" value="female"><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($petAge_err)) ? 'has-error' : ''; ?>">
                        <h5>How old is the pet?</h5>
                        <label for="0-2-dog">0-2 years</label>
                        <input type="radio" name="age" id="0-2-dog" value="2" checked><br>
                        <label for="2-4-dog">2-4 years</label>
                        <input type="radio" name="age" id="2-4-dog" value="4"><br>
                        <label for="5-dog">5+ years</label>
                        <input type="radio" name="age" id="5-dog" value="5"><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($breed_err)) ? 'has-error' : ''; ?>">
                        <h5>Please select breed.<h5>
                        <select class="form-control" name="breed">
                            <option disabled selected value="">Select Breed</option>
                            <option value="breed 1">Breed 1</option>
                            <option value="breed 2">Breed 2</option>                   
                        </select><br>
                    </div>
                    <div class="form-group <?php echo (!empty($neutered_err)) ? 'has-error' : ''; ?>">
                        <h5>Are they neutered?</h5>
                        <label for="nutered-dog-y">Yes</label>
                        <input type="radio" name="neutered" id="nutered-dog-y" value="yes" checked><br>
                        <label for="nutered-dog-n">No</label>
                        <input type="radio" name="neutered" id="nutered-dog-n" value="no"><br>
                        <span class="help-block"><?php echo $neutered_err; ?></span>
                    </div>
                    <div>
                        <h5>TEMP VALUE add away to choose all needed based on type of pet.</h5>
                        <label for="vac1-dog">Vaccine A</label>
                        <input type="checkbox" class="vac" id="vac1-dog" value="vac1"><br>
                        <label for="vac2-dog">Vaccine B</label>
                        <input type="checkbox" class="vac" id="vac2-dog"value="vac2"><br>
                        <label for="vac3-dog">Vaccine C</label>
                        <input type="checkbox" class="vac" id="vac3-dog" value="vac3"><br>
                        <label for="vac4-dog">Vaccine D</label>
                        <input type="checkbox" class="vac" id="vac4-dog" value="vac4"><br><br>
                        <input type="hidden" class="vac-list" name="vac-list" val="">
                    </div>
                    
                    <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                        <h5>Upload a Pet Picture:</h5><br>
                        <input type="file" name="image" />
                        <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                        <span class="help-block"><?php echo $imgSize_err; ?></span>
                    </div>
                    
                    <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                        <br><h5>Pet Bio:</h5><br>
                        <textarea rows="4" cols="50" name="bio" placeholder="Loves to go play with other pets and loves to suggle next to you."></textarea><br>
                        <span class="help-block"><?php echo $bio_err; ?></span>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                    <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/>
                </form>
            </div>
            
            <div id="cat" class="d-none m-4 pet-form">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="petType" value="cat">
                    <div class="form-group <?php echo (!empty($petName_err)) ? 'has-error' : ''; ?>">
                        <h5>Pet's Name:</h5>
                        <input class="form-control" type="text" name="petName">
                        <span class="help-block"><?php echo $petName_err; ?></span>
                    </div>
                    
                    <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                        <h5>Is the pet a ...</h5>
                        <label for="male-cat">Male</label>
                        <input type="radio" name="gender" id="male-cat" value="male" checked><br>
                        <label for="female-cat">Female</label>
                        <input type="radio" name="gender" id="female-cat" value="female"><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($petAge_err)) ? 'has-error' : ''; ?>">
                        <h5>How old is the pet?</h5>
                        <label for="0-2-cat">0-2 years</label>
                        <input type="radio" name="age" id="0-2-cat" value="2" checked><br>
                        <label for="2-4-cat">2-4 years</label>
                        <input type="radio" name="age" id="2-4-cat" value="4"><br>
                        <label for="5-cat">5+ years</label>
                        <input type="radio" name="age" id="5-cat" value="5"><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($breed_err)) ? 'has-error' : ''; ?>">
                        <h5>Please select breed.<h5>
                        <select class="form-control" name="breed">
                            <option disabled selected value="">Select Breed</option>
                            <option value="breed 1">Breed 1</option>
                            <option value="breed 2">Breed 2</option>                   
                        </select><br>
                    </div>
                    <div class="form-group <?php echo (!empty($neutered_err)) ? 'has-error' : ''; ?>">
                        <h5>Are they neutered?</h5>
                        <label for="nutered-cat-y">Yes</label>
                        <input type="radio" name="neutered" id="nutered-cat-y" value="yes" checked><br>
                        <label for="nutered-cat-n">No</label>
                        <input type="radio" name="neutered" id="nutered-cat-n" value="no"><br>
                        <span class="help-block"><?php echo $neutered_err; ?></span>
                    </div>
                    <div>
                        <h5>TEMP VALUE add away to choose all needed based on type of pet.</h5>
                        <label for="vac1-cat">Vaccine A</label>
                        <input type="checkbox" class="vac" id="vac1-cat" value="vac1"><br>
                        <label for="vac2-cat">Vaccine B</label>
                        <input type="checkbox" class="vac" id="vac2-cat"value="vac2"><br>
                        <label for="vac3-cat">Vaccine C</label>
                        <input type="checkbox" class="vac" id="vac3-cat" value="vac3"><br>
                        <label for="vac4-cat">Vaccine D</label>
                        <input type="checkbox" class="vac" id="vac4-cat" value="vac4"><br><br>
                        <input type="hidden" class="vac-list" name="vac-list" val="">
                    </div>
                    
                    <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                        <h5>Upload a Pet Picture:</h5><br>
                        <input type="file" name="image" />
                        <br><span class="help-block"><?php echo $imgExt_err; ?></span>
                        <span class="help-block"><?php echo $imgSize_err; ?></span>
                    </div>
                    
                    <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                        <br><h5>Pet Bio:</h5><br>
                        <textarea rows="4" cols="50" name="bio" placeholder="Loves to go play with other pets and loves to suggle next to you."></textarea><br>
                        <span class="help-block"><?php echo $bio_err; ?></span>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                    <input type="hidden" value="" name="recaptcha_response" id="recaptchaResponse"/>
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
    <!-- include jquery, popper.js, and bootstrap js -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>
<script>
    $("#pet-select").change(function(){
        $(".pet-form").addClass("d-none");
        $('#'+($(this).val())).removeClass("d-none");
    });
    $("form").submit(function(){
        var vacs = "";
        $(this).find(".vac").each(function(){
            if($(this).is(':checked')){
                vacs+=$(this).val()+',';
            }
        });
        $(this).find(".vac-list").val(vacs);
    });
</script>