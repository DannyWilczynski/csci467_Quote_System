<?php
    session_start();
    
    include('config.php');

    $keys = array_keys($_POST);
    $_SESSION['NOTE_ID'] = $keys[1];

    // Update item name
    $sql = "UPDATE Note SET text_field=? WHERE note_id=?";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_POST['note'], $_SESSION['NOTE_ID']));

    // Redirect to quote page
    header("Location: quote.php");
?>