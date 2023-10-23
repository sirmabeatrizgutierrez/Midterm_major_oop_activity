<?php
require 'user.php'; // Include the database configuration with DBConfig class

// Initialize the DBConfig class to establish a database connection
$dbConfig = new DBConfig();
$connection = $dbConfig->getConnection();

// Create an instance of the Product class
$product = new Product($connection);

// Handle product insertion form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    // Implement the insertProduct method to insert a new product
    $productInserted = $product->insertProduct($product_name, $price, $stock_quantity);

    if ($productInserted) {
        echo "Product inserted successfully.";
    } else {
        echo "Product insertion failed.";
    }
}

// Retrieve all products
$products = $product->getAllProducts();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
</head>
<body>
    <h2>Product List</h2>

    <?php
    if (count($products) > 0) {
        echo "<table border=1>";
        echo "<tr><th>Product Name</th><th>Price</th><th>Stock Quantity</th></tr>";

        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['Product_Name'] . "</td>";
            echo "<td>" . $product['Price'] . "</td>";
            echo "<td>" . $product['Stock_Quantity'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No products available.";
    }
    ?>

    <h2>Insert New Product</h2>
    <form method="post">
        Product Name: <input type="text" name="product_name" required><br><br>
        Price: <input type="number" name="price" required><br><br>
        Stock Quantity: <input type="number" name="stock_quantity" required><br><br>
        <input type="submit" value="Insert Product">
    </form>
</body>
</html>
