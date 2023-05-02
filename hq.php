<?php
    include('config.php');
    echo "<link rel='stylesheet' href='style.css'>";
    echo "<h1 id='hq_header'>HQ</h1>";

    // Print finalized quotes
    $sql = "SELECT * FROM Quote WHERE status = 'Finalized' OR status = 'Sanctioned';";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute();

    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
    echo "<div id='hq_table'>";
    echo '<table border = 1>';
        echo '<tr><th>ID</th><th>Customer Name</th><th>Price</th><th>Customer Email</th><th>Quote Status</th><th></th>';
        foreach($rows as $row) {
            echo '<tr>';
            echo '<td>'.$row['quote_id'].'</td>';
            $sql = 'SELECT name FROM customers WHERE id = ' . $row['customer'];
            $prepared = $db2->prepare($sql);
            $success = $prepared->execute();

            $customers = $prepared->fetchALL(PDO::FETCH_ASSOC);
            foreach($customers as $cust){
                echo '<td>'.$cust['name'].'</td>';
            }
            
            echo '<td>'.$row['price'].'</td>';
            echo '<td>'.$row['customerEmail'].'</td>';
            echo '<td>'.$row['status'].'</td>';
            echo '<td><form action=select_quote.php method=POST style="display: inline;">';
            echo '<input type="submit" name="'.$row['quote_id'].'" value="View Quote">';
            echo '</form>';
            echo '<form action="delete_quote.php" method="POST" style="display: inline;">';
            echo '<input type="submit" name="'.$row['quote_id'].'" value="Delete Quote">';
            echo '</form></td>';
        }
    echo '</table>';

    echo '<button><a href="logout.php">Logout</a></button>';
    echo '</div>'
?>