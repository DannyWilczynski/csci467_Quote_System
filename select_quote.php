<?php
    session_start();

    include('config.php');
    $_SESSION['QUOTE_ID'] = key($_POST);

    // Redirect to quote page
    header("Location: quote.php");
?>