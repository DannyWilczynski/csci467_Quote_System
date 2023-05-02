<?php
    include('config.php');
    session_start();

    $quote_id = $_GET['id'];

    $sql = 'SELECT * FROM User, Quote, Create_Quote 
        WHERE Quote.quote_id = ? 
            AND Create_Quote.foreign_quote_id = ? 
            AND Create_Quote.associate_id = User.user_id';
    $stmt = $db1->prepare($sql);
    $stmt->execute(array($quote_id, $quote_id));

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

    echo '</br>';

    foreach($rows as $row){
        //SHOW CUSTOMER INFO
        
        //store some values
        $date = $row['date'];
        $commission = 'commission placeholder';
        $email = $row['customerEmail'];


        $sql = 'SELECT * FROM customers WHERE id = ' .$row['customer'];
        $stmt = $db2->prepare($sql);
        $stmt->execute();
        $customer_fetch = $stmt->fetchALL(PDO::FETCH_ASSOC);

        foreach ($customer_fetch as $row) {
            echo 'Order From: ' . $row['name'] . '</br></br>';
            echo $row['street'] . '</br>';
            echo $row['city'] . '</br>';
            echo 'Contact: '. $row['contact'] . '</br>';
        }
        echo 'Date Fulfilled: ' . $date . '</br>';
        echo 'Commission: '. $commission .'</br>';
        echo '</br></br>';

        //SHOW CUSTOMER EMAIL
        echo 'Email: ' . $email;
    }

    

    //SHOW LINE ITEMS
    $sql = 'SELECT item_name, price FROM Item, Quote_Item
                WHERE Item.item_id = Quote_Item.foreign_item_id
                AND Quote_Item.foreign_quote_id = ?';

    $stmt = $db1->prepare($sql);
    $stmt->execute(array($quote_id));

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

    echo '<h4>Line Items:</h4>';

    foreach($rows as $row){
        echo $row['item_name'] . ' - $' . $row['price'] . '</br>';
    }

    echo '</br>';

    //SHOW SECRET NOTES
    $sql = 'SELECT text_field FROM Note, Quote_Note
                WHERE Note.note_id = Quote_Note.foreign_note_id
                AND Quote_Note.foreign_quote_id = ?';

    $stmt = $db1->prepare($sql);
    $stmt->execute(array($quote_id));

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

    echo '<h4>Notes:</h4>';

    foreach($rows as $row){
        echo '- ' . $row['text_field'] . '</br>';
    }

    echo '</br></br>';
    //SHOW TOTAL PRICE
    $sql = 'SELECT price FROM Quote WHERE quote_id = ' .$quote_id;
    $stmt = $db1->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        echo 'Amount: $' . $row['price'] . '</br>';
    } 

    echo '<form action="home.php" method="POST">';
    echo '<input type="submit" value="Home Page">';
    echo '</form>';

?>