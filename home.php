<?php
    try { // if something goes wrong, an exception is thrown
        include('config.php');
        session_start();
    

        if(isset($_POST["name"])){ // save login info with session
            $_SESSION['username'] = $_POST['name'];
            $_SESSION['password'] = $_POST['password'];
        }



        

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
            
            $_SESSION['user_id'] = $row['user_id'];         //////////////////////////////I ADDED THIS THING YEAHH
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
            echo "Incorrect Login Information";
            echo "<form action=\"login.php\" method=\"post\">";
                echo "<button type=\"submit\"> Back to Login</button>";
            echo "</form>";
        }
        else{ // login was successful, display username and allow them to logout 
            echo "<h2>logged in as " .$_SESSION["username"]." </h2>";
            echo "<button><a href=\"logout.php\">Logout</a></button><br/> ";
            echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';
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
                print_r($_POST);
                
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
            include('hq.php');
        }
    }
    catch(PDOexception $e) { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }

?>


















<?php
/*
    try { // if something goes wrong, an exception is thrown
        include('config.php');
        session_start();
        header("logout.php");

        if(isset($_POST["name"])){ // save login info with session
            $_SESSION['username'] = $_POST['name'];
            $_SESSION['password'] = $_POST['password'];
        }
    

        if(isset($_POST["name"])){ // save login info cookie
            echo "uwu <br/>";
            setcookie ("name",$_POST['name'],time()+ 3600);
            setcookie ("password",$_POST['password'],time()+ 3600);
            header("Refresh:0");
            echo "Cookies Set Successfuly <br/>"; // Use to check if cookies are set 
        }
        

        /*
        if(isset($_COOKIE["name"])){
            echo "cookie name is ". $_COOKIE["name"]."<br/>";
            echo "cookie password is ". $_COOKIE["password"]."<br/><br/><br/>";    
        }
        else{
            echo "COOOKIES ARE NOT SET <br/>";
        }
        //




        // variables to dictate what is printed on the webpage
        $login = false;
        $associate = false;
        $admin = false;
        $hq = false;


        // check to see if user exists and if the password is correct 
        $sql = "SELECT * FROM User WHERE name = \"".$_COOKIE["name"]."\";";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();

        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);



        foreach ($rows as $row) {
            
            // if password is wrong, don't assign any role 
            if ($_COOKIE["password"] != $row['password']){
                $login = false;
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

        // send user back to the login page 
        if (!$login){
            echo "Incorrect Login Information<br/>";
            echo "<a href=\"login.php\">Login</a>";
        }
        else{ // login was successful, display username and allow them to logout 
            echo "<h2>logged in as " .$_COOKIE["name"]." </h2>";
            echo "<a href=\"logout.php\">Logout</a><br/> ";
            echo "___________________________________<br/><br/>";
        }
        if ($associate){ // display associate.php 
            include('associate.php');
        }
        if ($admin){// display admin.php 
            include('admin.php');
        }
        if ($hq){ // display hq.php 
            include('hq.php');
        }
    }
    catch(PDOexception $e) { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }
*/
?>