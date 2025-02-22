<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database configuration
$dbHost = 'localhost:8889';
$dbName = 'cafeteria';
$dbUsername = 'root';
$dbPassword = 'root';

// Create connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
$user_id = $_SESSION['user_id'];

// Check if product exists
$productCheckQuery = "SELECT productID FROM menu_items WHERE productID = ?";
$stmt = $conn->prepare($productCheckQuery);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Check if item exists in cart
    $cartCheckQuery = "SELECT cartID FROM shopping_cart WHERE user_id = ?";
    $cartStmt = $conn->prepare($cartCheckQuery);
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartStmt->store_result();
    
    if ($cartStmt->num_rows > 0) {
        $cartResult = $cartStmt->get_result();
        $cartRow = $cartResult->fetch_assoc();
        $cartId = $cartRow['cartID'];
        
        // Update quantity if item already exists in order_items
        $updateQuery = "SELECT order_item_id FROM order_items 
                       WHERE order_id = ? AND menu_item_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $cartId, $product_id);
        $updateStmt->execute();
        $updateStmt->store_result();
        
        if ($updateStmt->num_rows > 0) {
            $updateStmt->bind_result($existingId);
            $updateStmt->fetch();
            
            $updateQty = "UPDATE order_items 
                         SET quantity = quantity + 1
                         WHERE order_item_id = ?";
            $qtyStmt = $conn->prepare($updateQty);
            $qtyStmt->bind_param("i", $existingId);
            $qtyStmt->execute();
        } else {
            // Add new item
            $insertQuery = "INSERT INTO order_items 
                           (order_id, menu_item_id, quantity, price)
                           VALUES (?, ?, 1, 
                                   (SELECT price FROM menu_items WHERE productID = ?))";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $cartId, $product_id, $product_id);
            $insertStmt->execute();
        }
    } else {
        // Create new cart and add item
        $createCartQuery = "INSERT INTO shopping_cart (user_id, total_price) 
                           VALUES (?, 0)";
        $createCartStmt = $conn->prepare($createCartQuery);
        $createCartStmt->bind_param("i", $user_id);
        $createCartStmt->execute();
        
        $cartId = $conn->insert_id;
        
        $insertQuery = "INSERT INTO order_items 
                       (order_id, menu_item_id, quantity, price)
                       VALUES (?, ?, 1, 
                               (SELECT price FROM menu_items WHERE productID = ?))";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iii", $cartId, $product_id, $product_id);
        $insertStmt->execute();
    }
    echo json_encode(['status' => 'success', 'message' => 'Item added to cart!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
}

$conn->close();
?>