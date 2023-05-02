<?php
    try { // if something goes wrong, an exception is thrown
        include('config.php');
        session_start();
    

        if(isset($_POST["name"])){ // save login info with session
            $_SESSION['username'] = $_POST['name'];
            $_SESSION['password'] = $_POST['password'];
        }

        //print_r($_SESSION);
        //print_r($_POST);
        


        

        // variables to dictate what is printed on the webpage
        $login = false;
        $associate = false;
        $admin = false;
        $hq = false;


        // check to see if user exists and if the password is correct 
        $sql = "SELECT * FROM User WHERE name = \"".$_SESSION["username"]."\"";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();

        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);



        foreach ($rows as $row) {
            // if password is wrong, don't assign any role 
            $_SESSION['user_id'] = $row['user_id'];
            if ($_SESSION["password"] != $row['password']){
                break;
            }

            // username and password exist in database 
            $login = true;

            // assign associate role 
            if ($row['is_associate']){
                $associate = true;
            }

            // assign admin role 
            if ($row['is_admin']){
                $admin = true;
            }

            // assign hq role 
            if ($row['is_hq']){
                $hq = true;
            }

        }
        if (!$login){ //CHECK FOR LOGIN, THEN REDIRECT
            echo "<link rel='stylesheet' href='style.css'>";
            echo "<div id='incorrect_login'>";
                echo "<p>Incorrect Login Information</p>";
                echo "<form action=\"login.php\" method=\"post\">";
                    echo "<button type=\"submit\"> Back to Login</button>";
                echo "</form>";
            echo "</div>";
        }
        else{ // login was successful, display username and allow them to logout 
            echo "<link rel='stylesheet' href='style.css'>";
            echo "<div id='successful_login'>";
                echo "<h2>logged in as " .$_SESSION["username"]." </h2>";
                echo "<button><a href=\"logout.php\">Logout</a></button><br/> ";
                //echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';
            echo "</div>";
        }

        if ($associate){ // display associate.php 
            include('associate.php');
        }
        if ($admin){// display admin.php 
            include('admin.php');

            //CHOSE TO CREATE
            if(isset($_POST['create_associate'])){
                $sql = "INSERT INTO User(name, password, commission, address) VALUES(?, ?, ?, ?)";
                $prepared = $db1->prepare($sql);
                $success = $prepared->execute(array($_POST['new_name'], $_POST['new_password'], $_POST['commission'], $_POST['address']));
            }


            //USER CHOSE TO UPDATE
            if(isset($_POST['update_submit'])){
                $sql = 'UPDATE User SET name = ?, password = ?, commission = ?, address = ? WHERE user_id = ? ';
                $prepared = $db1->prepare($sql);
                $prepared->execute(array($_POST['new_name'], $_POST['new_password'], $_POST['new_commission'], $_POST['new_address'], $_POST['new_id']));
                
            }

            //USER CHOSE TO DELETE
            if(isset($_POST['delete_associate_submit'])){

                //DELETE WHERE USER'S ID IS FOREIGN KEY
                $sql = 'DELETE FROM Create_Quote WHERE associate_id = ' . $_POST['delete_associate_id'];
                $db1->prepare($sql)->execute();
                
                //DELETE USER
                $sql = 'DELETE FROM User WHERE user_id = ' . $_POST['delete_associate_id'];
                $db1->prepare($sql)->execute();
            }
        }
        if ($hq){ // display hq.php 
            //include('hq.php');
            header("Location: hq.php");
        }
    }
    catch(PDOexception $e) { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }

?>