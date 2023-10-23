<?php
require 'user.php'; // Include your user.php file for database configuration

session_start();

$user_id = 1; // Replace with your actual user identification mechanism

$dbConfig = new DBConfig();
$connection = $dbConfig->getConnection();
$cart = new Cart($connection); // Define $cart here

$checkout_message = '';
$totalCost = 0; // Initialize totalCost

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_cart'])) {
        // Clear the cart
        if ($cart->clearCart($user_id)) {
            echo "Your cart has been cleared.";
        } else {
            echo "Failed to clear the cart.";
        }
    } elseif (isset($_POST['checkout'])) {
        // Start a database transaction for checkout
        $connection->begin_transaction();

        try {
            // Compute the total cost of products in the cart
            $cartItems = $cart->viewCart($user_id);

            foreach ($cartItems as $item) {
                $totalCost += $item['Total_Price'];
            }

            // Add the delivery fee
            $deliveryFee = 50;
            $totalCost += $deliveryFee;

            // Insert the order into the "Orders" table without specifying the Cart_ID
            $insertOrderQuery = "INSERT INTO Orders (User_ID, Total_Fee, Order_Date) VALUES (?, ?, NOW())";

            $stmt = $connection->prepare($insertOrderQuery);
            $stmt->bind_param("id", $user_id, $totalCost);
            $stmt->execute();

            // Commit the transaction
            $connection->commit();

            $checkout_message = "Order has been successfully placed.";
        } catch (Exception $e) {
            // Rollback the transaction in case of an exception
            $connection->rollback();
            echo "Transaction failed: " . $e->getMessage();
        }
    }
}

$cartItems = $cart->viewCart($user_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <h2>Checkout</h2>
    <?php
    if (count($cartItems) > 0) {
        $totalCost = 0; // Initialize totalCost
        $orderDate = date('Y-m-d H:i:s'); // Get the current date and time

        echo "<form method='post' action='checkout.php'>";
        echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
        foreach ($cartItems as $item) {
            echo "<input type='hidden' name='cart_items[]' value='" . json_encode($item) . "'>";
        }
        echo "<table border='1'>";
        echo "<tr><th>Product Name</th><th>Price</th><th>Quantity</th><th>Total Price</th></tr>";
        foreach ($cartItems as $item) {
            echo "<tr>";
            echo "<td>" . $item['Product_Name'] . "</td>";
            echo "<td>" . $item['Price'] . "</td>";
            echo "<td>" . $item['Quantity'] . "</td>";
            echo "<td>" . $item['Total_Price'] . "</td>";
            $totalCost += $item['Total_Price']; // Add to total cost
            echo "</tr>";
        }

        $deliveryFee = 50;
        $totalCost += $deliveryFee; // Add the delivery fee

        echo "<tr>";
        echo "<td colspan='3'>Delivery Fee:</td>";
        echo "<td>50</td>"; // Display the delivery fee
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan='3'>Total Cost (Including delivery fee):</td>";
        echo "<td>" . $totalCost . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan='3'>Order Date:</td>";
        echo "<td>" . $orderDate . "</td>"; // Display the order date
        echo "</tr>";
        echo "</table>";

        if (empty($checkout_message)) {
            echo "<input type='submit' name='clear_cart' value='Clear Cart'>";
            echo "<input type='submit' name='checkout' value='Checkout'>";
        } else {
            echo "<p>$checkout_message</p>";
        }
    } else {
        echo "Your cart is empty.";
        echo "<a href='add_cart.php'>Add items to the cart</a>";
    }
  
    ?>
</body>
</html>
