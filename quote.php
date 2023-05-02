<!DOCTYPE html>
<html>
    <script>
        function showAddItem() {
            var newItem = document.getElementById("newItem");
            var addItem = document.getElementById("addItem");
            addItem.style.display = "block";
            newItem.style.display = "none";
        }

        function showEditItem(itemId) {
            var editItem = document.getElementById("editItem" + itemId);
            var saveItem = document.getElementById("saveItem" + itemId);
            editItem.style.display = "none";
            saveItem.style.display = "inline";
        }

        function showEditNote(noteId) {
            var editNote = document.getElementsByClassName("editNote" + noteId);
            var saveNote = document.getElementById("saveNote" + noteId);
            for (var i = 0; i < editNote.length; i++) {
                editNote[i].style.display = "none";
            }
            saveNote.style.display = "inline";
        }
    </script>
</html>

<?php
    session_start();

    include('config.php');

    echo '<style>
            input {
                padding:5px; color:#5b5b5b; width:150px; border:1px solid #9a9a9a;
                margin-top: 10px; width: 120px;
            }

            input[type="radio"] {
                width: 40px; margin: 0px;
            }

            label[for="percentage"] {
                margin-right: 10px;
            }

            input[type="submit"]:hover {
                background-color:lightgrey;
            }

            button {
                padding:5px; color:#5b5b5b; width:150px; border:1px solid #9a9a9a;
                margin-top: 10px; width: 120px;
            }
            
            button:hover {
                background-color:lightgrey;
            }

            label {
                font-family: sans-serif;
            }

            p1 {
                font-family: sans-serif;
                display:inline-block;
            }

            p {
                font-family: sans-serif;
            }
            
            h1 {
                font-family: sans-serif;
            }

            h3 {
                font-family: sans-serif;
            }
            
        </style>';

    // Display the line items for the quote selected
    $sql = "SELECT * FROM Quote_Item WHERE foreign_quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));

    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

    $total = 0;
    echo '<form action=hq.php method=POST><button style="width: 200px;">BACK</button></form></br>';
    echo '<h1>Quote #'.$_SESSION['QUOTE_ID'].'</h1>';
    foreach($rows as $row) {
        $sql = "SELECT * FROM Item WHERE item_id = ".$row['foreign_item_id'].";";
        $item_id = $row['foreign_item_id'];

        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();
        $lineItem = $prepared->fetch();
        $price = $lineItem['price'];

        global $total;
        $total += $price;

        echo '<p1><tr>';
        echo '<td> Item Name: '.$lineItem['item_name'].'</td>';
        echo '<td> ---  Price: $'.number_format($price, 2, '.', '').'</td>';
        echo '</tr>';
        // vvv EDIT ITEM FORM vvv
        echo '<form id="saveItem'.$item_id.'" action="edit_item.php" method="POST" style="display: none;">';
        echo ' ---- ';
        echo '<input placeholder="Item Name:" name="name" value="'.$lineItem['item_name'].'" required>';
        echo '<input placeholder="Item Price:" name="price" value='.number_format($price, 2, '.', '').' required>';
        echo '<input type="submit" name="'.$item_id.'" value="Save Changes">';
        echo '</form></p1>';
        // ^^^^^^^^^^^^^^^^^^^^^^
        $sql = "SELECT * FROM Quote WHERE quote_id = ?;";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
        $quote = $prepared->fetch();
        if($quote['status'] == 'Finalized') {
            echo ' ---- ';
            echo '<button id="editItem'.$item_id.'" onclick="showEditItem(\''.$item_id.'\')">Edit</button>';
            echo '<form action="delete_item.php" method="POST" style="display: inline;">';
            echo '<input type="submit" name="'.$item_id.'" value="Delete">';
            echo '</form>';
        }
        echo '</br>';
    }

    $sql = "SELECT * FROM Quote WHERE quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $quote = $prepared->fetch();

    // Add new line items
    if($quote['status'] == 'Finalized') {
        echo '<button id="newItem" onclick="showAddItem()">New Item</button>';
        echo '<form id="addItem" action="add_item.php" method="POST" style="display: none;">';
        echo '<input placeholder="Item Name:" name="name" required>';
        echo '<input type="number" step=".01" placeholder="Item Price:" name="price" min=0 max=99999.99 required>';
        echo '<input type="submit" value="Add Item">';
        echo '</form>';
    }

    // Set quote price to 0 if it is less than 1
    $price = $quote['price'];
    if($price < 0) {
        $sql = "UPDATE Quote SET price=? WHERE quote_id=?;";
        $prepared = $db1->prepare($sql);
        $prepared->execute(array(0, $_SESSION['QUOTE_ID']));
    }

    echo '<hr>';
    echo '<h3>Total Quote Price:      $'.number_format($quote['price'], 2, '.', '').'</h3>';
    echo '<form action="apply_discount.php" method="POST">';
    echo '<input placeholder="Discount"" name="discount" required>';
    echo '<input type="radio" id="amount" name="discount_type" value="amount" checked>';
    echo '<label for="amount">Dollar</label>';
    echo '<input type="radio" id="percentage" name="discount_type" value="percentage">';
    echo '<label for="percentage">Percent</label>';

    echo '<input type="submit">';
    echo '</form>';

    echo '<hr>';

    // Display Notes
    $sql = "SELECT * FROM Quote_Note WHERE foreign_quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));

    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
    echo '<p><strong>Notes:</p>';
    foreach($rows as $row) {
        $sql = "SELECT * FROM Note WHERE note_id = ".$row['foreign_note_id'].";";
        $prepared = $db1->prepare($sql);
        $success = $prepared->execute();
        $note = $prepared->fetch();
        $note_id = $row['foreign_note_id'];
        echo '<form style="display: inline;">';
        echo '<input class="editNote'.$note_id.'" value="'.$note['text_field'].'" disabled>';
        echo '</form>';
        // vvv EDIT NOTE FORM vvv
        echo '<form id="saveNote'.$note_id.'" action="edit_note.php" method="POST" style="display: none;">';
        echo '<input placeholder="Note:" name="note" value="'.$note['text_field'].'">';
        echo '<input type="submit" name="'.$note_id.'" value="Save Changes">';
        echo '</form>';
        // ^^^^^^^^^^^^^^^^^^^^^^
        echo '<button type="button" class="editNote'.$note_id.'" onclick="return showEditNote(\''.$note_id.'\')">Edit</button>';
        echo '<form action="delete_note.php" method="POST" style="display: inline;">';
        echo '<input id="'.$note_id.'" type="submit" name="'.$note_id.'" value="Delete">';
        echo '</form>';
        echo '</br>';
    }
    echo '<form action="add_note.php" method="POST">';
    echo '<input name="note" maxlength="250">';
    echo "<input type='submit' name='save_note' value='Save New Note'>";
    echo '</form>';
    echo '<br><br><br><br>';

    // Sanction or process quote
    $sql = "SELECT * FROM Quote WHERE quote_id = ?;";
    $prepared = $db1->prepare($sql);
    $success = $prepared->execute(array($_SESSION['QUOTE_ID']));
    $quote = $prepared->fetch();

    if($quote['status'] == 'Finalized') {
        echo '<form action="sanction_quote.php" method="POST">';
        echo '<input type="submit" value="SANCTION QUOTE" style="color:red; width:200px">';
        echo '</form>';
    } else if($quote['status'] == 'Sanctioned') {
        echo '<form action="order_quote.php" method="POST">';
        echo '<input type="submit" value="FINALIZE ORDER" style="color:blue; width:200px">';
        echo '</form>';
    }
?>