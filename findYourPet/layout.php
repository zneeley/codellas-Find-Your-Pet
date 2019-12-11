/*
Custom CSS Stylesheet
*/
<?php 
	header("Content-type: text/css; charset: UTF-8");
    // Logic for random background
    $background = array('hero.jpg','hero2.jpg','hero3.jpeg','hero4.jpeg','hero5.jpeg','hero6.jpg','hero7.jpg','hero8.jpg','hero9.jpg','hero10.jpg','hero11.jpg', 'hero12.jpg', 'hero13.jpg', 'hero14.jpg', 'hero15.jpg');
    
    // Get a random number and set background
    $i = rand(0, count($background)-1);
    $selectedBg = "$background[$i]";
    
?>



.content-container{
    position: absolute;
    top: 35%;
    max-width: 100%
	}

.logo{
    width: 60%;
    max-width: 100%;
    height: auto;
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
    color: black;
    
    /* Background styles */
    background-image: linear-gradient(rgba(255,255,255,255), rgba(0,0,0, 0)), url(images/<?php echo $selectedBg; ?>);
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
	
	/* Opacity */
	opacity: 0;
	
	

}

.relative {
  position: relative;
  left: 80px;
  top: 150px;
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

.container{
height: 100%;
align-content: center;
}

.card{
height: auto;
margin-top: auto;
margin-bottom: auto;
width: 330px;
xbackground-color: rgba(0,0,0,0.5) !important;
}

.card-header h3{
color: white;
}

.input-group-prepend span{
width: 50px;
background-color: #FFC312;
color: black;
border:0 !important;
}

input:focus{
outline: 0 0 0 0  !important;
box-shadow: 0 0 0 0 !important;

}

.remember{
color: white;
}

.remember input
{
width: 20px;
height: 20px;
margin-left: 15px;
margin-right: 5px;
}

.login_btn{
color: black;
background-color: #FFC312;
width: 100px;
}

.login_btn:hover{
color: black;
background-color: white;
}

.links{
color: white;
}

.links a{
margin-left: 4px;
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

/*
 * Profile Picture
 */
 .profile_pic{
   border-radius: 100%;
   width: 50px;
   height: 50px;
   shape-outside: circle();
   float:left;
   margin-right: 10px;
   margin-bottom: 10px;
 }
 .profile_pic_large{
   border-radius: 100%;
   width: 100px;
   height: 100px;
   shape-outside: circle();
   float:left;
   margin-right: 10px;
   margin-bottom: 10px;
 }
 .profile_text{
	 text-align: justify;
 }
 
 /*
 * Navbar
 */
 .navbar_pic{
   width: 50px;
   height: 50px;
   margin-right: 10px;
   margin-bottom: 10px;
 }
 .profile_text{
	 text-align: justify;
 }
