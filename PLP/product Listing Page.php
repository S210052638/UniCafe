<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Cafeteria - Product Listing</title>  
</head>
<body>
    <?php
        // Database configuration
        $dbHost = 'localhost:8889'; // MAMP port 8889
        $dbName = 'cafeteria';      // database name
        $dbUsername = 'root';       
        $dbPassword = 'root';       // MAMP password

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

    <div class="container">
        <div class="header">
            <h1>University Cafeteria</h1>
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
                <input type="text" placeholder="Search products..." id="searchInput">
                <button type="button" id="searchButton">Search</button>
            </div>
        </div>

        <!---------------Product Grid------------->
        <div class="product-grid" id="productGrid">
            <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '
                        <div class="product-card" data-category="' . $row["category"] . '">
                            <img src="pic/' . $row["image"] . '" alt="' . $row["name"] . '" class="product-image">
                            <h3>' . $row["name"] . '</h3>
                            <p class="product-description">' . $row["description"] . '</p>
                            <p class="price">SAR ' . $row["price"] . '</p>
                            <a href="Product Details Page.html?product=' . strtolower(str_replace(' ', '-', $row["name"])) . '" class="more-info">More Info</a>
                        </div>';
                    }
                } else {
                    echo "No products found.";
                }
            ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
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

        // Filter products by category
        function filterProducts(category) {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                card.style.display = category === 'all' ? 'block' : 
                                    cardCategory === category ? 'block' : 'none';
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
            });
        }
    </script>

    <?php
        // Close database connection
        $conn->close();
    ?>
</body>
</html>