<?php
   session_start();
   
   //remove all session variables
   session_unset();

   //destroy the session
   session_destroy();

   echo "<link rel='stylesheet' href='style.css'>";
   echo "<div id='logout'>";
   echo "<p>You have been logged out.</p>";
   echo "<form action=\"login.php\" method=\"post\">";
      echo "<button type=\"submit\"> Back to Login</button>";
   echo "</div>";
?>