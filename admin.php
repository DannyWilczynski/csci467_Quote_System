<?php
    include('config.php');



    echo "<form action=\"?\" method=\"POST\">";               
        echo 'Administrator:';
        echo "<input type=\"submit\" name=\"associate\" value=\"Associates\">";
        echo "<input type=\"submit\" name=\"quote\" value=\"Quotes\">";
    echo "</form>";

    echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';

    //VIEW/EDIT/DELETE ASSOCIATES
    if (isset($_POST['associate'])){  
        echo "<h4>Sales Associates</h4>";

        //VIEW ALL ASSOCIATES, GIVE OPTIONS
        $sql = "SELECT * FROM User WHERE is_associate = 1;";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();

        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        echo '<table border = 1>';
        echo '<tr><th>ID</th><th>Name</th><th>Commission</th><th>Modify</th>';
        foreach($rows as $row){
            echo '<tr>';
            echo '<td>'.$row['user_id'].'</td>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td> $'.$row['commission'].'</td>';
            echo '<td><button><a href="editAssociate.php?id='.$row['user_id'].'">Edit</a></button>';
            echo '<button><a href="deleteConfirmation.php?id='.$row['user_id'].'">Delete</button></td>';
            echo '</tr>';
        }
        echo '</table>';

        echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';

        //GIVE CREATE ASSOCIATE OPTION
        echo '<p>Create a new Sales Associate</p>';
        echo "<form action=\"?\" method=\"POST\">";
        //NAME
        echo '<label for="new_name">Name:</label>';
        echo '<input type="text" name="new_name">';
        echo '</br>';
        //PASSWORD
        echo '<label for="new_password">Password:</label>';
        echo '<input type="text" name="new_password">';
        echo '</br>';
        //COMMISSION
        echo '<label for="commission">Commission:</label>';
        echo '<input type="number" id="commission" name="commission" min="0">';
        echo '</br>';
        //ADDRESS
        echo '<label for="address">Address:</label>';
        echo '<input type="text" id="address" name="address">';
        echo '</br>';

        echo '<input type="submit" name="create_associate" value="Create">';
        
        echo '</form>';
        

    }   
    if (isset($_POST['quote'])){  
        echo "<h4>Quotes</h4>";

        if(!isset($_POST['associate_choice'])){
            $_POST['associate_choice'] = '';
        }
        if(!isset($_POST['status_choice'])){
            $_POST['status_choice'] = '';
        }
        if(!isset($_POST['customer_choice'])){
            $_POST['customer_choice'] = '';
        }
        if(!isset($_POST['date_min_choice'])){
            $_POST['date_min_choice'] = '';
        }
        if(!isset($_POST['date_max_choice'])){
            $_POST['date_max_choice'] = '';
        }
        
        

        //FILTER FORM
        echo '<form action="?" method="POST">';
        //STATUS
        echo '<label for=status_choice>Status</label>';
        echo '<select id="status_choice" name="status_choice">';
        echo '<option value="">None</option>';
        echo '<option value="Unfinalized">Unfinalized</option>';
        echo '<option value="Finalized">Finalized</option>';
        echo '<option value="Sanctioned">Sanctioned</option>';
        echo '<option value="Purchased">Purchased</option>';
        echo '</select>';
        //ASSOCIATE
        echo '<label for=associate_choice>Associates</label>';
        echo '<select id="associate_choice" name="associate_choice">';
        echo '<option value="">None</option>';

        $sql = "SELECT name FROM User WHERE is_associate = 1";
        foreach($db1->query($sql) as $row){
            echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
        }
        echo '</select>';


        //BY CUSTOMER
        echo '<label for=customer_choice>Customers</label>';
        echo '<select id="customer_choice" name="customer_choice">';
        echo '<option value="">None</option>';
        $sql = "SELECT customer FROM Quote";
        foreach($db1->query($sql) as $customer){
            $sql = "SELECT name, id FROM customers WHERE id = " . $customer['customer'];
            foreach($db2->query($sql) as $row){
                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
            }
        }
        echo '</select>';

        echo '</br>';

        //BY DATE
        echo '<label for=date_min_choice>Date</label>';
        echo '<select id="date_min_choice" name="date_min_choice">';
        echo '<option value=""></option>';

        $sql = "SELECT date FROM Create_Quote ORDER BY date ASC";
        foreach($db1->query($sql) as $row){
            echo '<option value="'.$row['date'].'">'.$row['date'].'</option>';
        }
        echo '</select>';

        echo '<label for=date_max_choice> to </label>';
        echo '<select id="date_max_choice" name="date_max_choice">';
        echo '<option value=""></option>';

        $sql = "SELECT date FROM Create_Quote ORDER BY date ASC";
        foreach($db1->query($sql) as $row){
            echo '<option value="'.$row['date'].'">'.$row['date'].'</option>';
        }
        echo '</select>';

        //SUBMIT BUTTON
        echo '<input type="submit" name="submit_customer_choice">';

        echo '<input type="hidden" name="quote" value="quote">';
        echo '</form>';

        if(($_POST['date_min_choice'] != '') and ($_POST['date_max_choice'] != '')){//FILTER ON DATE

            if($_POST['date_max_choice'] > $_POST['date_min_choice']){
                $max_choice = $_POST['date_max_choice'];
                $min_choice = $_POST['date_min_choice'];
            }
            else{
                $max_choice = $_POST['date_min_choice'];
                $min_choice = $_POST['date_max_choice'];
            }

            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id AND Create_Quote.date <= ? AND Create_Quote.date >= ?";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute(array($max_choice, $min_choice));

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        }

        elseif(($_POST['date_min_choice'] == '') and ($_POST['date_max_choice'] != '')){//FILTER ONLY MAX DATE
            $choice = $_POST['date_max_choice'];

            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id and Create_Quote.date <= \"".$choice.'"';
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
        }

        elseif (($_POST['date_min_choice'] != '') and ($_POST['date_max_choice'] == '')){//FILTER ONLY MIN DATE
            $choice = $_POST['date_min_choice'];

            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id and Create_Quote.date >= \"".$choice.'"';
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
        }

        elseif($_POST['customer_choice']!=''){//FILTER ON CUSTOMERS
            $choice = $_POST['customer_choice'];

            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id and Quote.customer = ".$choice;
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        }

        elseif($_POST['associate_choice']!=''){//FILTER ON ASSOCIATE

            $choice = $_POST['associate_choice'];
            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id and User.name = \"".$choice.'"';
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
        }

        elseif($_POST['status_choice'] != ''){ //FILTER ON STATUS

            $choice = $_POST['status_choice'];
            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id and Quote.status = \"".$choice.'"';
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        }
        else{ //DEFAULT VIEW
            $sql = "SELECT * FROM Quote, Create_Quote, User WHERE Quote.quote_id = Create_Quote.foreign_quote_id and User.is_associate = 1 and User.user_id = Create_Quote.associate_id";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

        }

        echo '<table border = 1>';
        echo '<tr><th>ID</th><th>Date</th><th>Associate</th><th>Customer</th><th>Price</th><th>Customer Email</th><th>Status</th>';
        foreach($rows as $row){
            echo '<tr>';
            echo '<td>'.$row['quote_id'].'</td>';
            echo '<td>'.$row['date'].'</td>';
            echo '<td>'.$row['name'].'</td>';

            $sql = "SELECT name FROM customers WHERE id = ".$row['customer']."";
            $prepared = $db2->prepare($sql);
            $success = $prepared->execute();
            $customer = $prepared->fetchALL(PDO::FETCH_ASSOC);
            foreach($customer as $customer){
                
                echo '<td>'.$customer['name'].'</td>';
                
            }

            echo '<td> $'.$row['price'].'</td>';
            echo '<td>'.$row['customerEmail'].'</td>';
            echo '<td>'.$row['status'].'</td>';
            echo '<td><button><a href="viewQuoteAdmin.php?id='.$row['quote_id'].'">View</a></button></td>';
            echo '</tr>';
            echo '</br>';
        }
        echo '</table>';
        
    } 
?>