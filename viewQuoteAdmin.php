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

    echo '<style>
            input[type=submit] {
                padding:5px; color:#5b5b5b; width:150px; border:1px solid #9a9a9a;
                margin-top: 10px; width: 120px;
            }
            
            input[type=submit]:hover {
                background-color:lightgrey;
            }

            p {
                font-family: sans-serif;
            }

            h4 {
                font-family: sans-serif;
            }
            
        </style>';
    
    foreach($rows as $row){
        //SHOW CUSTOMER INFO

        $commission = 0;

        if($row['status'] == 'Ordered'){
            $sql = 'SELECT commission_percent FROM Processed, Processed_Quote WHERE Processed_Quote.foreign_quote_id = ?';
            $stmt = $db1->prepare($sql);
            $stmt->execute(array($_GET['id']));

            $commission_percent = $stmt->fetch();

            $commission = $row['price'] * $commission_percent[0];
            $commission = round($commission, 2);
        }
        
        //store some values
        $date = $row['date'];
        
        $email = $row['customerEmail'];


        $sql = 'SELECT * FROM customers WHERE id = ' .$row['customer'];
        $stmt = $db2->prepare($sql);
        $stmt->execute();
        $customer_fetch = $stmt->fetchALL(PDO::FETCH_ASSOC);

        foreach ($customer_fetch as $row) {
            echo '<p>Order From: ' . $row['name'] . '</br></br>';
            echo $row['street'] . '</br>';
            echo $row['city'] . '</br>';
            echo 'Contact: '. $row['contact'] . '</br>';
        }
        echo 'Date Fulfilled: ' . $date . '</br>';
        echo 'Commission: $'. number_format($commission, 2, '.', '') .'</br>';
        echo '</br></br>';

        //SHOW CUSTOMER EMAIL
        echo 'Email: ' . $email . '</p>';
        echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';
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
        echo '<p>' . $row['item_name'] . ' - $' . $row['price'] . '</p>';
    }

    echo '</br>';
    echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';

    //SHOW SECRET NOTES
    $sql = 'SELECT text_field FROM Note, Quote_Note
                WHERE Note.note_id = Quote_Note.foreign_note_id
                AND Quote_Note.foreign_quote_id = ?';

    $stmt = $db1->prepare($sql);
    $stmt->execute(array($quote_id));

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

    echo '<h4>Notes:</h4>';

    foreach($rows as $row){
        echo '<p>- ' . $row['text_field'] . '</p>';
    }

    echo '</br>';
    echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray"></br>';
    //SHOW TOTAL PRICE
    $sql = 'SELECT price FROM Quote WHERE quote_id = ' .$quote_id;
    $stmt = $db1->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        echo '<p>Amount: $' . $row['price'] . '</p>';
    } 

    echo '<form action="home.php" method="POST">';
    echo '<input type="submit" value="Home Page">';
    echo '</form>';

?>