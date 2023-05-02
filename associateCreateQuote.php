<?php
    include('config.php');
    session_start();

    if($_POST['customer'] != ''){
        $sql = 'SELECT * FROM customers WHERE id = '.$_POST['customer'];
        $prepared = $db2->prepare($sql);
        $success = $prepared->execute();
        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        foreach($rows as $row){
            echo '<h4>Quote for: ' . $row['name'] . '</h4>';
            echo $row['street'] . '</br>';
            echo $row['city'] . '</br>';
            echo $row['contact'] . '</br>';
        }

        echo '</br></br>';

        echo '<form action="home.php" method="POST">';
        echo '<label for="cust_email">Customer Email:</label></br>';
        echo '<input type="text" id="cust_email" name="cust_email">';
        
        echo '</br>';

        //LINE ITEM
        echo '<h4>Line Item:</h4>';
        echo '<label for="item_name">Item Name</label>';
        echo '<input type="text" id="item_name" name="item_name"></br>';

        echo '<label for="item_price">Item Price</label>';
        echo '<input type="number" id="item_price" name="item_price" min=0>';

        //SECRET NOTES
        echo '<h4>Secret Note:</h4>';
        echo '<label for="note_text">Note</label>';
        echo '<input type="text" id="note_text" name="note_text"></br>';

        //SUBMIT
        echo '</br>';
        echo '<input type="submit" name="submit_new_quote" value="Submit Quote">';


        echo '</form>';
    }
    else{
        echo 'You did not choose a customer.';
    }

    echo '<form action="home.php" method="POST">';
    echo '<input type="submit" name="go_back" value="Cancel">';
    echo '</form>';

?>