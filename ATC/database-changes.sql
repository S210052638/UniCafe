Database changes:

   -- shopping_cart table
CREATE TABLE shopping_cart (
  cartID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id int(11) NOT NULL,
  total_price decimal(10,2) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users (userID) ON DELETE CASCADE ON UPDATE CASCADE
);

-- order_items table
CREATE TABLE order_items (
  order_item_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  cart_id int(11) NOT NULL,
  menu_item_id int(11) NOT NULL,
  quantity int(11) NOT NULL,
  price decimal(10,2) NOT NULL,
  FOREIGN KEY (cart_id) REFERENCES shopping_cart (cartID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (menu_item_id) REFERENCES menu_items (productID) ON DELETE CASCADE ON UPDATE CASCADE
);

-- orders table
CREATE TABLE orders (
  orderID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id int(11) NOT NULL,
  total_price decimal(10,2) NOT NULL,
  status varchar(30) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users (userID) ON DELETE CASCADE ON UPDATE CASCADE
);