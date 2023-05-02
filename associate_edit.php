<?php
    session_start();
    echo "<h2>logged in as " .$_SESSION["username"]." </h2>";
    echo "<button><a href=\"logout.php\">Logout</a></button><br/> ";
    echo '<hr style="height:2px;border-width:0;color:gray;background-color:gray">';
?>


<html>
    <h3>Editing an quote</h3>

    
    <?php
        include('config.php');

        // found is used to find the id of a quote 
        $found = false;
        $quote_id = 0;
        
        // this loop finds the id of the quote that was selected to be edited 
        while (!$found && $quote_id <=  $_SESSION["max_quoteid"] ){
            $quote_id= $quote_id + 1;
            if (isset($_POST['quote_'.$quote_id])){
                $found = true;
            }

        }

        
        // get information from quote table 
        $sql = "SELECT * FROM Quote WHERE quote_id=".$quote_id.";";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();
        $quote = $prepared->fetchALL(PDO::FETCH_ASSOC);
        
        // get quote price, email and customer id 
        foreach($quote as $row){
            $customer_id = $row['customer'];
            $price = $row['price'];
            $customer_email = $row['customerEmail'];
        }
        


        // get customer info from CUSTOMER TABLE 
        $sql = "SELECT * FROM customers WHERE id=".$customer_id .";";
        $prepared = $db2->prepare($sql);
        $success = $prepared->execute();
        $Customers = $prepared->fetchALL(PDO::FETCH_ASSOC);
        foreach($Customers as $row){
            echo "<h2>" . $row['name']."</h2>";
            echo "<p>" . $row['street']."</p>";
            echo "<p>" . $row['city']."</p>";
            echo "<p>" . $row['contact']."</p>";
        }




        echo "<form action=\"home.php\" method=\"POST\">"; 
            echo "<input type=\"hidden\" name=\"quote_id\" value=\"".$quote_id."\">";

            //QUOTE TABLE         
            $sql = "SELECT * FROM Quote WHERE quote_id=".$quote_id.";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $quote = $prepared->fetchALL(PDO::FETCH_ASSOC);
            $price=0;
            
            foreach($quote as $row){ // get customer id and price 
                $customer_id = $row['customer'];
                $price = $price + $row['price'];
                $customer_email = $row['customerEmail'];
            }
            echo "<input type=\"hidden\" id=\"QuotePrice\" value=\"".$price."\">";
            echo "<br/><br/>";
            
            echo "Email: ";
            echo "<input type=\"email\" name=\"email\" maxlength=\"50\" placeholder=\"John@quote.com\" value=".$customer_email." required><br/><br/>";
                        
            echo "Line Items: ";     
            echo "<input onclick=\"addItemBox()\" type=\"button\" id=\"NewItem\" value=\"New Item\"><br/>";


            // QUOTE_ITEM TABLE 
            $sql = "SELECT * FROM Quote_Item WHERE foreign_quote_id=".$quote_id.";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $quoteitem = $prepared->fetchALL(PDO::FETCH_ASSOC);

            // for each item in the quote, add a textbox to allow users to edit or delete previous items 
            foreach($quoteitem as $row){
   
                // ITEM TABLE 
                $sql = "SELECT * FROM Item WHERE item_id=".$row['foreign_item_id'].";";
                $prepared = $db1->prepare($sql);
                $success = $prepared->execute();
                $item = $prepared->fetchALL(PDO::FETCH_ASSOC);

                // create a textbox for every old item
                foreach($item as $items){
                    echo "<input type=\"text\" required=\"true\" id=\"old_item_".$items['item_id']."\" name=\"old_item_".$items['item_id']."\" value=\"".$items['item_name']. "\" placeholder=\"Item Name\" maxlength=20>";
                    echo "<input class=\"item_price\" required=\"true\" type=\"number\" name=\"old_price_". $items['item_id']."\" value=\"".$items['price']."\" id=\"old_price_". $items['item_id']. "\" step=\".01\" placeholder=\"$000.00\" min=\"0\" max=\"99999.99\">";
                    echo "<input class=\"item_check\" type=\"checkbox\" name=\"old_trash_item_". $items['item_id']."\" id=old_trash_item_". $items['item_id']."\">";
                    echo "Delete Item";
                }
                echo "<br/>";
            }

        
            // div used to create new texboxes when the button is pressed 
            echo "<div id=\"LineItemTextBoxes\"></div>";
            echo "<br/>";


            echo "Secret Notes: ";
            echo "<input onclick=\"addSecretBox()\" type=\"button\" id=\"NewSecret\" value=\"New Secret\"><br/>";


            // QUOTE_NOTE TABLE
            $sql = "SELECT * FROM Quote_Note WHERE foreign_quote_id=".$quote_id.";";
            $prepared = $db1->prepare($sql);
            $success = $prepared->execute();
            $quotenote = $prepared->fetchALL(PDO::FETCH_ASSOC);

            // loops through every existing quote 
            foreach($quotenote as $row){ 
                $sql = "SELECT * FROM Note WHERE note_id=".$row['foreign_note_id'].";";
                $prepared = $db1->prepare($sql);
                $success = $prepared->execute();
                $note = $prepared->fetchALL(PDO::FETCH_ASSOC);

                foreach($note as $notes){// for each note in a quote, create a textbox with the old note values already there 
                    echo "<input type=\"text\" value=\"".$notes['text_field']."\" name=\"old_secret_".$notes['note_id']."\" placeholder=\"Secret Message\" maxlength=250>";
                    echo "<input type=\"checkbox\" name=\"old_trash_secret_".$notes['note_id']."\">";
                    echo "Delete Secret";
                }
                echo "<br/>";
            }
            // this is where new textboxes will appear 
            echo "<div id=\"SecretNoteTextBoxes\"></div>";
    
    ?>
                                            
            <br/><br/>

            Discount:
            <input type="number" id="discount_amount" name = "discount_amount" step=".01" placeholder="0" >
            <input type="radio" id="discount_dollar" name="discount" value="dollar">Dollar 
            <input type="radio" id="discount_percent" name="discount" value="percent">Percent 
            <p class = "output" id="output1" ></p>
            <input type="button" id="btn1" value="Calculate Total">
            
            
            <br/><br/><br/><br/><br/>
            <input type="hidden" name="CustID" id="CustID" >
            <input type="hidden" name="Hidden_Price" id="Hidden_Price" value="0">
            
            <input type="submit" id="editQuote" name="editQuote" value="Save Changes">
            <input type="submit" id="editFinalizeQuote" name="editFinalizeQuote" value="Finalize Quote"><br/>
            <input type="submit" name="Discard" value="Discard Changes">
                    
        </form>


    <script>
        
        const btn1 = document.getElementById('btn1'); // check for calculate total button 
        const out1 = document.getElementById('output1'); // calculate total output 

        //var btn = document.getElementById("myBtn"); // select customer button (off until customer selected )
        var showDiscDollar = document.getElementById("discount_dollar"); // dollar discount 
        var showDiscpercent = document.getElementById("discount_percent"); // percent discount 

        // the discount textbox is disabled until dollar or percent are checked 
        document.getElementById("discount_amount").disabled = true;
        
        btn1.addEventListener('click',CalculateSubtotal); // calculate subtotal when btn pressed 

        discount_dollar.addEventListener('change', DiscountState); // enable discount textbox after radio is selected
        discount_percent.addEventListener('change', DiscountState); // enable discount textbox after radio is selected 
        editQuote.addEventListener('click', CalculateSubtotal);
        editFinalizeQuote.addEventListener('click', CalculateSubtotal);

        var itemCounter = 0; // number of items added 
        var secretCounter = 0; // number of secret notes added 
        var pricevalue = 0;
        var olditems = [];
        let oldcheck = [];
        let oldItemsTotal = 0;
        let oldDiscount = 0;
        
        first_time = true;
        

        window.onload=CalculateSubtotal();
        

        /**
         * Calculates the subtotal while a user is inputting
         * new items and discounts in the modal
         */
        function CalculateSubtotal(){ 
            
            
            if (first_time){ // find the discount in dollars if this is the first time the page loads

                oldTotal = document.getElementById('QuotePrice').value; // add the old total w/ discounts applied
                first_time = false;
                
                olditems = document.getElementsByClassName("item_price");
                oldcheck = document.getElementsByClassName("item_check");
                for (let i = 0; i < olditems.length; i++){
                    oldItemsTotal = oldItemsTotal + +olditems[i].value;
                }

                oldDiscount = oldItemsTotal - oldTotal;
            }
            else{ // if this is not the first time the page loads, calculate the total of the old items 
                oldTotal = 0;
                for (let i = 0; i < olditems.length; i++){
                    if (!oldcheck[i].checked){
                        oldTotal = oldTotal + +olditems[i].value;
                    }
                }
                oldTotal = oldTotal - +oldDiscount;
            }


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
            subTotal = subTotal + +oldTotal;

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

            document.getElementById("Hidden_Price").value = Total;

        }// end of function Calculate Subtotal()

        
        //Enables the number input for a discount after a radio option is selected 
        function DiscountState(){
            document.getElementById("discount_amount").disabled = false;
        }
        

        /**
         * adds a new item text box, item price box, and a delete checkbox every time a 
         * button is pressed. 
         */
        function addItemBox(){
 
            // create a textbox with a unique name
            var textbox = document.createElement("input");
            textbox.type = "text";
            textbox.name = "new_item_" + itemCounter;
            textbox.placeholder="Item Name";
            textbox.setAttribute("maxlength","20");


            // add the textbox to the modal 
            var container = document.getElementById("LineItemTextBoxes");
            container.appendChild(textbox);


            // create a number box with a unique name
            var numbox = document.createElement("input");
            numbox.type = "number";
            numbox.requred = "true";
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


    </script>

</html>

