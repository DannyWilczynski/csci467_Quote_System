<?php
    session_start();
    
    include('config.php');

    $keys = array_keys($_POST);
    $_SESSION['ITEM_ID'] = $keys[2];

    $new_price = $_POST['price'];

    // Store old item price for quote price calculations
    $sql = "SELECT * FROM Item WHERE item_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('id' => $_SESSION['ITEM_ID']));
    $item = $prepared->fetch();
    $old_price = $item['price'];

    // Reject negative and non-nunmeric values
    if($new_price < 0 || !is_numeric($new_price)) {
        $new_price = $old_price;
    }

    // Reject values greater than price max (99999.99)
    if($new_price > 99999.99) {
        $new_price = $old_price;
    }

    $diff = $new_price - $old_price;

    // Update item name
    $sql = "UPDATE Item SET item_name=:item_name WHERE item_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('item_name' => $_POST['name'], 'id' => $_SESSION['ITEM_ID']));

    // Update item price
    $sql = "UPDATE Item SET price=:price WHERE item_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('price' => $new_price, 'id' => $_SESSION['ITEM_ID']));

    // Update quote price
    $sql = "SELECT * FROM Quote WHERE quote_id=?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $quote = $prepared->fetch();

    $quote_price = $quote['price'];
    $quote_price = $quote_price + $diff;

    if($quote_price < 0) {
        $quote_price = 0;
    }

    $sql = "UPDATE Quote SET price=:price WHERE quote_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('price' => $quote_price, 'id' => $_SESSION['QUOTE_ID']));

    // Redirect to quote page
    header("Location: quote.php");
?>