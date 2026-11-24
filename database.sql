-- Restaurant Management & Online Food Ordering System Database
-- Database: restaurant_db

CREATE DATABASE IF NOT EXISTS `restaurant_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `restaurant_db`;

-- Drop existing tables if re-importing
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `foods`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `settings`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(30) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Foods Table
CREATE TABLE `foods` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ingredients` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image` VARCHAR(255) DEFAULT NULL,
  `rating` DECIMAL(3,1) NOT NULL DEFAULT 4.5,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_popular` TINYINT(1) NOT NULL DEFAULT 0,
  `is_special` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_foods_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Orders Table
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `delivery_address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `delivery_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Cash on Delivery',
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `order_status` ENUM('Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Order Items Table
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `food_id` INT DEFAULT NULL,
  `food_name` VARCHAR(150) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Reservations Table
CREATE TABLE `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `reservation_date` DATE NOT NULL,
  `reservation_time` TIME NOT NULL,
  `guests` INT NOT NULL DEFAULT 2,
  `special_request` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Reviews Table
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `food_id` INT DEFAULT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `review` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Contacts Table
CREATE TABLE `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Settings Table
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `restaurant_name` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `whatsapp` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `opening_hours` VARCHAR(255) DEFAULT NULL,
  `delivery_fee` DECIMAL(10,2) NOT NULL DEFAULT 150.00,
  `facebook` VARCHAR(255) DEFAULT NULL,
  `instagram` VARCHAR(255) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `currency_symbol` VARCHAR(10) NOT NULL DEFAULT 'Rs. '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED DATA

-- Settings
INSERT INTO `settings` (`id`, `restaurant_name`, `logo`, `phone`, `email`, `whatsapp`, `address`, `opening_hours`, `delivery_fee`, `facebook`, `instagram`, `website`, `currency_symbol`) VALUES
(1, 'Saffron & Spice Gourmet Restaurant', 'logo.png', '+92 300 1234567', 'info@saffronspice.com', '923001234567', 'Plot 42, Food Street, Block 7, Clifton, Karachi, Pakistan', 'Mon - Sun: 12:00 PM - 01:00 AM', 150.00, 'https://facebook.com/saffronspice', 'https://instagram.com/saffronspice', 'http://localhost/MS-RESTURANT', 'Rs. ');

-- Users (Password: admin123 for admin, user123 for ahmed)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `address`, `role`, `status`) VALUES
(1, 'Restaurant Admin', 'admin@restaurant.com', '+92 300 0000000', '$2y$10$u3FsVr7FbgepOsKxqkhmOu/E4lkTzXpKQv/5XqvIzPx4/M0IARMTe', 'Restaurant Management Office, Karachi', 'admin', 'active'),
(2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', '$2y$10$VEh.2608HAqbYCiBSAIq..Qi4rmhoLL5C.oIzeuQaajb0Cnu79CTa', 'House 14-B, Sector 11-A, North Karachi', 'customer', 'active'),
(3, 'Fatima Noor', 'fatima@example.com', '03129876543', '$2y$10$VEh.2608HAqbYCiBSAIq..Qi4rmhoLL5C.oIzeuQaajb0Cnu79CTa', 'Apartment 402, Gulshan-e-Iqbal Block 13, Karachi', 'customer', 'active');

-- Categories
INSERT INTO `categories` (`id`, `name`, `description`, `image`, `status`) VALUES
(1, 'Starters', 'Appetizing soups, crispy bites, and mouth-watering starters to kick off your feast.', 'cat-starters.jpg', 'active'),
(2, 'BBQ', 'Tender, chargrilled meats infused with authentic aromatic spices and smoky herbs.', 'cat-bbq.jpg', 'active'),
(3, 'Burgers', 'Gourmet artisanal patties grilled to perfection with melting cheese and secret sauces.', 'cat-burgers.jpg', 'active'),
(4, 'Pizza', 'Wood-fired crispy dough crowned with rich marinara, melted mozzarella, and fresh toppings.', 'cat-pizza.jpg', 'active'),
(5, 'Chinese', 'Wok-tossed delicacies featuring sizzling noodles, savory gravies, and fragrant fried rice.', 'cat-chinese.jpg', 'active'),
(6, 'Pakistani', 'Classic royal heritage curries, fragrant biryanis, and succulent karahi specials.', 'cat-pakistani.jpg', 'active'),
(7, 'Fast Food', 'Crispy loaded platters, golden fries, crunchy wings, and juicy wraps.', 'cat-fastfood.jpg', 'active'),
(8, 'Drinks', 'Freshly brewed mocktails, chilled shakes, mint lemonades, and traditional coolers.', 'cat-drinks.jpg', 'active'),
(9, 'Desserts', 'Decadent sweet endings: warm molten lava cakes, royal kheer, and artisan cheesecakes.', 'cat-desserts.jpg', 'active');

-- Foods
INSERT INTO `foods` (`id`, `category_id`, `name`, `description`, `ingredients`, `price`, `image`, `rating`, `is_featured`, `is_popular`, `is_special`, `status`) VALUES
-- Starters
(1, 1, 'Dynamite Prawns', 'Crispy fried prawns tossed in our signature spicy and creamy dynamite dressing.', 'King Prawns, Paprika, Sriracha, Mayo, Garlic, Spring Onions', 1150.00, 'dynamite-prawns.jpg', 4.9, 1, 1, 0, 'active'),
(2, 1, 'Stuffed Chicken Strips', 'Golden fried chicken tenders stuffed with melted cheddar and jalapeño bits.', 'Chicken Breast, Cheddar Cheese, Jalapeños, Breadcrumbs, Herbs', 750.00, 'stuffed-strips.jpg', 4.7, 0, 1, 0, 'active'),
(3, 1, 'Hot & Sour Soup', 'Rich and hearty soup loaded with shredded chicken, black mushrooms, egg ribbons, and bamboo shoots.', 'Chicken Broth, Shredded Chicken, Wood Ear Mushroom, Egg, Vinegar, Chili Oil', 550.00, 'hot-sour-soup.jpg', 4.6, 0, 0, 0, 'active'),

-- BBQ
(4, 2, 'Chicken Malai Boti', 'Tender boneless chicken cubes marinated in heavy cream, green chilies, and ground cardamom.', 'Boneless Chicken, Fresh Cream, Yogurt, Cardamom, White Pepper, Green Chili', 890.00, 'malai-boti.jpg', 4.9, 1, 1, 1, 'active'),
(5, 2, 'Beef Bihari Kabab', 'Mouth-melting slivers of beef marinated with raw papaya and authentic mustard oil.', 'Prime Beef, Papaya Paste, Fried Onions, Mustard Oil, Garam Masala', 950.00, 'bihari-kabab.jpg', 4.8, 1, 1, 0, 'active'),
(6, 2, 'Mutton Chops Grill', 'Succulent lamb/mutton ribs marinated overnight in ginger garlic and roasted cumin.', 'Mutton Ribs, Cumin, Coriander, Lemon, Ginger-Garlic paste, Kashmiri Chili', 1650.00, 'mutton-chops.jpg', 4.9, 0, 0, 1, 'active'),

-- Burgers
(7, 3, 'Signature Gourmet Smash Burger', 'Double smashed beef patties, aged cheddar cheese, caramelized onions, pickles, and truffle mayo on a brioche bun.', 'Prime Beef Patty, Cheddar Cheese, Brioche Bun, Truffle Mayo, Pickles, Grilled Onions', 890.00, 'smash-burger.jpg', 4.9, 1, 1, 1, 'active'),
(8, 3, 'Crispy Zinger Crunch', 'Golden batter-fried chicken thigh glazed with spicy peri peri sauce and chilled slaw.', 'Crispy Chicken Thigh, Peri Peri Glaze, Coleslaw, Sesame Brioche Bun, Cheese Slice', 650.00, 'zinger-burger.jpg', 4.8, 0, 1, 0, 'active'),
(9, 3, 'Smokey BBQ Beef Burger', 'Chargrilled beef patty with crispy turkey bacon, smoked gouda, and hickory BBQ sauce.', 'Beef Patty, Turkey Bacon, Smoked Gouda, Hickory BBQ Sauce, Crispy Onions', 980.00, 'bbq-burger.jpg', 4.7, 0, 0, 0, 'active'),

-- Pizza
(10, 4, 'Royal Chicken Tikka Pizza', 'Tender chunks of spicy chicken tikka, red onions, bell peppers, and bubbling mozzarella.', 'Pizza Dough, Pizza Sauce, Mozzarella, Chicken Tikka, Bell Peppers, Red Onion, Oregano', 1450.00, 'pizza-tikka.jpg', 4.8, 1, 1, 0, 'active'),
(11, 4, 'Smoked Pepperoni Feast', 'Artisanal crust topped with double beef pepperoni, zesty marinara sauce, and fresh basil.', 'Crispy Thin Crust, Beef Pepperoni, Mozzarella, Marinara, Parmesan, Fresh Basil', 1590.00, 'pizza-pepperoni.jpg', 4.9, 0, 1, 1, 'active'),
(12, 4, 'Creamy Ranch Supreme', 'Grilled chicken, sliced mushrooms, jalapeños, and drizzle of fresh herbed ranch dressing.', 'Hand Tossed Crust, White Ranch Base, Roasted Chicken, Mushrooms, Jalapeños, Mozzarella', 1500.00, 'pizza-ranch.jpg', 4.6, 0, 0, 0, 'active'),

-- Chinese
(13, 5, 'Kung Pao Chicken', 'Classic Sichuan wok tossed chicken with dried red chilies, roasted peanuts, and spring onions.', 'Chicken Cubes, Roasted Peanuts, Szechuan Chili, Bell Peppers, Soy-Vinegar Reduction', 880.00, 'kung-pao.jpg', 4.8, 0, 1, 0, 'active'),
(14, 5, 'Egg Fried Rice Platter', 'Fragrant jasmine rice wok-fried with fluffy scrambled eggs, spring scallions, and sesame oil.', 'Jasmine Rice, Eggs, Carrots, Peas, Spring Onion, Sesame Oil, Light Soy', 520.00, 'fried-rice.jpg', 4.7, 0, 0, 0, 'active'),
(15, 5, 'Beef Chilli Dry', 'Crispy strips of prime beef wok-fried with spicy green chilies, ginger, and savory garlic sauce.', 'Prime Beef Strips, Thai Green Chilies, Fresh Ginger, Garlic, Dark Soy Sauce', 990.00, 'beef-chilli.jpg', 4.9, 1, 0, 1, 'active'),

-- Pakistani
(16, 6, 'Royal Mutton Dum Biryani', 'Aromatic basmati rice cooked on slow flame (dum) with tender mutton chunks and saffron spices.', 'Aged Basmati Rice, Mutton, Saffron Milk, Mint, Brown Onions, Royal Spices', 1190.00, 'mutton-biryani.jpg', 5.0, 1, 1, 1, 'active'),
(17, 6, 'Desi Chicken Shinwari Karahi', 'Cooked in pure animal fat and butter with fresh tomatoes, green chilies, and crushed black pepper.', 'Fresh Chicken, Vine Tomatoes, Green Chilies, Ginger Juliennes, Pure Desi Butter', 1350.00, 'chicken-karahi.jpg', 4.9, 1, 1, 0, 'active'),
(18, 6, 'Special Garlic Roghani Naan', 'Freshly baked tandoori flatbread brushed with garlic butter, sesame seeds, and fresh coriander.', 'Refined Flour, Milk, Butter, Garlic, Sesame Seeds, Coriander', 120.00, 'roghani-naan.jpg', 4.8, 0, 0, 0, 'active'),

-- Fast Food
(19, 7, 'Loaded Volcano Fries', 'Crinkle cut golden fries smothered in cheese sauce, diced grilled chicken, jalapeños, and bacon bits.', 'Potato Fries, Cheddar Cheese Sauce, Grilled Chicken, Jalapeños, Crispy Bits', 580.00, 'loaded-fries.jpg', 4.8, 0, 1, 0, 'active'),
(20, 7, 'Crispy Buffalo Wings (8 Pcs)', 'Deep fried chicken wings tossed in tangy cayenne pepper buffalo sauce with blue cheese dip.', 'Chicken Wings, Cayenne Hot Sauce, Butter, Vinegar, Blue Cheese Dip', 690.00, 'buffalo-wings.jpg', 4.7, 0, 1, 0, 'active'),

-- Drinks
(21, 8, 'Fresh Mint Lemonade Cooler', 'Chilled crushed ice beverage with fresh garden mint leaves, lime juice, and soda water.', 'Garden Mint, Fresh Lime, Rock Salt, Soda Water, Crushed Ice, Sugar Cane Syrup', 280.00, 'mint-lemonade.jpg', 4.9, 0, 1, 0, 'active'),
(22, 8, 'Nutella Brownie Thick Shake', 'Rich creamy blend of whole milk, Nutella spread, fudge brownies, and chocolate drizzle.', 'Nutella, Vanilla Ice Cream, Fudge Brownie, Full Cream Milk, Whipped Cream', 490.00, 'nutella-shake.jpg', 4.9, 1, 1, 0, 'active'),

-- Desserts
(23, 9, 'Molten Chocolate Lava Cake', 'Warm chocolate sponge filled with rich molten chocolate center, served with vanilla bean ice cream.', 'Belgian Dark Chocolate, Butter, Farm Eggs, Flour, Vanilla Bean Gelato', 620.00, 'lava-cake.jpg', 5.0, 1, 1, 1, 'active'),
(24, 9, 'Royal Shahi Kheer', 'Traditional slow-cooked fragrant rice pudding garnished with roasted pistachios, almonds, and silver vark.', 'Full Fat Milk, Broken Rice, Cardamom, Pistachios, Almonds, Saffron', 380.00, 'shahi-kheer.jpg', 4.8, 0, 0, 0, 'active');

-- Orders & Order Items
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `delivery_address`, `city`, `notes`, `total_amount`, `delivery_fee`, `payment_method`, `payment_status`, `order_status`, `created_at`) VALUES
(1, 'ORD-20261001-101', 2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', 'House 14-B, Sector 11-A, North Karachi', 'Karachi', 'Please provide extra mint sauce.', 1600.00, 150.00, 'Cash on Delivery', 'pending', 'Delivered', NOW() - INTERVAL 2 DAY),
(2, 'ORD-20261003-102', 3, 'Fatima Noor', 'fatima@example.com', '03129876543', 'Apartment 402, Gulshan-e-Iqbal Block 13, Karachi', 'Karachi', 'Ring doorbell twice.', 2340.00, 150.00, 'Cash on Delivery', 'pending', 'Preparing', NOW() - INTERVAL 1 HOUR);

INSERT INTO `order_items` (`id`, `order_id`, `food_id`, `food_name`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 7, 'Signature Gourmet Smash Burger', 1, 890.00, 890.00),
(2, 1, 19, 'Loaded Volcano Fries', 1, 580.00, 580.00),
(3, 2, 16, 'Royal Mutton Dum Biryani', 1, 1190.00, 1190.00),
(4, 2, 4, 'Chicken Malai Boti', 1, 890.00, 890.00),
(5, 2, 21, 'Fresh Mint Lemonade Cooler', 1, 280.00, 280.00);

-- Reservations
INSERT INTO `reservations` (`id`, `user_id`, `customer_name`, `email`, `phone`, `reservation_date`, `reservation_time`, `guests`, `special_request`, `status`, `created_at`) VALUES
(1, 2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', CURDATE() + INTERVAL 1 DAY, '20:00:00', 4, 'Family gathering, quiet window table requested.', 'Confirmed', NOW()),
(2, 3, 'Fatima Noor', 'fatima@example.com', '03129876543', CURDATE() + INTERVAL 2 DAY, '19:30:00', 2, 'Anniversary surprise setup if possible.', 'Pending', NOW());

-- Reviews
INSERT INTO `reviews` (`id`, `user_id`, `food_id`, `rating`, `review`, `status`, `created_at`) VALUES
(1, 2, 7, 5, 'The Signature Gourmet Smash Burger is simply out of this world! Juicy, perfectly seasoned, and the truffle mayo adds a luxurious touch. Highly recommended!', 'approved', NOW() - INTERVAL 2 DAY),
(2, 3, 16, 5, 'Royal Mutton Dum Biryani reminded me of pure traditional royal feasts. Fragrant basmati and tender meat. Fast delivery as well!', 'approved', NOW() - INTERVAL 1 DAY),
(3, 2, 4, 5, 'Chicken Malai Boti was creamy and melted right away. Excellent quality and presentation.', 'approved', NOW() - INTERVAL 12 HOUR);

-- Contact Messages
INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Zubair Tariq', 'zubair@example.com', '03331122334', 'Catering Inquiry for Corporate Dinner', 'Hello, we would like to inquire about your catering packages for an upcoming corporate event of 50 people.', 'unread', NOW() - INTERVAL 3 HOUR);
