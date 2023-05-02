<?php
    session_start();

    include('config.php');

    $note_id = key($_POST);
    echo $note_id;
    echo "<br>";

    // Insert row into Quote_Item table
    $sql = "DELETE FROM Quote_Note WHERE foreign_note_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($note_id));

    // Insert row into Item table
    $sql = "DELETE FROM Note WHERE note_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($note_id));

    // Redirect to quote page
    header("Location: quote.php");
?>