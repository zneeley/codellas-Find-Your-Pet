<!DOCTYPE html>
<style type="text/css">
</style>
<html>
<head>
<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- include jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- include bootstrap --> 
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
         
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
      <a href="register.php" class="btn btn-lg btn-secondary">Register</a>
      <a href="login.php" class="btn btn-lg btn-secondary">Login</a>
    </p>
  </main>

  <footer class="mastfoot mt-auto">
    <div class="inner">
      <p>@2019 Find your Pet</p>
    </div>
  </footer>
</div>
     
   
        
   </body>
    </body>
    
</html>
<script>
	$(document).ready(function(){
		$('.hero-image').animate({ opacity: 1 }, 700)
	})
</script>

