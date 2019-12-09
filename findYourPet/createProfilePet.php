<?php

//Include the config.php file
require_once "config.php";

// init variables
$fileDir = $fileNameNew = $accountBio = $petID = $shelterID = $petType = $gender = $neutered = $vaccinationRecords = $petName = $petAge = $shelterID = $breed = "";
$imgExt_err = $imgSize_err = $bio_err = $gender_err = $petType_err = $petName_err = $neutered_err = $petAge_err = $breed_err = "";
$profileType =  $editType = $profileImgDir = $reCaptchaVal = "";

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
    $vaccinationRecords = $_POST['vac-list'];
    
    // Create UPID
    $petID = uniqid("UPID-");
    
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
                        <input type="radio" name="gender" id="male-dog" value="Male" checked>
                        <label for="male-dog">Male</label><br>
                        <input type="radio" name="gender" id="female-dog" value="Female">
                        <label for="female-dog">Female</label><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($petAge_err)) ? 'has-error' : ''; ?>">
                        <h5>How old is the pet? (In human years)</h5>
                        <input type="radio" name="age" id="0-2-dog" value="0-2 years" checked>
                        <label for="0-2-dog">0-2 years</label><br>
                        <input type="radio" name="age" id="2-4-dog" value="2-4 years">
                        <label for="2-4-dog">2-4 years</label><br>
                        <input type="radio" name="age" id="4-6-dog" value="4-6 years">
                        <label for="4-6-dog">4-6 years</label><br>
                        <input type="radio" name="age" id="6-8-dog" value="6-8 years">                        
                        <label for="6-8-dog">6-8 years</label><br>
                        <input type="radio" name="age" id="8-10-dog" value="8-10 years">
                        <label for="8-10-dog">8-10 years</label><br>
                        <input type="radio" name="age" id="10-12-dog" value="10-12 years">
                        <label for="10-12-dog">10-12 years</label><br>
                        <input type="radio" name="age" id="12-dog" value="12 years or older">                        
                        <label for="12-dog">12+ years</label><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($breed_err)) ? 'has-error' : ''; ?>">
                        <h5>Please select breed.<h5>
                        <select class="form-control" name="breed">
                            <option disabled selected value="">Select Breed</option>
                            <option value="Labrador Retriever">Labrador Retriever</option>
                            <option value="Bulldog">Bulldog</option>
                            <option value="Siberian Hucky">Siberian Husky</option>
                            <option value="Pug">Pug</option>
                            <option value="Pomeranian">Pomeranian</option>
                            <option value="Golden Retriever">Golden Retriever</option>
                            <option value="Shiba Inu">Shiba Inu</option>
                            <option value="Poodle">Poodle</option>
                            <option value="German Shepard">German Shepard</option>
                            <option value="American Pit Bull Terrier">American Pit Bull Terrier</option>
                            <option value="Chihuahua">Chihuahua</option>
                            <option value="Beagle">Beagle</option>
                            <option value="Dachshund">Dachshund</option>
                            <option value="Maltese">Maltese</option>
                            <option value="Rottweiler">Rottweiler</option>
                            <option value="French Bulldog">French Bulldog</option>
                            <option value="Dobermann">Dobermann</option>
                            <option value="Shih Tzu">Shih Tzu</option>
                            <option value="Chow Chow">Chow Chow</option>
                            <option value="Great Dane">Great Dane</option>
                            <option value="Samoyed">Samoyed</option>
                            <option value="Boxer">Boxer</option>
                            <option value="Yorkshire Terrier">Yorkshire Terrier</option>
                            <option value="Border Collie">Border Collie</option>
                            <option value="Australian Shepherd">Australian Shepherd</option>
                            <option value="Cane Corso">English Mastiff</option>
                            <option value="Pembroke Welsh Corgi">Pembroke Welsh Corgi</option>
                            <option value="Newfoundland">Newfoundland</option>
                            <option value="Jack Russell Terrier">Jack Russell Terrier</option>
                            <option value="Bichon Frise">Bichon Frise</option>
                            <option value="St. Bernard">St. Bernard</option>
                            <option value="Sarabi Mastiff">Sarabi Mastiff</option>
                            <option value="Akita">Akita</option>
                            <option value="Miniature Pinscher">Miniature Pinscher</option>
                            <option value="Bull Terrier">Bull Terrier</option>
                            <option value="Shar Pei">Shar Pei</option>
                            <option value="Boston Terrier">Boston Terrier</option>
                            <option value="English Cocker Spaniel">English Cocker Spaniel</option>
                            <option value="Greyhound">Greyhound</option>
                            <option value="Belgian Shephard">Belgian Shepherd</option>
                            <option value="Labradoodle">Labradoodle</option>
                            <option value="Cavalier King Charles Spaniel">Cavalier King Charles Spaniel</option>
                            <option value="Malinois">Malinois</option>
                            <option value="American Bully">American Bully</option>
                            <option value="Havanese">Havanese</option>
                            <option value="Basenji">Basenji</option>
                            <option value="Bandog">Bandog</option>
                            <option value="Weimaraner">Weimaraner</option>
                            <option value="Bernese Mountian Dog">Bernese Mountian Dog</option>
                            <option value="Papillon">Papillon</option>
                        </select><br>
                    </div>
                    <div class="form-group <?php echo (!empty($neutered_err)) ? 'has-error' : ''; ?>">
                        <h5>Are they neutered?</h5>
                        <input type="radio" name="neutered" id="nutered-dog-y" value="Yes" checked>
                        <label for="nutered-dog-y">Yes</label><br>
                        <input type="radio" name="neutered" id="nutered-dog-n" value="No">
                        <label for="nutered-dog-n">No</label><br>
                        <span class="help-block"><?php echo $neutered_err; ?></span>
                    </div>
                    <div>
                        <h5>Dog Vaccinations. Check all the apply.</h5>
                        <input type="checkbox" class="vac" id="vac1-dog" value="Rabies">
                        <label for="vac1-dog">Rabies</label><br>
                        <input type="checkbox" class="vac" id="vac2-dog"value="Distemper">
                        <label for="vac2-dog">Distemper</label><br>
                        <input type="checkbox" class="vac" id="vac3-dog"value="Parvovirus">
                        <label for="vac3-dog">Parvovirus</label><br>
                        <input type="checkbox" class="vac" id="vac4-dog"value="Adenovirus Type 1">
                        <label for="vac4-dog">Adenovirus Type 1</label><br>
                        <input type="checkbox" class="vac" id="vac5-dog"value="Adenovirus Type 2">
                        <label for="vac5-dog">Adenovirus Type 2</label><br>
                        <input type="checkbox" class="vac" id="vac6-dog"value="Parainfluenza">                        
                        <label for="vac6-dog">Parainfluenza</label><br>
                        <input type="checkbox" class="vac" id="vac7-dog"value="Bordetella bronchiseptica (kennel cough)">
                        <label for="vac7-dog">Bordetella bronchiseptica (kennel cough)</label><br>
                        <input type="checkbox" class="vac" id="vac8-dog"value="Lyme disease">
                        <label for="vac8-dog">Lyme disease</label><br>
                        <input type="checkbox" class="vac" id="vac9-dog"value="Leptospirosis">
                        <label for="vac9-dog">Leptospirosis</label><br>
                        <input type="checkbox" class="vac" id="vac10-dog"value="Canine influenza">
                        <label for="vac10-dog">Canine influenza</label><br>
                        <input type="hidden" class="vac-list" name="vac-list" val=""><br>
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
                    <input type="hidden" value="" class="recaptchaResponse" name="recaptcha_response"/>
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
                        <input type="radio" name="gender" id="male-cat" value="Male" checked>
                        <label for="male-cat">Male</label><br>
                        <input type="radio" name="gender" id="female-cat" value="Female">
                        <label for="female-cat">Female</label><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($petAge_err)) ? 'has-error' : ''; ?>">
                        <h5>How old is the pet? (In human years)</h5>
                        <input type="radio" name="age" id="0-2-cat" value="0-2 years" checked>
                        <label for="0-2-cat">0-2 years</label><br>
                        <input type="radio" name="age" id="2-4-cat" value="2-4 years">
                        <label for="2-4-cat">2-4 years</label><br>
                        <input type="radio" name="age" id="4-6-cat" value="4-6 years">
                        <label for="4-6-cat">4-6 years</label><br>
                        <input type="radio" name="age" id="6-8-cat" value="6-8 years">
                        <label for="6-8-cat">6-8 years</label><br>
                        <input type="radio" name="age" id="8-10-cat" value="8-10 years">
                        <label for="8-10-cat">8-10 years</label><br>
                        <input type="radio" name="age" id="10-12-cat" value="10-12 years">
                        <label for="10-12-cat">10-12 years</label><br>
                        <input type="radio" name="age" id="12-14-cat" value="12-14 years">
                        <label for="12-14-cat">12-14 years</label><br>
                        <input type="radio" name="age" id="14-16-cat" value="14-16 years">
                        <label for="14-16-cat">14-16 years</label><br>
                        <input type="radio" name="age" id="16-cat" value="16 years or older">
                        <label for="16-cat">16+ years</label><br>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($breed_err)) ? 'has-error' : ''; ?>">
                        <h5>Please select breed.<h5>
                        <select class="form-control" name="breed">
                            <option disabled selected value="">Select Breed</option>
                            <option value="Bengal Cat">Bengal Cat</option>
                            <option value="Persian Cat">Persian Cat</option>
                            <option value="Maine Coon">Maine Coon</option>
                            <option value="Siamese Cat">Siamese Cat</option>
                            <option value="British Shorthair">British Shorthair</option>
                            <option value="Sphynx Cat">Sphynx Cat</option>
                            <option value="Ragdoll">Ragdoll</option>
                            <option value="Russian Blue">Russian Blue</option>
                            <option value="Munchikin Cat">Munchkin Cat</option>
                            <option value="Savannah Cat">Savannah Cat</option>
                            <option value="Scottish Fold">Scottish Fold</option>
                            <option value="Siberian Cat">Siberian Cat</option>
                            <option value="Norweigna Forest Cat">Norwegian Forest Cat</option>
                            <option value="Birman">Birman</option>
                            <option value="Turkish Angora">Turkish Angora</option>
                            <option value="Abyssinian Cat">Abyssinian Cat</option>
                            <option value="american Shorthair">American Shorthair</option>
                            <option value="Toyger">Toyger</option>
                            <option value="Himalayan Cat">Himalayan Cat</option>
                            <option value="Chartreux">Chartreux</option>
                            <option value="Bombay Cat">Bombay Cat</option>
                            <option value="Singapura Cat">Singapura Cat</option>
                            <option value="Ragamuffin Cat">Ragamuffin Cat</option>
                            <option value="breed 2">Balinese Cat</option>
                            <option value="Manx Cat">Manx Cat</option>
                            <option value="Selkirk Rex">Selkirk Rex</option>
                            <option value="Peterbald">Peterbald</option>
                            <option value="American Wirehair">American Wirehair</option>
                            <option value="Somali Cat">Somali Cat</option>
                            <option value="European Shorthair">European Shorthair</option>
                            <option value="Nebelung">Nebelung</option>
                            <option value="Burmese Cat">Burmese Cat</option>
                            <option value="American Curl">American Curl</option>
                            <option value="Devon Rex">Devon Rex</option>
                            <option value="Ocicar">Ocicat</option>
                            <option value="Van Cat">Van Cat</option>
                            <option value="Pixie-bob">Pixie-bob</option>
                            <option value="Oriental Shorthair">Oriental Shorthair</option>
                            <option value="Burmilla">Burmilla</option>
                            <option value="Scottish Straight">Scottish Straight</option>
                            <option value="Tonkinese Cat">Tonkinese Cat</option>
                            <option value="Korat">Korat</option>
                            <option value="Lykoi">Lykoi</option>
                            <option value="Japanese Bobrail">Japanese Bobtail</option>
                            <option value="Snowshoe Cat">Snowshoe Cat</option>
                            <option value="Havana Brown">Havana Brown</option>
                            <option value="Minskin">Minskin</option>
                            <option value="LaPerm">LaPerm</option>
                            <option value="Oriental Longhair">Cornish Rex</option>
                            <option value="Oriental Longhair">Tabby</option>
                        </select><br>
                    </div>
                    <div class="form-group <?php echo (!empty($neutered_err)) ? 'has-error' : ''; ?>">
                        <h5>Are they neutered?</h5>
                        <input type="radio" name="neutered" id="nutered-cat-y" value="Yes" checked>
                        <label for="nutered-cat-y">Yes</label><br>
                        <input type="radio" name="neutered" id="nutered-cat-n" value="No">
                        <label for="nutered-cat-n">No</label><br>
                        <span class="help-block"><?php echo $neutered_err; ?></span>
                    </div>
                    <div>
                        <h5>Cat Vaccinations. Check all the apply.</h5>
                        <input type="checkbox" class="vac" id="vac1-cat" value="Rabies">
                        <label for="vac1-cat">Rabies</label><br>
                        <input type="checkbox" class="vac" id="vac2-cat"value="Feline Distemper (Panleukopenia)">
                        <label for="vac2-cat">Feline Distemper (Panleukopenia)</label><br>
                        <input type="checkbox" class="vac" id="vac3-cat" value="Feline Herpesvirus">
                        <label for="vac3-cat">Feline Herpesvirus</label><br>
                        <input type="checkbox" class="vac" id="vac4-cat" value="Calicivirus">
                        <label for="vac4-cat">Calicivirus</label><br>
                        <input type="checkbox" class="vac" id="vac5-cat" value="Feline Leukemia Virus (FeLV)">
                        <label for="vac5-cat">Feline Leukemia Virus (FeLV)</label><br>
                        <input type="checkbox" class="vac" id="vac6-cat" value="Bordetella">
                        <label for="vac6-cat">Bordetella</label><br><br>
                        <input type="hidden" class="vac-list" name="vac-list" val="">
                    </div>
                    
                    <div class="form-group <?php echo (!empty($imgExt_err) && !empty($imgSize_err)) ? 'has-error' : ''; ?>">
                        <h5>Upload a Pet Picture:</h5><br>
                        <input type="file" name="image" accept=".png,.jpg,.jpeg"/>
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
                    <input type="hidden" value="" class="recaptchaResponse" name="recaptcha_response"/>
                </form>
            </div>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6Lc7Cb0UAAAAAIMgxbAXd9kLcVhLPeapc8zsouu7', { action: 'profile' })
                .then(function (token) {
                $(".recaptchaResponse").each( function(){
                  $(this).val(token);
                  console.log($(this).val());
                });
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
