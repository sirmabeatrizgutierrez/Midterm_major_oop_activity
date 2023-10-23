<?php
require 'user.php';

// Initialize the DBConfig class to establish a database connection
$dbConfig = new DBConfig();
$connection = $dbConfig->getConnection();
$product = new Product($connection);
$cart = new Cart($connection);

// Assume you have a method or variable to get the User_ID of the currently logged-in user.
// Replace this with your actual method or variable to obtain the User_ID.
// Example: $user_id = $_SESSION['user_id'];
$user_id = 1; // Replace with your actual User_ID retrieval code.

// Initialize the total quantity to zero
$totalQuantity = 0;

// Process the form submission to add items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Set the desired quantity

    // Add the selected product to the cart and update the total quantity
    if ($cart->addItemToCart($user_id, $product_id, $quantity)) {
        echo "Product added to cart successfully.";
        $totalQuantity += $quantity;
    } else {
        echo "Failed to add the product to the cart.";
    }
}

$products = $product->getAllProducts();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
</head>
<body>
    <h2>Product List</h2>

    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Stock Quantity</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product) { ?>
        <tr>
            <td><?php echo $product['Product_Name']; ?></td>
            <td><?php echo $product['Price']; ?></td>
            <td><?php echo $product['Stock_Quantity']; ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                    <input type="submit" name="add_to_cart" value="Add to Cart">
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    <!-- Add a link to the shopping cart -->
    <a href="checkout.php">View Shopping Cart</a>
</body>
</html>
