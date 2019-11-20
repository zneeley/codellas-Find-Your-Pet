<?php

// Start session
session_start();

// Reset all session variables
$_SESSION = array();

// Kill off the session
session_destroy();

// Send back to the login page
header("location: index.php");
exit;

?>

