<?php
    session_start();

    include('config.php');

    $item_id = key($_POST);

    // Store item price for quote price calculations
    $sql = "SELECT * FROM Item WHERE item_id=?;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array($item_id));
    $item = $prepared->fetch();
    $item_price = $item['price'];

    // Insert row into Quote_Item table
    $sql = "DELETE FROM Quote_Item WHERE foreign_item_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($item_id));

    // Insert row into Item table
    $sql = "DELETE FROM Item WHERE item_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($item_id));

    // Update quote price
    $sql = "SELECT * FROM Quote WHERE quote_id=?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $quote = $prepared->fetch();

    $price = $quote['price'];
    $price = $price - $item_price;

    if($price < 0) {
        $price = 0;
    }

    $sql = "UPDATE Quote SET price=:price WHERE quote_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('price' => $price, 'id' => $_SESSION['QUOTE_ID']));

    // Redirect to quote page
    header("Location: quote.php");
?>