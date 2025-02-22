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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get products
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Caf - Product Listing</title>  
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>University Caf</h1>
            <h2>Food & Beverage</h2>
        </div>

        <!-- Categories -->
        <div class="product-browsing">
            <h3>Product Browsing</h3>
            <div class="product-categories">
                <a href="#" class="category" data-category="all">All</a>
                <a href="#" class="category" data-category="drinks">Drinks</a>
                <a href="#" class="category" data-category="desserts">Desserts</a>
                <a href="#" class="category" data-category="meals">Meals</a>
                <a href="#" class="category" data-category="others">Others</a>
            </div>
            <!-- Search -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search products...">
                <button type="button" id="searchButton">Search</button>
            </div>
        </div>

        <!---------------Product Grid------------->
        <div class="product-grid" id="productGrid">
            <?php
                while($row = $result->fetch_assoc()) {
                    echo '
                        <div class="product-card" data-category="' . $row["category"] . '">
                            <img src="pic/' . $row["image"] . '" alt="' . $row["name"] . '">
                            <h3>' . $row["name"] . '</h3>
                            <p class="product-description">' . $row["description"] . '</p>
                            <p class="price">SAR ' . $row["price"] . '</p>
                            <form method="POST" id="cart_form_'.$row["productID"].'">
                                <input type="hidden" name="product_id" value="'.$row["productID"].'">
                                <button type="button" onclick="addToCart('.$row["productID"].')">Add to Cart</button>
                            </form>
                        </div>';
                }
                $result->close();
                $conn->close();
            ?>
        </div>
    </div>

   <!-- JavaScript -->
    <script>
        // Function to resize images in the product grid
        function resizeImages() {
            const productCards = document.querySelectorAll('.product-card');
            const targetWidth = 200; // Set your desired width
            const targetHeight = 200; // Set your desired height

            productCards.forEach(card => {
                const img = card.querySelector('img');
                // Calculate the aspect ratio
                const aspectRatio = img.naturalWidth / img.naturalHeight;
                // Add event listener for image load to ensure dimensions are correct
                img.addEventListener('load', function() {
                    if (this.naturalWidth > targetWidth || this.naturalHeight > targetHeight) {
                        // Adjust dimensions while maintaining aspect ratio
                        if (aspectRatio > 1) {
                            this.width = targetWidth;
                            this.height = this.width / aspectRatio;
                            if (this.height > targetHeight) {
                                this.height = targetHeight;
                                this.width = this.height * aspectRatio;
                            }
                        } else {
                            this.height = targetHeight;
                            this.width = this.height * aspectRatio;
                            if (this.width > targetWidth) {
                                this.width = targetWidth;
                                this.height = this.width / aspectRatio;
                            }
                        }
                    }
                });
            });
        }

        // Event listener to trigger image resizing after DOM content loads
        document.addEventListener('DOMContentLoaded', resizeImages);

        // Event listener for window resize to adjust images when the window size changes
        window.addEventListener('resize', resizeImages);

        // Event listeners for category filtering
        document.querySelectorAll('.category').forEach(category => {
            category.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedCategory = category.getAttribute('data-category');
                filterProducts(selectedCategory);
            });
        });

        // Event listener for search
        document.getElementById('searchButton').addEventListener('click', searchProducts);

        // Event listener for Add to Cart
        function addToCart(productId) {
            fetch(`add_to_cart.php?product_id=${productId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding to cart.');
            });
        }

        // Filter products by category
        function filterProducts(category) {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                card.style.display = category === 'all' ? 'block' :
                                    cardCategory === category ? 'block' : 'none';
                // Trigger resize after filtering to ensure images adjust
                setTimeout(() => resizeImages(), 100);
            });
        }

        // Search products
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
                // Trigger resize after search to ensure images adjust
                setTimeout(() => resizeImages(), 100);
            });
        }
    </script>
</body>
</html>
