<?php
    include('config.php');
    session_start();

    $id = $_GET['id'];


    //GET ALL INFO FROM SELECTED ID
    $sql = 'SELECT * FROM User WHERE user_id = '.$id;
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute();

    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

    //DISPLAY IT
    echo '<table border = 1>';
    echo '<tr><th>ID</th><th>Name</th><th>Password</th><th>Commission</th><th>Address</th>';
    foreach($rows as $row){
        echo '<tr>';
        echo '<td>'.$row['user_id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['password'].'</td>';
        echo '<td> $'.$row['commission'].'</td>';
        echo '<td>'.$row['address'].'</td>';
        echo '</tr>';
        $name = $row['name'];
        $password = $row['password'];
        $commission = $row['commission'];
        $address = $row['address'];
    }
    echo '</table>';

    //EDIT IT
    echo '<form action="home.php" method="POST">';
    echo '<label for="new_name">Name: </label>';
    echo '<input type="text" id="new_name" name="new_name" value="'.$name.'" required>';
    echo '</br>';
    echo '<label for="new_password">Password: </label>';
    echo '<input type="text" id="new_password" name="new_password" value="'.$password.'" required>';
    echo '</br>';
    echo '<label for="new_commission">Commission: </label>';
    echo '<input type="text" id="new_commission" name="new_commission" value="'.$commission.'" required>';
    echo '</br>';
    echo '<label for="new_address">Address: </label>';
    echo '<input type="text" id="new_address" name="new_address" value="'.$address.'" required>';
    echo '</br>';
    echo '<input type="hidden" name="new_id" value='.$id.'>';
    echo '<input type="submit" name="update_submit" value="Submit">';
    echo '</form>';

    
?>