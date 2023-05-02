<?php
    session_start();
    
    include('config.php');

    echo '<style>
            input {
                padding:5px; color:#5b5b5b; width:150px; border:1px solid #9a9a9a;
                margin-top: 10px; width: 120px;
            }
            
            input[type="submit"]:hover {
                background-color:lightgrey;
            }

            p {
                font-family: sans-serif;
            }

            h3 {
                font-family: sans-serif;
            }
            
        </style>';

    echo "<h3>Email for Quote #".$_SESSION['QUOTE_ID']." sent</h3><br>";

    // Update quote to sanctioned
    $sql = "UPDATE Quote SET status='Sanctioned' WHERE quote_id=:id;";
    $prepared = $db1->prepare($sql);
    $prepared->execute(array('id' => $_SESSION['QUOTE_ID']));

    // Send email with quote details
    $sql = "SELECT * FROM Quote WHERE quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $quote = $prepared->fetch();

    // the message
    $msg = "QUOTE #".$_SESSION['QUOTE_ID']."    \n";
    

    $sql = "SELECT * FROM Quote_Item WHERE foreign_quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $items = $prepared->fetchAll(PDO::FETCH_ASSOC);

    foreach($items as $item) {
        $sql = "SELECT * FROM Item WHERE item_id = ?;";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute(array($item['foreign_item_id']));
        $i = $prepared->fetch();

        $msg .= " - ".$i['item_name']."\n";
    }

    $msg .= "\n\n ---- Price (with discounts applied): $".$quote['price'];

    echo '<p>' . $msg . '</p>';

    // use wordwrap() if lines are longer than 100 characters
    $msg = wordwrap($msg,100);

    // send email
    mail($quote['customerEmail'],"Your quote has been completed",$msg);

    // Redirect to hq page
    echo '<hr>';
    echo '<form action="hq.php">';
    echo '<input type="submit" value="Back to HQ">';
    echo '</form>';
?>