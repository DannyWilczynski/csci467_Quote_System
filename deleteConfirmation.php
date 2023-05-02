<?php
    include('config.php');
    $id = $_GET['id'];
    session_start();

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

    echo'</br> Are you sure you want to delete the above Sales Associate?';
    echo '<form action="home.php" method="POST">';
    echo '<input type="submit" name="delete_associate_submit" value="Yes">';
    echo '<input type="submit" name="do_not_delete_associate_submit" value="No">';
    echo '<input type="hidden" name="delete_associate_id" value = "' . $id . '">';
    echo '</form>';
?>