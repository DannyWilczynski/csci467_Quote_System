<html><head>
    <title>Login Page</title>
</head></html>


</body>
</html>
<?php
    try { // if something goes wrong, an exception is thrown
        include('config.php');
    }
    catch(PDOexception $e) { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }


    if(!isset($_POST['name'])){
        echo "<div class=\"center\">"; //centers the entire first page
            echo "<h1>Login</h1>"; //header for main page
                echo "<form action=\"home.php\" method=\"POST\">"; //beginning of form
                    echo "<p>Enter Name: <input required type=\"text\" name=\"name\" </p>"; 
                    echo "<p>Enter Password: <input required type=\"password\" name=\"password\" </p>";
                    echo "<br/><input type=\"submit\" name=\"email-login\" value=\"Submit\">"; //submit button
                echo "</form>"; //end of form
        echo "</div>";
    }
    
?>