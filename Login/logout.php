<?php
session_start();
if (session_destroy())// Destroying All Sessions
{
	// Redirecting To Home Page	
	header("Location: ../layout_2/index.php");
}
?>