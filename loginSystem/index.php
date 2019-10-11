<!DOCTYPE html>
<style type="text/css">
</style>
<html>
<head>
<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- include bootstrap --> 
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="layout.php">
</head>
<body>

    <body class="text-center">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column hero-image">
  <header class="masthead mb-auto">
    <div class="inner">
        <div class="logo-image"><img src="/images/image.png" alt="logo" width=auto height="250"></div>
<!--
      <nav class="nav nav-masthead justify-content-center">
        <a class="nav-link active" href="#">Home</a>
        <a class="nav-link" href="#">Contact</a> 
      </nav> -->
    </div>
  </header>

  <main role="main" class="inner cover">
    <h1 class="cover-heading">Find your Pet</h1>
    <p class="lead">We make adopting pets and finding your forever best friend easier!</p>
    <p class="lead">
      <button id="register_btn" class="btn btn-lg btn-secondary">Register</button>
      <a href="login.php" class="btn btn-lg btn-secondary">Login</a>
    </p>
  </main>

  <footer class="mastfoot mt-auto">
    <div class="inner">
      <p>@2019 Find your Pet</p>
    </div>
  </footer>
</div>
     
   
        
   <body>
   <!-- include jquery, popper.js, and bootstrap js -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

   </body>
    
<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title col-12 text-secondary">Are you a...</h5>
      </div>
      <div class="modal-body col-12">
		<a href="registerShelter.php" class="btn btn-primary">Shelter</a>
		<a href="register.php" class="btn btn-success">User</a>
      </div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
	
</html>
<script>
	$(document).ready(function(){
		$('.hero-image').animate({ opacity: 1 }, 700)
	})
	$('#register_btn').on('click',function(){
		$('#myModal').modal('toggle');
	});
</script>

