<html>
    <link rel="stylesheet" href="style.css">

    <style>
        .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }


        .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
        font-family: sans-serif;
        }


        .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        }

        .close:hover,
        .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
        }




    </style>
        

    <?php
    /*******************************************************************************************************************************
     *                                                                                                                             *
     * This section checks what information the user inputed and creates a new quote,                                              *
     * inserting data into the appropriate tables                                                                                  *
     *                                                                                                                             *
     *******************************************************************************************************************************/
    
        // if the a new quote was created, set status to unfinalized or finalized
        $status = "none";
        if(isset($_POST["CreateQuote"])){
            if ($_POST["CreateQuote"] == "Create Quote"){
                $status = "Unfinalized";
            } 
        }
        else if(isset($_POST["FinalizeQuote"])){
            if ($_POST["FinalizeQuote"] == "Finalize Quote"){
                $status = "Finalized";
            } 
        } // if a previous quote was edited, set status to edit or editfinalizd
        else if(isset($_POST["editQuote"])){
            if ($_POST["editQuote"] == "Save Changes"){
                $status = "Edit";
            } 
        }
        else if(isset($_POST["editFinalizeQuote"])){
            if ($_POST["editFinalizeQuote"] == "Finalize Quote"){
                $status = "editFinalized";
            } 
        }

        

        // if the form was submitted, add the new input into the database 
        if( $status == "Unfinalized" or $status == "Finalized" ){
 
            // INSERT QUOTE INTO QUOTE TABLE 
            $sql = "INSERT INTO Quote(customer, price, customerEmail, status) VALUES  (".($_POST["CustID"]). ", 0 , \"".$_POST["email"]."\", \"".$status."\");";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();


            // Get the new quotes id that was just created 
            $quote_id = 0;            
            $sql = "SELECT LAST_INSERT_ID()";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $new_quote_id = $prepared->fetchALL(PDO::FETCH_ASSOC);
            
            // store the quote id as $quote_id
            foreach($new_quote_id as $row){
                $quote_id = $row['LAST_INSERT_ID()'];
            }
            

            // Add associate_id and the new $quote_id into the Create_Quote table
            $sql = "INSERT INTO Create_Quote (associate_id, foreign_quote_id, date) VALUES (".$_SESSION['user_id'].", ".$quote_id.", \"".date("Y/m/d")."\");";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();


            // Add all of the secret notes to the Note table 
            $secret_counter = 0;
            while (isset($_POST["new_secret_".$secret_counter])){// this loops through all of the secret notes whether or not they were deleted 

                if (isset($_POST["trash_secret_".$secret_counter])){// if trash option was selected, dont add it 
                    //echo "Throwing away note " . $_POST["new_secret_".$secret_counter] ."<br>";

                }
                else{// if trash option was not selected, add the secret to the Note Table  
                    
                    // INSERT SECRET INTO NOTE TABLE 
                    $sql = "INSERT INTO Note(text_field) VALUES (\"". $_POST["new_secret_".$secret_counter] ."\");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();


                    // Get the noteid of the Note that was just created 
                    $noteid = 0;
                    $sql = "SELECT LAST_INSERT_ID()";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    $new_quote_id = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($new_quote_id as $row){ // store the Note for future sql statements 
                        $note_id= $row['LAST_INSERT_ID()'];
                    }


                    // INSERT NOTEID AND QUOTE ID INTO QUOTENOTE 
                    $sql = "INSERT INTO Quote_Note(foreign_note_id, foreign_quote_id) VALUES (".$note_id. ", ".$quote_id.");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                }// end of "else", trash option was not selected 

                $secret_counter = $secret_counter + 1; // increment $secret_counter then return to the top of the loop for the next note
            
            }// end of while loop, all Secret notes have been entered 


            
        
            //Insert all of the items to the Item table 
            $item_counter = 0;
            $subtotal = 0;

            while (isset($_POST["new_item_".$item_counter])){ // loop through all of the items that were created 
                

                if (isset($_POST["trash_item_".$item_counter])){// dont add an item if trash option was selected 
                    //echo "Throwing away item ".$_POST["new_item_".$item_counter]."<br/>";
                }
                else{// if trash was not selected, insert the item to the Item table

                    if (!empty($_POST["New_item_price_".$item_counter])){ // this checks if the price box of an item was left blank 
                        $subtotal = $subtotal + $_POST["New_item_price_".$item_counter]; // calculate the subtotal before discounts are applied 
                    }


                    // INSERTS EACH NON TRASH ITEM 
                    $sql = "INSERT INTO Item (price, item_name) VALUES (".$_POST["New_item_price_".$item_counter].", \"".$_POST["new_item_".$item_counter]."\");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    
                    // get the new item id that was just created 
                    $item_id = 0;
                    $sql = "SELECT LAST_INSERT_ID()";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    $new_item_id = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($new_item_id as $row){ // store the newly created item id 
                        $item_id= $row['LAST_INSERT_ID()'];
                    }


                    // INSERT quote id AND item id INTO QUOTE Item TABLE 
                    $sql = "INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (".$quote_id.", ".$item_id.");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                } // end of "else", an item was added as long as it wasn't marked as trash 

                $item_counter = $item_counter + 1; // increment counter before checking next item 

            } // end of item loop, all items have been entered to Item table 


            $discount = 0;
            // calculate the discount 
            if(isset($_POST["discount"])&& !empty($_POST["discount_amount"])){
                
                if ($_POST["discount"] == "dollar"){ // if discount was a dollar, subtract that ammount from the total 
                    $subtotal = $subtotal - $_POST["discount_amount"];
                }
                else if ($_POST["discount"] == "percent"){ // if discount was a % calculate the discount and subtract from total
                    $discount = $subtotal * $_POST["discount_amount"];
                    $discount = $discount/100;
                    $subtotal = $subtotal - $discount;
                }
            } // end of discount checking 

            // maximum discount is free, make sure theres no negative totals
            if ($subtotal < 0){
                $subtotal = 0;
            }




            // update the quote price to reflect the computed total 
            $sql = "UPDATE Quote SET price=".$subtotal." WHERE quote_id=".$quote_id.";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            echo "<h2 style='font-family: sans-serif; font-weight: bold; margin-left: 2%;'>Quote Created Successfully!</h2>";
        }// end of if ("Unfinalized" or "Finalized" )statement 





        /****************************************************************************************************
         *                                                                                                  *
         * This section updates quotes in the database after they have already been created                 *
         *                                                                                                  *
         ****************************************************************************************************/
        else if( $status == "Edit" or $status == "editFinalized" ){
            $quote_id = $_POST['quote_id'];        
            
            // update Quote_Note table entries
            $sql = "SELECT * FROM Quote_Note WHERE foreign_quote_id = \"".$quote_id."\";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

             // updates or deletes old secret notes 
            foreach($rows as $row){
                $note_id = $row['foreign_note_id'];

                // if delete checkbox was selected, delete the quote 
                if ( isset($_POST["old_trash_secret_".$note_id])){

                    // delete quote_note first, as it usees a foreign key
                    $sql="DELETE FROM Quote_Note WHERE foreign_note_id=".$note_id.";";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                    // then delete from the note table 
                    $sql="DELETE FROM Note WHERE note_id=".$note_id.";";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                }
                else{// if delete was not selected, update the text in the note 
                    $sql = "UPDATE Note SET text_field=\"".$_POST["old_secret_".$note_id]."\" WHERE note_id=".$note_id.";";

                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                }
                    
            }


            // adds new secrets to the db
            $secret_counter = 0;
            while (isset($_POST["new_secret_".$secret_counter])){// this loops through all of the secret notes whether or not they were deleted 

                if (isset($_POST["trash_secret_".$secret_counter])){// if trash option was selected, dont add it 
                    //echo "Throwing away note " . $_POST["new_secret_".$secret_counter] ."<br>";

                }
                else{// if trash option was not selected, add the secret to the Note Table  
                    
                    // INSERT SECRET INTO NOTE TABLE 
                    $sql = "INSERT INTO Note(text_field) VALUES (\"". $_POST["new_secret_".$secret_counter] ."\");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();


                    // Get the noteid of the Note that was just created 
                    $noteid = 0;
                    $sql = "SELECT LAST_INSERT_ID()";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    $new_quote_id = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($new_quote_id as $row){ // store the Note for future sql statements 
                        $note_id= $row['LAST_INSERT_ID()'];
                    }


                    // INSERT NOTEID AND QUOTE ID INTO QUOTENOTE 
                    $sql = "INSERT INTO Quote_Note(foreign_note_id, foreign_quote_id) VALUES (".$note_id. ", ".$quote_id.");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                }// end of "else", trash option was not selected 

                $secret_counter = $secret_counter + 1; // increment $secret_counter then return to the top of the loop for the next note
            
            }// end of while loop, all Secret notes have been entered


            // update old items from item table and add to subtotal 
            $sql = "SELECT * FROM Quote_Item WHERE foreign_quote_id = \"".$quote_id."\";";

            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

            // loop through each item in a quote 
            foreach($rows as $row){
                
                $item_id = $row['foreign_item_id'];

                // if delete item was selected, delete the old item 
                if ( isset($_POST["old_trash_item_".$item_id])){
                    
                    $sql="DELETE FROM Quote_Item WHERE foreign_item_id=".$item_id.";";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                    $sql="DELETE FROM Item WHERE item_id=".$item_id.";";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                }
                else{ // else, update the name and price of the item 
                    $sql = "UPDATE Item SET item_name=\"".$_POST["old_item_".$item_id]."\", price=\"".$_POST["old_price_".$item_id]."\",  WHERE item_id=".$item_id.";";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                }
            }


            //Insert all of the items to the Item table 
            $item_counter = 0;
            $subtotal = 0;

            while (isset($_POST["new_item_".$item_counter])){ // loop through all of the items that were created 
                

                if (isset($_POST["trash_item_".$item_counter])){// dont add an item if trash option was selected 
                    //echo "Throwing away item ".$_POST["new_item_".$item_counter]."<br/>";
                }
                else{// if trash was not selected, insert the item to the Item table

                    if (!empty($_POST["New_item_price_".$item_counter])){ // this checks if the price box of an item was left blank 
                        $subtotal = $subtotal + $_POST["New_item_price_".$item_counter]; // calculate the subtotal before discounts are applied 
                    }


                    // INSERTS EACH NON TRASH ITEM 
                    $sql = "INSERT INTO Item (price, item_name) VALUES (".$_POST["New_item_price_".$item_counter].", \"".$_POST["new_item_".$item_counter]."\");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    
                    // get the new item id that was just created 
                    $item_id = 0;
                    $sql = "SELECT LAST_INSERT_ID()";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();
                    $new_item_id = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($new_item_id as $row){ // store the newly created item id 
                        $item_id= $row['LAST_INSERT_ID()'];
                    }


                    // INSERT quote id AND item id INTO QUOTE Item TABLE 
                    $sql = "INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (".$quote_id.", ".$item_id.");";
                    $prepared = $db1->prepare($sql);
                    $success = $prepared->execute();

                } // end of "else", an item was added as long as it wasn't marked as trash 

                $item_counter = $item_counter + 1; // increment counter before checking next item 

            } // end of item loop, all items have been entered to Item table



            $subtotal = $_POST['Hidden_Price'];
            // maximum discount is free, make sure theres no negative totals
            if ($subtotal < 0){
                $subtotal = 0;
            }


            // change $status to match what the final status of a quote will be 
            if ($status == "Edit"){
                $status = "Unfinalized";
            }
            else{
                $status = "Finalized";
            }


            // update quote to new price, email and status 
            $sql = "UPDATE Quote SET price=".$subtotal.", customerEmail=\"". $_POST['email'] ."\", status=\"".$status."\" WHERE quote_id=".$quote_id.";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();

            echo "<h2 style='font-family: sans-serif; font-weight: bold; margin-left: 2%;'>Quote Updated Successfully!</h2>";

        }

    // end of checking user input and creating/editing a quote
    ?>






    <?php
        // store the customer info for the dropdown menu
        $sql = "SELECT * FROM customers;";
        $prepared = $db2->prepare($sql);
        $success = $prepared->execute();
        $Customers = $prepared->fetchALL(PDO::FETCH_ASSOC);

        // use CustomerArray to display User info such as address and contact informaiton 
        echo "<script>var CustomerArray = " . json_encode($Customers).";</script>"; 

    ?>

    <div id="associate_select">
    <h3>Create a new quote</h3>

    Customer Name:
    <select name="Customer Name" id="CustomerSelect" name="CustomerSelect" required>
        <option value="none" selected disabled hidden> Select an Option</option>

    <?php
        // each customer is listed as an option in the dropdown menu
        foreach ($Customers as $row) {
            echo "<option value=\"".$row["id"] . "\">" . $row["name"]."</option>";  
        }
    ?>
    </select>

    
    <button id="myBtn">Select Customer</button>
    </div>
        
    <!-- Opens a modal when select customer button is pressed  -->
    <div id="myModal" class="modal">

        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <!-- output changes depending on what the user selected-->
                <h2 class="output" id="name_label"></h2>
                <p class="output" id="addr_label"></p>
                <p class="output" id="city_label"></p>
                <p class="output" id="contact_label"></p>
            </div>

            <div class="modal-body">
                <br/><br/>

                <form action="?" method="POST">
                    Email:
                    <input type="email" name="email" maxlength="50" placeholder="John@quote.com" required>
                    <br/><br/>
                    

                    Line Items: 
                    <input onclick="addItemBox()" type="button" id="NewItem" value="New Item">
                    <!-- each time the button is pressed, a new textbox appears for items-->
                    <div id="LineItemTextBoxes"></div>
                    <br/>


                    Secret Notes: 
                    <input onclick="addSecretBox()" type="button" id="NewSecret" value="New Secret">
                    <!-- when this button is pressed, a new textboox appears for secret notes -->
                    <div id="SecretNoteTextBoxes"></div>
                    <br/><br/>
                

                    Discount:
                    <input type="number" id="discount_amount" name = "discount_amount" step=".01" placeholder="0" >
                    <input type="radio" id="discount_dollar" name="discount" value="dollar">Dollar 
                    <input type="radio" id="discount_percent" name="discount" value="percent">Percent 
                    <p class = "output" id="output1" ></p>
                    <input type="button" id="btn1" value="Calculate Total">
                    
                    
                    <br/><br/><br/><br/><br/>
                    <input type="hidden" name="CustID" id="CustID" >
                    <input type="submit" name="CreateQuote" value="Create Quote">
                    <input type="submit" name="FinalizeQuote" value="Finalize Quote"><br/>
                
                </form>

            </div>
            
        </div>

    </div>

    


    <?php // list current quotes / allow edits 

        //LIST CURRENT QUOTES
        echo '<div id="associate_quote_table">';
        echo '<br/><br/><h2>List of Current Quotes</h2>';

        $user_id = $_SESSION['user_id'];

        // Print out all of a users current quotes 
        $sql = "SELECT * FROM Quote, Create_Quote WHERE Quote.quote_id = Create_Quote.foreign_quote_id and Create_Quote.associate_id = ".$user_id;
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();
        $quotes = $prepared->fetchALL(PDO::FETCH_ASSOC);

        echo "<form action=\"associate_edit.php\" method=\"POST\">";


        echo "<table border=\"1\" cellpadding=\"4\" style=\"border-collapse:collapse;\">";
        echo '<tr><th>id</th><th>Name</th><th>Price</th><th>Contact Email</th><th>Status</th><th>Edit</th></tr>';

        // loop through each quote 
        foreach($quotes as $quote){
            $sql = "SELECT name FROM customers WHERE id = ".$quote['customer']."";
            $prepared = $db2->prepare($sql);
            $success = $prepared->execute();
            $customer = $prepared->fetchALL(PDO::FETCH_ASSOC);
            
            $max_quoteid = 0;

            // records for each value in the Quote table 
            foreach($customer as $row){
                echo '<tr>';
                echo '<td>'.$quote['quote_id'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                echo '<td> $'.$quote['price'].'</td>';
                echo '<td>'.$quote['customerEmail'].'</td>';
                echo '<td>'.$quote['status'].'</td>';

                // edit button for unfinalized quotes 
                if($quote['status'] == 'Unfinalized'){
                    echo "<td><input type=\"submit\" id\"edit\" name=\"quote_".$quote['quote_id']."\" value=\"Edit Quote\" ></input></td>";
                    $_SESSION["max_quoteid"] = $quote['quote_id'];
                }
                else {
                    echo "<td></td>";
                }
                echo '</tr>';
            }
            
        }
        echo "</table>";
        $max_quoteid = isset($max_quoteid) ? $max_quoteid : '';
        echo "<input type=\"hidden\" name=\"max_quoteid\" value=\"".$max_quoteid."\"/>";
        echo "</form>";
        echo "</div>";

    ?>







    <script>
        //const txt1 = document.getElementById('tboxy');
        const btn1 = document.getElementById('btn1'); // check for calculate total button 
        const out1 = document.getElementById('output1'); // calculate total output 

        const out2 = document.getElementById('name_label'); // customer name 
        const out3 = document.getElementById('addr_label'); // customer address
        const out4 = document.getElementById('city_label'); // customer city 
        const out5 = document.getElementById('contact_label'); // customer contact 
        
        var btn = document.getElementById("myBtn"); // select customer button (off until customer selected )
        var showDiscDollar = document.getElementById("discount_dollar"); // dollar discount 
        var showDiscpercent = document.getElementById("discount_percent"); // percent discount 
        var modal = document.getElementById("myModal"); // the modal being displayed 
        var span = document.getElementsByClassName("close")[0];// span to close the modal 
        

        // button to open modal is disabled until a customer is selected 
        document.getElementById("myBtn").disabled = true;

        // the discount textbox is disabled until dollar or percent are checked 
        document.getElementById("discount_amount").disabled = true;

        
        btn1.addEventListener('click',CalculateSubtotal); // calculate subtotal when btn pressed 
        CustomerSelect.addEventListener('change',ShowName); // display name, address, and contact info after customer is selected
        CustomerSelect.addEventListener('change', ModalState); // enable the "select customer" button after customer selected
        CustomerSelect.addEventListener('change', CalculateSubtotal);// display Total: $0 after customer is selected 
        discount_dollar.addEventListener('change', DiscountState); // enable discount textbox after radio is selected
        discount_percent.addEventListener('change', DiscountState); // enable discount textbox after radio is selected 

        var itemCounter = 0; // number of items added 
        var secretCounter = 0; // number of secret notes added 

        // if the page is refreshed, do not submit another form and create a duplicate quote 
        if (window.history.replaceState){
            window.history.replaceState( null, null, window.location.href)/////////////////////////////////////////////////////////////////////////////////////////////MAKE SURE TO UNCOMMENT THIS OUT SO REFRESH DOESNT SENED MULTIPLE THINGY BOIS YEAH SADFASFDASFASFASFAS DFADS SAF SADF ASDF ASDF ASDF ASDA
        }

        /**
         * Calculates the subtotal while a user is inputting
         * new items and discounts in the modal
         */
        function CalculateSubtotal(){ 
            var subTotal = 0;
            var Total = 0;
            var dollar_discount = 0;
            var percent_discount = 0;

            var items;       
            var checkTrash;   
              
            // calculate total price before discounts 
            for (let i = 0; i < itemCounter; i++){ 
                items = "New_item_price_" + i;
                checkTrash = "trash_item_" + i;
                
                // if an item is deleted, don't include it in the subtotal 
                if (!document.getElementById(checkTrash).checked){
                    subTotal = subTotal + +(document.getElementById(items).value);
                }
            }

            // if discount dollar is selected, get the dollar discount ammount 
            if (document.getElementById('discount_dollar').checked){
                dollar_discount = document.getElementById('discount_amount').value;
                Total = subTotal-dollar_discount;
            }
            // if discount percent is selected, find the percent discount ammount 
            else if (document.getElementById('discount_percent').checked){
                percent_discount = document.getElementById('discount_amount').value;
                percent_discount = subTotal * percent_discount;
                percent_discount = percent_discount / 100;
                Total = subTotal-percent_discount;
            }
            else{ // no discount was selected, total is the subtotal 
                Total = subTotal;
            }

            // ensure that total can never be a negative number 
            if (Total < 0){
                Total = 0;
            }
            
            // show the total with two decimal points 
            Total = Total.toFixed(2);
            out1.innerHTML = "Total: $" + String(Total);
        }// end of function Calculate Subtotal()


        
        //Enables the button that opens up the modal
        function ModalState(){
            document.getElementById("myBtn").disabled = false;   
        }


        
        //Enables the number input for a discount after a radio option is selected 
        function DiscountState(){
            document.getElementById("discount_amount").disabled = false;
        }
        

        /**
         * Fills in the selected customer's name, street, city and contact
         * when the modal is opened 
         */
        function ShowName(){
            // find the stored customerid
            var result = CustomerArray.find(obj => obj.id == CustomerSelect.value);
            document.getElementById("CustID").value = CustomerSelect.value; // gets the customer id 

            // display the customer's information 
            out2.innerHTML = result.name; 
            out3.innerHTML = result.street;
            out4.innerHTML = result.city;
            out5.innerHTML = result.contact;
        }


        /**
         * adds a new item text box, item price box, and a delete checkbox every time a 
         * button is pressed. 
         */
        function addItemBox(){

            // create a textbox with a unique name
            var textbox = document.createElement("input");
            textbox.type = "text";
            textbox.required = "true";
            textbox.name = "new_item_" + itemCounter;
            textbox.placeholder="Item Name";
            textbox.setAttribute("maxlength","20");

            // add the textbox to the modal 
            var container = document.getElementById("LineItemTextBoxes");
            container.appendChild(textbox);


            // create a number box with a unique name
            var numbox = document.createElement("input");
            numbox.type = "number";
            numbox.required="true";
            numbox.name = "New_item_price_" + itemCounter;
            numbox.id = "New_item_price_" + itemCounter;
            numbox.step=".01";
            numbox.placeholder="$000.00";
            numbox.min="0";
            numbox.max="99999.99";
            

            // add the number box into the modal
            var container = document.getElementById("LineItemTextBoxes");
            container.appendChild(numbox);


            // create a checkbox to indicate whether or not to save the item 
            var trashtext = document.createElement("input");
            trashtext.type = "checkbox";
            trashtext.name = "trash_item_"+itemCounter;
            trashtext.id = "trash_item_"+itemCounter;

            // add the checkbox to the modal
            var container = document.getElementById("LineItemTextBoxes");
            container.appendChild(trashtext);

            // add a label to indicate what the trash textbox does 
            var delete_item = document.createTextNode("Delete Item");
            var container = document.getElementById("LineItemTextBoxes");
            container.appendChild(delete_item);

            
            // add a break inbetween each new textbox 
            container.appendChild(document.createElement("br"));
            
            // increment the item counter so the next item box has a unique name 
            itemCounter++;

        } // end of addItemBox()

        
        /**
         * adds a textbox for the secret notes as well as a delete checkbox 
         * every time a button is pressed 
         */
        function addSecretBox(){

            // create a textbox with a unique name 
            var secretbox = document.createElement("input");
            secretbox.type = "text";
            secretbox.name = "new_secret_" + secretCounter;
            secretbox.placeholder="Secret Message";
            secretbox.setAttribute("maxlength","250");

            // add the secret textbox to the page 
            var container = document.getElementById("SecretNoteTextBoxes");
            container.appendChild(secretbox);


            // create a checkbox to indicate whether or not to save the note
            var trashsecret = document.createElement("input");
            trashsecret.type = "checkbox";
            trashsecret.name = "trash_secret_"+secretCounter;
           
            // add the checkbox to the page 
            var container = document.getElementById("SecretNoteTextBoxes");
            container.appendChild(trashsecret);


            // add a label to indicate what the checkbox does 
            var delete_secret = document.createTextNode("Delete Secret");
            var container = document.getElementById("SecretNoteTextBoxes");
            container.appendChild(delete_secret);

            // add a newline before the next secret textbox 
            container.appendChild(document.createElement("br"));

            // increment secret counter so each note will get a new name 
            secretCounter++;
            
        }// end of addSecretBox() function 


        // When the user clicks on the button, open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // close the modal when the user clicks on <span>
        span.onclick = function() {
            modal.style.display = "none";
        }

        // close the modal when the user clicks outside of the modal 
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

    </script>

</html>



