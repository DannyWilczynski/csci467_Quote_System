<?php
    session_start();

    include('config.php');

    $quote_id = key($_POST);

    // Delete quote from Create_Quote
    $sql = "DELETE FROM Create_Quote WHERE foreign_quote_id = ?";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($quote_id));

    // Delete quote from Quote_Note
    $sql = "DELETE FROM Quote_Note WHERE foreign_quote_id = ?";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($quote_id));

    // Delete quote from Quote_Item
    $sql = "DELETE FROM Quote_Item WHERE foreign_quote_id = ?";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($quote_id));

    // Delete specified quote from table
    $sql = "DELETE FROM Quote WHERE quote_id = ?";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($quote_id));

    // Redirect to quote page
    header("Location: hq.php");
?>