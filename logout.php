<?php
   session_start();
   
   //remove all session variables
   session_unset();

   //destroy the session
   session_destroy();


   echo '<p>You have been logged out.</p>';
   echo '<a href="login.php">Log back in</a>';
?>
