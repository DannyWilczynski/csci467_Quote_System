<?php
    function process_order($quote_id, $associate, $cust_id, $price) {
        include('config.php');

        $url = 'http://blitz.cs.niu.edu/PurchaseOrder/';

        $data = array(
            'order' => $quote_id, 
            'associate' => $associate,
            'custid' => $cust_id, 
            'amount' => $price);
                
        $options = array(
            'http' => array(
                'header' => array('Content-type: application/json', 'Accept: application/json'),
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );

        // Add to external processing system
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // Grab name, date, and comission from JSON array
        $data = json_decode($result,true);
        $name = $data['name'];
        $date = $data['processDay'];
        $commission_pct = $data['commission'];

        $commission_pct = str_replace('%', '', $commission_pct);
        $commission_pct = (float)$commission_pct/100.00;

        // Update process comission percent
        $sql = "SELECT `AUTO_INCREMENT`
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = 'z1948125'
                AND   TABLE_NAME   = 'Processed';";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();
        $process_id = $prepared->fetch();

        $sql = "INSERT INTO Processed(process_name, process_day, commission_percent) VALUES (?, ?, ?);";
        $prepared = $db1->prepare($sql);
        $prepared->execute(array($name, $date, $commission_pct));

        $sql = "INSERT INTO Processed_Quote(foreign_quote_id, foreign_process_id) VALUES (?, ?);";
        $prepared = $db1->prepare($sql);
        $prepared->execute(array($_SESSION['QUOTE_ID'], $process_id[0]));


        // Update associate's commission
        $sql = "SELECT * FROM User WHERE user_id=?";
        $prepared = $db1->prepare($sql);
        $prepared->execute(array($associate));
        $user = $prepared->fetch();
        $commission = $commission_pct * $price;
        $commission = $commission + $user['commission'];

        $sql = "UPDATE User SET commission=? WHERE user_id=?";
        $prepared = $db1->prepare($sql);
        $prepared->execute(array($commission, $associate));
    }
?>