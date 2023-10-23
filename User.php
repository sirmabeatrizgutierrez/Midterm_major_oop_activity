<?php
require 'DB_Config.php';
class User {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function registerUser($username, $password, $email) {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the Users table
        $insertQuery = "INSERT INTO users (Username, Password, Email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);

        if (!$stmt) {
            return false; // Failed to prepare the query
        }

        // Bind parameters and execute the query
        $stmt->bind_param("sss", $username, $hashedPassword, $email);

        if ($stmt->execute()) {
            return true; // Registration successful
        } else {
            return false; // Registration failed
        }
    }
}
?>

<?php
class Product {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM Products";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            return $products;
        } else {
            return array();
        }
    }

    public function insertProduct($product_name, $price, $stock_quantity) {
        $query = "INSERT INTO Products (Product_Name, Price, Stock_Quantity) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sdi", $product_name, $price, $stock_quantity);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
    }
}
?>
<?php
class Cart {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function addItemToCart($user_id, $product_id, $quantity) {
        // Check if the same product is already in the cart for the user
        $query = "SELECT * FROM Carts WHERE User_ID = ? AND Product_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product already in the cart, update quantity
            $query = "UPDATE Carts SET Quantity = Quantity + ? WHERE User_ID = ? AND Product_ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        } else {
            // Product not in the cart, insert a new record
            $query = "INSERT INTO Carts (User_ID, Product_ID, Quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        if ($stmt->execute()) {
            return true; // Item added to the cart successfully
        } else {
            return false; // Item insertion or update failed
        }

        $stmt->close();
    }

    public function viewCart($user_id) {
        $query = "SELECT Carts.Cart_ID, Products.Product_Name, Carts.Quantity, Products.Price, (Carts.Quantity * Products.Price) AS Total_Price
                  FROM Carts
                  INNER JOIN Products ON Carts.Product_ID = Products.Product_ID
                  WHERE Carts.User_ID = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $cartItems = array();
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }

        return $cartItems;
    }

    public function clearCart($user_id) {
        $query = "DELETE FROM Carts WHERE User_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            return true; // Cart cleared successfully
        } else {
            return false; // Cart clearance failed
        }

        $stmt->close();
    }
}
?>
<?php
class Order {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Create a new order record
    public function createOrder($user_id, $cart_id, $total_fee) {
        $query = "INSERT INTO Orders (User_ID, Cart_ID, Total_Fee, Order_Date) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iid", $user_id, $cart_id, $total_fee);

        if ($stmt->execute()) {
            return true; // Order creation was successful
        } else {
            return false; // Order creation failed
        }

        $stmt->close();
    }

    // Retrieve orders for a given user
    public function getOrdersByUser($user_id) {
        $query = "SELECT * FROM Orders WHERE User_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }
}
?>
