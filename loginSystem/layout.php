/*
Custom CSS Stylesheet
*/
<?php 
	header("Content-type: text/css; charset: UTF-8");
    // Logic for random background
    $background = array('hero.jpg','hero2.jpg');
    
    // Get a random number and set background
    $i = rand(0, count($background)-1);
    $selectedBg = "$background[$i]";
    
?>

body, html {
  height: 100%;
  margin: 0;
  font-family: arial, Helvetica, sans-serif;
}

.logo-image {
    width: 90vw;
    height: auto;
    position: center;
    

}
.hero-image {  

   /* Sizing */
    width: 100vw;
    height: 100vh;
    
    /* Flexbox stuff */
    display: flex;
    justify-content: center;
    align-items: center;
    
    /* Text styles */
    text-align: center;
    color: white;
    
    /* Background styles */
    background-image: linear-gradient(rgba(255,255,255, 2), rgba(0,0,0, 0.5)), url(images/<?php echo $selectedBg; ?>);
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
	
	/* Opacity */
	opacity: 0;

}
.hero-text {
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: black;
}

.hero-text button {
  border: none;
  outline: 0;
  display: inline-block;
  padding: 10px 25px;
  color: black;
  background-color: #ddd;
  text-align: center;
  cursor: pointer;
}

.hero-text button:hover {
  background-color: #555;
  color: white;
}

/*
 * Globals
 */

/* Links */
a,
a:focus,
a:hover {
  color: black;
}

/* Custom default button */
.btn-secondary,
.btn-secondary:hover,
.btn-secondary:focus {
  color: #333;
  text-shadow: none; /* Prevent inheritance from `body` */
  background-color: #fff;
  border: .05rem solid #fff;
}


/*
 * Base structure
 */

html,
body {
  height: 100%;
  background-color: #333;
}

body {
  display: -ms-flexbox;
  display: flex;
  color: #fff;
  text-shadow: 0 .05rem .1rem rgba(0, 0, 0, 255);
  box-shadow: inset 0 0 5rem rgba(0, 0, 0, 255);
}

.cover-container {
  max-width: 100%;
}


/*
 * Header
 */
.masthead {
  margin-bottom: 2rem;
}

.masthead-brand {
  margin-bottom: 0;
}

.nav-masthead .nav-link {
  padding: .25rem 0;
  font-weight: 700;
  color:#333;
  background-color: transparent;
  border-bottom: .25rem solid transparent;
}

.nav-masthead .nav-link:hover,
.nav-masthead .nav-link:focus {
  border-bottom-color: #333;
}

.nav-masthead .nav-link + .nav-link {
  margin-left: 1rem;
}

.nav-masthead .active {
  color: #333;
  border-bottom-color: #333;
}

@media (min-width: 48em) {
  .masthead-brand {
    float: left;
  }
  .nav-masthead {
    float: right;
  }
}


/*
 * Cover
 */
.cover {
  padding: 0 1.5rem;
}
.cover .btn-lg {
  padding: .75rem 1.25rem;
  font-weight: 700;
}


/*
 * Footer
 */
.mastfoot {
  color: rgba(255, 255, 255, .5);
}
